<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>eSewa Payment</title>
</head>

<body>
    <form action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST">
        <input type="hidden" name="amount" value="{{ $order->total_amount }}" required>
        <input type="hidden" name="tax_amount" value="0" required>
        <input type="hidden" name="total_amount" value="{{ $order->total_amount }}" required>
        <input type="hidden" name="transaction_uuid" value="{{ $pid }}" required>
        <input type="hidden" name="product_code" value="EPAYTEST" required>
        <input type="hidden" name="product_service_charge" value="0" required>
        <input type="hidden" name="product_delivery_charge" value="0" required>
        <input type="hidden" name="success_url" value="{{ route('success') }}" required>
        <input type="hidden" name="failure_url" value="{{ route('failure') }}" required>
        <input type="hidden" name="signed_field_names" value="total_amount,transaction_uuid,product_code" required>
        <input type="hidden" name="signature" value="{{ $signature }}" required>
        <input type="submit" value="Pay with eSewa">
    </form>
</body>

</html>
