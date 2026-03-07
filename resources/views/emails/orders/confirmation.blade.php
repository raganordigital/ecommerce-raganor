<x-mail::message>
# Order Confirmation

Thank you for your order! We're pleased to confirm that we've received your order #**{{ $order->order_number }}**.

## Order Summary

<x-mail::table>
| Product | Quantity | Price |
|:--------|:--------:|------:|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | ${{ number_format($item->subtotal, 2) }} |
@endforeach
| **Total** | | **${{ number_format($order->total, 2) }}** |
</x-mail::table>

## Shipping Address
**{{ $order->shipping_name }}**  
{{ $order->shipping_address }}  
{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zipcode }}  
{{ $order->shipping_country }}

## Order Status
You can track your order status by clicking the button below:

<x-mail::button :url="$orderUrl">
View Order Status
</x-mail::button>

If you have any questions about your order, please don't hesitate to contact us.

Thanks,<br>
{{ config('app.name') }}
</x-mail::mail>