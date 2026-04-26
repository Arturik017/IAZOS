<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SellerApprovedPasswordMail;
use App\Models\SellerApplication;
use App\Models\SellerPaymentAccount;
use App\Models\SellerProfile;
use App\Models\User;
use App\Services\SellerAccountDeletionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password as PasswordRule;

class SellerApplicationAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');

        $applications = SellerApplication::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('shop_name', 'like', "%{$q}%")
                        ->orWhere('legal_name', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('pickup_address', 'like', "%{$q}%");
                });
            })
            ->when(in_array($status, ['pending', 'approved', 'rejected'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => SellerApplication::count(),
            'pending' => SellerApplication::where('status', 'pending')->count(),
            'approved' => SellerApplication::where('status', 'approved')->count(),
            'rejected' => SellerApplication::where('status', 'rejected')->count(),
        ];

        return view('admin.seller_applications.index', compact(
            'applications',
            'q',
            'status',
            'stats'
        ));
    }

    public function approve(Request $request, $id)
    {
        $app = SellerApplication::findOrFail($id);

        if ($app->status === 'approved') {
            return back()->with('error', 'Cererea este deja aprobata.');
        }

        if ($app->status === 'rejected') {
            return back()->with('error', 'Cererea este deja respinsa.');
        }

        $data = $request->validate([
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $chosenPassword = $data['password'];

        $existingUser = User::where('email', $app->email)->first();

        if ($existingUser) {
            $user = $existingUser;
            $user->name = $app->name;
            $user->role = 'seller';
            $user->seller_status = 'approved';

            $user->password = Hash::make($chosenPassword);

            $user->save();
        } else {
            $user = User::create([
                'name' => $app->name,
                'email' => $app->email,
                'password' => Hash::make($chosenPassword),
                'role' => 'seller',
                'seller_status' => 'approved',
            ]);
        }

        $profile = SellerProfile::firstOrNew([
            'user_id' => $user->id,
        ]);

        $profile->shop_name = $app->shop_name;
        $profile->legal_name = $app->legal_name;
        $profile->phone = $app->phone;
        $profile->pickup_address = $app->pickup_address;
        $profile->seller_type = $app->seller_type;
        $profile->idnp = $app->idnp;
        $profile->company_idno = $app->company_idno;
        $profile->delivery_type = $app->delivery_type;
        $profile->courier_company = $app->courier_company;
        $profile->courier_contract_details = $app->courier_contract_details;
        $profile->notes = $app->notes;
        $profile->application_status = 'approved';
        $profile->approved_at = now();

        if (empty($profile->commission_percent)) {
            $profile->commission_percent = 10.00;
        }

        $profile->save();

        $paymentStatus = 'missing';
        $paymentIsActive = false;

        if ($app->has_online_payments_enabled) {
            $hasCorePaymentData = filled($app->payment_provider)
                && $app->payment_provider !== 'none'
                && filled($app->merchant_id)
                && filled($app->api_key)
                && filled($app->secret_key);

            $paymentStatus = $hasCorePaymentData ? 'pending' : 'missing';
        }

        SellerPaymentAccount::updateOrCreate(
            ['seller_profile_id' => $profile->id],
            [
                'provider' => $app->has_online_payments_enabled ? $app->payment_provider : 'none',
                'merchant_id' => $app->merchant_id,
                'terminal_id' => $app->terminal_id,
                'api_key' => $app->api_key,
                'secret_key' => $app->secret_key,
                'payment_contact_email' => $app->payment_contact_email ?: $app->email,
                'settlement_iban' => $app->settlement_iban,
                'is_active' => $paymentIsActive,
                'status' => $paymentStatus,
                'notes' => $app->payment_notes,
                'verified_at' => null,
            ]
        );

        $app->status = 'approved';
        $app->approved_at = now();
        $app->rejected_at = null;
        $app->save();

        Mail::to($user->email)->send(new SellerApprovedPasswordMail($user, $chosenPassword, (string) ($profile->shop_name ?: $app->shop_name)));

        return back()->with('success', 'Cererea a fost aprobata. Sellerul a primit pe email parola setata de admin.');
    }

    public function reject($id)
    {
        $app = SellerApplication::findOrFail($id);

        if ($app->status === 'approved') {
            return back()->with('error', 'Cererea este deja aprobata si nu mai poate fi respinsa direct.');
        }

        $app->status = 'rejected';
        $app->rejected_at = now();
        $app->approved_at = null;
        $app->save();

        return back()->with('success', 'Cererea a fost respinsa.');
    }

    public function destroy($id, SellerAccountDeletionService $deletionService)
    {
        $app = SellerApplication::findOrFail($id);

        $result = $deletionService->deleteApplicationAndRelatedData($app);

        if ($result['deleted_user_id']) {
            return back()->with('success', 'Cererea si contul seller au fost sterse definitiv. Datele asociate au fost curatate din baza de date si cache-ul aplicatiei a fost golit.');
        }

        return back()->with('success', 'Cererea a fost stearsa definitiv.');
    }

    public function updatePaymentAccountStatus(Request $request, SellerApplication $sellerApplication)
    {
        $data = $request->validate([
            'payment_account_status' => ['required', 'in:missing,pending,active,rejected'],
            'payment_account_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = User::where('email', $sellerApplication->email)->first();
        $profile = $user?->sellerProfile;
        $paymentAccount = $profile?->paymentAccount;

        if (!$paymentAccount) {
            return back()->with('error', 'Sellerul nu are inca un cont de plati online creat.');
        }

        $isActive = $data['payment_account_status'] === 'active';

        $paymentAccount->update([
            'status' => $data['payment_account_status'],
            'is_active' => $isActive,
            'notes' => $data['payment_account_notes'] ?: $paymentAccount->notes,
            'verified_at' => $isActive ? now() : null,
        ]);

        return back()->with('success', 'Statusul contului de plati a fost actualizat.');
    }
}
