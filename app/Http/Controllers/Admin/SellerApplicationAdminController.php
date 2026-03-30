<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerApplication;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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

    public function approve($id)
    {
        $app = SellerApplication::findOrFail($id);

        if ($app->status === 'approved') {
            return back()->with('error', 'Cererea este deja aprobată.');
        }

        if ($app->status === 'rejected') {
            return back()->with('error', 'Cererea este deja respinsă.');
        }

        $existingUser = User::where('email', $app->email)->first();

        if ($existingUser) {
            $user = $existingUser;

            $user->name = $app->name;
            $user->role = 'seller';
            $user->seller_status = 'approved';

            if (empty($user->password)) {
                $user->password = Hash::make(Str::random(16));
            }

            $user->save();
        } else {
            $user = User::create([
                'name' => $app->name,
                'email' => $app->email,
                'password' => Hash::make(Str::random(16)),
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

        $app->status = 'approved';
        $app->approved_at = now();
        $app->rejected_at = null;
        $app->save();

        Password::sendResetLink([
            'email' => $user->email,
        ]);

        return back()->with('success', 'Cererea a fost aprobată. Sellerul a primit email pentru setarea parolei.');
    }

    public function reject($id)
    {
        $app = SellerApplication::findOrFail($id);

        if ($app->status === 'approved') {
            return back()->with('error', 'Cererea este deja aprobată și nu mai poate fi respinsă direct.');
        }

        $app->status = 'rejected';
        $app->rejected_at = now();
        $app->approved_at = null;
        $app->save();

        return back()->with('success', 'Cererea a fost respinsă.');
    }
}