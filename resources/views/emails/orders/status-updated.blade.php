<x-mail::message>
# Order Status Updated

Your order #**{{ $order->order_number }}** status has been updated.

## New Status: **{{ $order->status->label() }}**

<x-mail::button :url="$orderUrl">
View Order Details
</x-mail::button>

If you have any questions about your order, please don't hesitate to contact us.

Thanks,<br>
{{ config('app.name') }}
</x-mail::mail>