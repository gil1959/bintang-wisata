<?php
namespace App\Services\Payments;
use App\Models\Payment;

class PayPalSettlement
{
    public function capture(Payment $payment,array $capture,array $payload):void
    {
        abort_unless(($capture['status']??'')==='COMPLETED',422,'Capture belum selesai.');
        $expected=$payment->gateway_payload['paypal_amount_usd']??null;
        $amount=$capture['amount']??[];
        abort_unless(is_numeric($expected)&&(float)$expected>0&&($amount['currency_code']??'')==='USD'&&is_numeric($amount['value']??null),422,'Mata uang atau nominal PayPal tidak valid.');
        abort_unless((int)round((float)$expected*100)===(int)round((float)$amount['value']*100),422,'Nominal PayPal tidak cocok.');
        app(VerifiedPaymentStatus::class)->apply($payment,'paid',$payload,(float)$payment->amount);
    }
}
