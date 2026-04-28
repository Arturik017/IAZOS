<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerPaymentAccount;
use App\Support\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(403, 'Acces interzis.');
        }

        $profile = $user->sellerProfile;

        if (!$profile) {
            abort(404, 'Profil seller inexistent.');
        }

        $paymentAccount = $profile->paymentAccount ?: new SellerPaymentAccount([
            'status' => 'missing',
            'is_active' => false,
            'payment_contact_email' => $user->email,
        ]);

        return view('seller.profile.edit', compact('profile', 'paymentAccount'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(403, 'Acces interzis.');
        }

        $profile = $user->sellerProfile;

        if (!$profile) {
            abort(404, 'Profil seller inexistent.');
        }

        $data = $request->validate([
            'shop_name' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:4096'],
            'remove_avatar' => ['nullable', 'boolean'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company_idno' => ['required', 'string', 'max:50'],
            'delivery_type' => ['nullable', 'in:courier,personal'],
            'courier_company' => ['nullable', 'string', 'max:255'],
            'courier_contract_details' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'has_online_payments_enabled' => ['nullable', 'boolean'],
            'merchant_id' => ['nullable', 'string', 'max:255'],
            'terminal_id' => ['nullable', 'string', 'max:255'],
            'api_key' => ['nullable', 'string', 'max:4000'],
            'secret_key' => ['nullable', 'string', 'max:4000'],
            'payment_contact_email' => ['nullable', 'email', 'max:255'],
            'settlement_iban' => ['nullable', 'string', 'max:64'],
            'payment_notes' => ['nullable', 'string', 'max:3000'],
        ]);

        if (($data['delivery_type'] ?? null) === 'personal') {
            $data['courier_company'] = null;
            $data['courier_contract_details'] = null;
        }

        $supportsAvatar = Schema::hasColumn('seller_profiles', 'avatar_path');
        $avatarRequested = $request->boolean('remove_avatar') || $request->hasFile('avatar');

        if ($supportsAvatar) {
            if ($request->boolean('remove_avatar') && $profile->avatar_path) {
                Storage::disk('public')->delete($profile->avatar_path);
                $data['avatar_path'] = null;
            }

            if ($request->hasFile('avatar')) {
                if ($profile->avatar_path) {
                    Storage::disk('public')->delete($profile->avatar_path);
                }

                $data['avatar_path'] = ImageStorage::storeWebp($request->file('avatar'), 'seller/avatars', 'public', 80, 'avatar', 600, 600);
            }
        }

        unset($data['avatar'], $data['remove_avatar']);

        $paymentData = [
            'merchant_id' => $data['merchant_id'] ?? null,
            'terminal_id' => $data['terminal_id'] ?? null,
            'payment_contact_email' => $data['payment_contact_email'] ?? $user->email,
            'settlement_iban' => $data['settlement_iban'] ?? null,
            'notes' => $data['payment_notes'] ?? null,
        ];

        $paymentData['is_active'] = false;

        if ($request->filled('api_key')) {
            $paymentData['api_key'] = $request->input('api_key');
        }

        if ($request->filled('secret_key')) {
            $paymentData['secret_key'] = $request->input('secret_key');
        }

        if (!$request->boolean('has_online_payments_enabled')) {
            $paymentData['merchant_id'] = null;
            $paymentData['terminal_id'] = null;
            $paymentData['payment_contact_email'] = null;
            $paymentData['settlement_iban'] = null;
            $paymentData['notes'] = $data['payment_notes'] ?? null;
            $paymentData['api_key'] = null;
            $paymentData['secret_key'] = null;
            $paymentStatus = 'missing';
        } else {
            $hasCorePaymentData = filled($paymentData['merchant_id'])
                && (array_key_exists('api_key', $paymentData) || filled(optional($profile->paymentAccount)->api_key))
                && (array_key_exists('secret_key', $paymentData) || filled(optional($profile->paymentAccount)->secret_key));

            $paymentStatus = $hasCorePaymentData ? 'pending' : 'missing';
        }

        $paymentData['status'] = $paymentStatus;
        $paymentData['verified_at'] = null;

        unset(
            $data['has_online_payments_enabled'],
            $data['merchant_id'],
            $data['terminal_id'],
            $data['api_key'],
            $data['secret_key'],
            $data['payment_contact_email'],
            $data['settlement_iban'],
            $data['payment_notes'],
        );

        $profile->update($data);
        $profile->paymentAccount()->updateOrCreate(
            ['seller_profile_id' => $profile->id],
            $paymentData
        );

        $message = 'Profilul seller a fost actualizat.';

        if ($avatarRequested && !$supportsAvatar) {
            $message .= ' Pentru poza de profil trebuie rulata migrarea noua.';
        }

        return redirect()->route('seller.profile.edit')->with('success', $message);
    }
}
