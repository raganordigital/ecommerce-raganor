<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Order Update #{{ $order->order_number }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">

<!-- Preheader -->
<div style="display:none;font-size:1px;color:#f4f4f5;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">
    Your order #{{ $order->order_number }} status has been updated to {{ $order->status instanceof \BackedEnum ? $order->status->value : $order->status }}.
</div>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f4f5;">
    <tr>
        <td align="center" style="padding:40px 16px;">

            <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;">

                <!-- ═══ HEADER ═══ -->
                <tr>
                    <td style="background-color:#030712;border-radius:16px 16px 0 0;padding:32px 40px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td>
                                    <table cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td style="background-color:#fbbf24;border-radius:10px;width:36px;height:36px;text-align:center;vertical-align:middle;">
                                                <span style="color:#111827;font-weight:900;font-size:16px;line-height:36px;display:block;">E</span>
                                            </td>
                                            <td style="padding-left:10px;vertical-align:middle;">
                                                <span style="color:#ffffff;font-weight:900;font-size:20px;letter-spacing:-0.5px;">{{ config('app.name') }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td align="right" style="vertical-align:middle;">
                                    <span style="color:#6b7280;font-size:13px;">Order Update</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- ═══ STATUS HERO ═══ -->
                @php
                    $status = $order->status instanceof \BackedEnum ? $order->status->value : $order->status;
                    $statusConfig = match(strtolower($status)) {
                        'processing'  => ['bg' => '#dbeafe', 'color' => '#1e40af', 'icon' => '⚙️', 'band' => '#2563eb', 'bandText' => '#eff6ff'],
                        'shipped'     => ['bg' => '#e0e7ff', 'color' => '#3730a3', 'icon' => '🚚', 'band' => '#4f46e5', 'bandText' => '#eef2ff'],
                        'delivered'   => ['bg' => '#d1fae5', 'color' => '#065f46', 'icon' => '✅', 'band' => '#059669', 'bandText' => '#ecfdf5'],
                        'cancelled'   => ['bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => '✕',  'band' => '#dc2626', 'bandText' => '#fef2f2'],
                        'refunded'    => ['bg' => '#fce7f3', 'color' => '#9d174d', 'icon' => '↩',  'band' => '#db2777', 'bandText' => '#fdf4ff'],
                        default       => ['bg' => '#fef3c7', 'color' => '#92400e', 'icon' => '📦', 'band' => '#d97706', 'bandText' => '#fffbeb'],
                    };
                @endphp
                <tr>
                    <td style="background-color:{{ $statusConfig['band'] }};padding:28px 40px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td>
                                    <p style="margin:0 0 4px 0;color:{{ $statusConfig['bandText'] }};opacity:0.8;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Status Update</p>
                                    <h1 style="margin:0;color:#ffffff;font-size:24px;font-weight:900;letter-spacing:-0.5px;">Order #{{ $order->order_number }}</h1>
                                </td>
                                <td align="right" style="vertical-align:middle;">
                                    <span style="background-color:rgba(255,255,255,0.2);color:#ffffff;font-size:13px;font-weight:700;padding:6px 14px;border-radius:99px;white-space:nowrap;">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- ═══ BODY ═══ -->
                <tr>
                    <td style="background-color:#ffffff;padding:40px;">

                        <!-- Greeting -->
                        <p style="margin:0 0 24px 0;color:#374151;font-size:15px;line-height:1.6;">
                            Hi <strong>{{ $order->shipping_name }}</strong>, here's an update on your recent order.
                        </p>

                        <!-- Status card -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
                            <tr>
                                <td style="background-color:{{ $statusConfig['bg'] }};border-radius:12px;padding:20px 24px;">
                                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td style="vertical-align:middle;width:44px;">
                                                <div style="width:40px;height:40px;background-color:{{ $statusConfig['color'] }};border-radius:50%;text-align:center;line-height:40px;font-size:18px;">
                                                    {{ $statusConfig['icon'] }}
                                                </div>
                                            </td>
                                            <td style="padding-left:14px;vertical-align:middle;">
                                                <p style="margin:0 0 2px 0;color:#6b7280;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;">New Status</p>
                                                <p style="margin:0;color:{{ $statusConfig['color'] }};font-size:18px;font-weight:900;">{{ ucfirst($status) }}</p>
                                            </td>
                                            <td align="right" style="vertical-align:middle;">
                                                <p style="margin:0;color:#9ca3af;font-size:12px;">{{ now()->format('M d, Y') }}</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Status-specific message -->
                        @php
                            $statusMessage = match(strtolower($status)) {
                                'processing' => 'Great news! We\'ve received your payment and are now preparing your order. You\'ll get another update once it ships.',
                                'shipped'    => 'Your order is on its way! Check your tracking information for real-time delivery updates.',
                                'delivered'  => 'Your order has been delivered. We hope you love your purchase! If anything isn\'t right, please contact us within 30 days.',
                                'cancelled'  => 'Your order has been cancelled. If you paid, a refund will be processed within 5–10 business days.',
                                'refunded'   => 'Your refund has been processed and should appear in your account within 5–10 business days.',
                                default      => 'Your order status has been updated. Click below to view the latest details.',
                            };
                        @endphp
                        <p style="margin:0 0 28px 0;color:#374151;font-size:14px;line-height:1.7;background-color:#f9fafb;border-radius:10px;padding:16px 18px;">
                            {{ $statusMessage }}
                        </p>

                        <!-- Order snapshot -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
                            <tr>
                                <td style="padding-bottom:10px;border-bottom:1px solid #f3f4f6;">
                                    <span style="color:#111827;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">Order Summary</span>
                                </td>
                            </tr>
                            @foreach($order->items as $item)
                            <tr>
                                <td style="padding:10px 0;border-bottom:1px solid #f3f4f6;color:#374151;font-size:13px;">{{ $item->product_name }}</td>
                                <td style="padding:10px 0;border-bottom:1px solid #f3f4f6;text-align:center;color:#9ca3af;font-size:12px;">× {{ $item->quantity }}</td>
                                <td style="padding:10px 0;border-bottom:1px solid #f3f4f6;text-align:right;color:#111827;font-size:13px;font-weight:600;">${{ number_format((float) $item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="2" style="padding-top:10px;color:#374151;font-size:13px;font-weight:700;">Total</td>
                                <td style="padding-top:10px;text-align:right;color:#111827;font-size:14px;font-weight:900;">${{ number_format((float) $order->total, 2) }}</td>
                            </tr>
                        </table>

                        <!-- CTA -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:32px;">
                            <tr>
                                <td align="center">
                                    <a href="{{ $orderUrl }}" style="display:inline-block;background-color:#fbbf24;color:#111827;font-size:14px;font-weight:800;padding:14px 36px;border-radius:10px;text-decoration:none;letter-spacing:0.01em;">
                                        View Order Details →
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <!-- Help -->
                        <p style="margin:0;color:#9ca3af;font-size:13px;line-height:1.6;text-align:center;border-top:1px solid #f3f4f6;padding-top:24px;">
                            Questions about your order? <a href="{{ config('app.url') }}" style="color:#d97706;text-decoration:none;font-weight:600;">Contact us</a> — we're happy to help.
                        </p>

                    </td>
                </tr>

                <!-- ═══ FOOTER ═══ -->
                <tr>
                    <td style="background-color:#f9fafb;border-radius:0 0 16px 16px;padding:24px 40px;border-top:1px solid #e5e7eb;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="color:#9ca3af;font-size:12px;line-height:1.6;">
                                    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                                    You're receiving this because you placed an order with us.
                                </td>
                                <td align="right" style="vertical-align:middle;">
                                    <a href="{{ config('app.url') }}" style="color:#d97706;font-size:12px;font-weight:600;text-decoration:none;">{{ config('app.url') }}</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>