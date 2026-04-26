<?php

namespace App\Services\Payments;

use App\Models\Order;
use App\Models\SellerPaymentAccount;

class PaymentLinkService
{
    public function __construct(
        private readonly MaibSellerPaymentService $maibSellerPaymentService,
    ) {
    }

    public function createPaymentUrl(Order $order): array
    {
        $order->loadMissing(['seller.sellerProfile.paymentAccount', 'items']);

        /** @var SellerPaymentAccount|null $paymentAccount */
        $paymentAccount = $order->seller?->sellerProfile?->paymentAccount;

        if (!$paymentAccount || !$paymentAccount->isReadyForCheckout()) {
            throw new \RuntimeException('Acest vanzator nu are inca platile online activate.');
        }

        return match ($paymentAccount->provider) {
            'maib' => $this->maibSellerPaymentService->createPaymentUrl($order, $paymentAccount),
            'paynet' => throw new \RuntimeException('Integrarea Paynet pentru selleri este pregatita ca structura, dar mai are nevoie de configurarea API-ului final.'),
            default => throw new \RuntimeException('Providerul de plati al vanzatorului nu este configurat corect.'),
        };
    }
}
