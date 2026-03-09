<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Order Confirmation #{{ $order->order_number }}</title>
    <!--[if mso]>
    <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">

<!-- Preheader text -->
<div style="display:none;font-size:1px;color:#f4f4f5;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">
    Order #{{ $order->order_number }} confirmed — thank you for shopping with us!
</div>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f4f5;">
    <tr>
        <td align="center" style="padding:40px 16px;">

            <!-- Email wrapper -->
            <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;">

                <!-- ═══ HEADER ═══ -->
                <tr>
                    <td style="background-color:#030712;border-radius:16px 16px 0 0;padding:32px 40px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td>
                                    <!-- Logo -->
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
                                    <span style="color:#6b7280;font-size:13px;">Order Confirmation</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- ═══ HERO BAND ═══ -->
                <tr>
                    <td style="background-color:#fbbf24;padding:28px 40px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td>
                                    <p style="margin:0 0 4px 0;color:#92400e;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Thank you for your order!</p>
                                    <h1 style="margin:0;color:#111827;font-size:26px;font-weight:900;letter-spacing:-0.5px;">Order #{{ $order->order_number }}</h1>
                                </td>
                                <td align="right" style="vertical-align:middle;">
                                    <!-- Checkmark circle -->
                                    <div style="width:52px;height:52px;background-color:rgba(0,0,0,0.12);border-radius:50%;text-align:center;line-height:52px;font-size:24px;">✓</div>
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
                            Hi <strong>{{ $order->shipping_name }}</strong>, we've received your order and it's being processed. We'll notify you once it ships.
                        </p>

                        <!-- ─── Order Items ─── -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:32px;">
                            <tr>
                                <td colspan="3" style="padding-bottom:12px;border-bottom:2px solid #f3f4f6;">
                                    <span style="color:#111827;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">Order Items</span>
                                </td>
                            </tr>

                            @foreach($order->items as $item)
                            <tr>
                                <td style="padding:14px 0;border-bottom:1px solid #f3f4f6;vertical-align:top;">
                                    <p style="margin:0 0 2px 0;color:#111827;font-size:14px;font-weight:600;">{{ $item->product_name }}</p>
                                    @if($item->variant_name)
                                    <p style="margin:0;color:#9ca3af;font-size:12px;">{{ $item->variant_name }}</p>
                                    @endif
                                </td>
                                <td style="padding:14px 12px;border-bottom:1px solid #f3f4f6;vertical-align:top;text-align:center;">
                                    <span style="color:#6b7280;font-size:13px;">× {{ $item->quantity }}</span>
                                </td>
                                <td style="padding:14px 0;border-bottom:1px solid #f3f4f6;vertical-align:top;text-align:right;">
                                    <span style="color:#111827;font-size:14px;font-weight:600;">${{ number_format((float) $item->subtotal, 2) }}</span>
                                </td>
                            </tr>
                            @endforeach

                            <!-- Subtotal / shipping / total -->
                            @if($order->shipping_cost > 0)
                            <tr>
                                <td colspan="2" style="padding:10px 0 4px 0;color:#6b7280;font-size:13px;">Subtotal</td>
                                <td style="padding:10px 0 4px 0;text-align:right;color:#374151;font-size:13px;">${{ number_format((float) $order->total - $order->shipping_cost, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding:4px 0;color:#6b7280;font-size:13px;">Shipping</td>
                                <td style="padding:4px 0;text-align:right;color:#374151;font-size:13px;">${{ number_format((float) $order->shipping_cost, 2) }}</td>
                            </tr>
                            @endif

                            @if(isset($order->discount_amount) && $order->discount_amount > 0)
                            <tr>
                                <td colspan="2" style="padding:4px 0;color:#059669;font-size:13px;">Discount</td>
                                <td style="padding:4px 0;text-align:right;color:#059669;font-size:13px;">-${{ number_format((float) $order->discount_amount, 2) }}</td>
                            </tr>
                            @endif

                            <!-- Total -->
                            <tr>
                                <td colspan="3" style="padding-top:12px;">
                                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td style="background-color:#f9fafb;border-radius:10px;padding:14px 16px;">
                                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <td style="color:#111827;font-size:15px;font-weight:700;">Total</td>
                                                        <td align="right" style="color:#111827;font-size:18px;font-weight:900;">${{ number_format((float) $order->total, 2) }}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- ─── Two column: Shipping + Payment ─── -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:32px;">
                            <tr>
                                <!-- Shipping Address -->
                                <td width="48%" style="vertical-align:top;background-color:#f9fafb;border-radius:10px;padding:16px 18px;">
                                    <p style="margin:0 0 8px 0;color:#111827;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">Shipping To</p>
                                    <p style="margin:0;color:#374151;font-size:13px;line-height:1.7;">
                                        <strong>{{ $order->shipping_name }}</strong><br>
                                        {{ $order->shipping_address }}<br>
                                        {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zipcode }}<br>
                                        {{ $order->shipping_country }}
                                    </p>
                                </td>
                                <td width="4%"></td>
                                <!-- Order Info -->
                                <td width="48%" style="vertical-align:top;background-color:#f9fafb;border-radius:10px;padding:16px 18px;">
                                    <p style="margin:0 0 8px 0;color:#111827;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">Order Info</p>
                                    <table cellpadding="0" cellspacing="0" border="0" style="width:100%;">
                                        <tr>
                                            <td style="color:#6b7280;font-size:12px;padding-bottom:5px;">Date</td>
                                            <td style="color:#374151;font-size:12px;font-weight:600;padding-bottom:5px;text-align:right;">{{ $order->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td style="color:#6b7280;font-size:12px;padding-bottom:5px;">Status</td>
                                            <td style="text-align:right;padding-bottom:5px;">
                                                <span style="background-color:#d1fae5;color:#065f46;font-size:11px;font-weight:700;padding:2px 8px;border-radius:99px;">
                                                    {{ ucfirst($order->status instanceof \BackedEnum ? $order->status->value : $order->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="color:#6b7280;font-size:12px;">Payment</td>
                                            <td style="text-align:right;">
                                                <span style="background-color:#d1fae5;color:#065f46;font-size:11px;font-weight:700;padding:2px 8px;border-radius:99px;">Paid</span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- ─── CTA Button ─── -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:32px;">
                            <tr>
                                <td align="center">
                                    <a href="{{ $orderUrl }}" style="display:inline-block;background-color:#fbbf24;color:#111827;font-size:14px;font-weight:800;padding:14px 36px;border-radius:10px;text-decoration:none;letter-spacing:0.01em;">
                                        View Your Order →
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <!-- ─── Help note ─── -->
                        <p style="margin:0;color:#9ca3af;font-size:13px;line-height:1.6;text-align:center;border-top:1px solid #f3f4f6;padding-top:24px;">
                            Questions? Reply to this email or visit our <a href="{{ config('app.url') }}" style="color:#d97706;text-decoration:none;font-weight:600;">help centre</a>.
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
                                    You're receiving this email because you placed an order with us.
                                </td>
                                <td align="right" style="vertical-align:middle;">
                                    <a href="{{ config('app.url') }}" style="color:#d97706;font-size:12px;font-weight:600;text-decoration:none;">{{ config('app.url') }}</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>
            <!-- /Email wrapper -->

        </td>
    </tr>
</table>

</body>
</html>