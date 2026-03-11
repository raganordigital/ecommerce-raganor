<x-mail::message>
# Verify Your Email Address

Hello,

You're almost there! To complete your order, please verify your email address by entering the following verification code on the checkout page.

## Verification Code: **{{ $code }}**

This code will expire in 10 minutes.

If you did not attempt to place an order, no further action is required.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>