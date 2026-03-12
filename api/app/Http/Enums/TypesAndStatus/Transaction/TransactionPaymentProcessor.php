<?php

namespace App\Enums\TypesAndStatus\Transaction;

namespace App\Enums\TypesAndStatus\Transaction;

enum PaymentProvider: string
{
    case Voguepay = 'Voguepay';
    case SSLCommerz = 'SSLCommerz';
    case TwoCheckout = '2Checkout';
    case FlutterWave = 'Flutter Wave';
    case Mercadopago = 'Mercadopago';
    case AuthorizeNet = 'Authorize.Net';
    case Razorpay = 'Razorpay';
    case MolliePayment = 'Mollie Payment';
    case Paytm = 'Paytm';
    case Paystack = 'Paystack';
    case Instamojo = 'Instamojo';
    case Stripe = 'Stripe';
    case Paypal = 'Paypal';
    case FreeOrder = 'Free Order';
    case Unknown = 'Unknown'; // Covers (NULL) or unrecognized options
}
