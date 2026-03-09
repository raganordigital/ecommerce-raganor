<!DOCTYPE html>
<html>
<head>
    <title>New Order #{{ $order->order_number }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background-color: #030712; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
            <h1 style="margin: 0;">New Order Received!</h1>
        </div>
        
        <div style="background-color: #f4f4f4; padding: 20px; border-radius: 0 0 8px 8px;">
            <p><strong>Order #:</strong> {{ $order->order_number }}</p>
            <p><strong>Date:</strong> {{ $order->created_at->format('F j, Y, g:i a') }}</p>
            
            <h2>Customer Information</h2>
            <p><strong>Name:</strong> {{ $order->user->name }}</p>
            <p><strong>Email:</strong> {{ $order->user->email }}</p>
            <p><strong>Phone:</strong> {{ $order->shipping_phone }}</p>
            
            <h2>Order Items</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #ddd;">
                        <th style="padding: 10px; text-align: left;">Product</th>
                        <th style="padding: 10px; text-align: center;">Qty</th>
                        <th style="padding: 10px; text-align: right;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 10px;">{{ $item->product_name }}</td>
                        <td style="padding: 10px; text-align: center;">{{ $item->quantity }}</td>
                        <td style="padding: 10px; text-align: right;">${{ number_format((float) $item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="padding: 10px; text-align: right;"><strong>Total:</strong></td>
                        <td style="padding: 10px; text-align: right; font-size: 18px; font-weight: bold;">${{ number_format((float) $order->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            
            <h2>Shipping Address</h2>
            <p>
                {{ $order->shipping_name }}<br>
                {{ $order->shipping_address }}<br>
                {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zipcode }}<br>
                {{ $order->shipping_country }}
            </p>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ $adminUrl }}" style="background-color: #1d4ed8; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">View Order in Admin Panel</a>
            </div>
        </div>
    </div>
</body>
</html>