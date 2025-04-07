<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Service Order Confirmation!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.5;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #ddd;
        }

        .header {
            background-color: #0055a4;
            color: white;
            padding: 15px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 20px;
        }

        .greeting {
            margin-bottom: 20px;
        }

        p {
            color: #000000;
        }

        .section-title {
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 15px;
            color: #0055a4;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table tr td {
            padding: 8px 5px;
            vertical-align: top;
            color: #000000;
        }

        table tr td:first-child {
            font-weight: bold;
            width: 40%;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 15px;
            color: #0055a4;
            font-weight: bold;
        }

        .footer img {
            margin-top: 10px;
            max-width: 100%;
        }

        .note {
            margin-top: 30px;
            font-size: 12px;
        }

        .price-section {
            margin-top: 25px;
            font-weight: bold;
        }

        .package-table td {
            width: auto;
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .package-table th {
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border-bottom: 2px solid #0055a4;
            color: #0055a4;
        }

        .qr-code {
            text-align: center;
            margin: 20px 0;
        }

        .qr-code img {
            max-width: 200px;
        }

        #service-date {
            border: #0055a4 2px solid;
            border-collapse: collapse;
        }

        .service-details table {
            margin-top: 15px;
            border-collapse: collapse;
        }

        .service-details table tr td {
            border-bottom: 1px solid #eee;
        }

        .payment-details {
            background-color: #f9f9f9;
            padding: 15px;
            margin-top: 25px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="qr-code">
            <h2>QR Code</h2>
            <p>Please find the QR code for your order below:</p>
            <img src="{{ $message->embed(storage_path('app/public/qr-codes/' . $data['qr_image'])) }}" alt="QR Code" width="200">
        </div>

        <div class="header">
            <h1>Service Order Confirmation!</h1>
        </div>

        <div class="content">
            <div class="greeting">
                <p>Dear {{ $data['customer']['first_name'] ?? '' }}
                    {{ $data['customer']['last_name'] ?? 'Valued Customer' }},</p>
                <p>Thank you for choosing our services. Your order has been received and is being processed.</p>
            </div>

            <div class="section-title">Customer Details</div>
            <table>
                <tr>
                    <td>Name</td>
                    <td>{{ $data['customer']['first_name'] ?? '' }} {{ $data['customer']['last_name'] ?? '' }}</td>
                </tr>
                @if(!empty($data['customer']['company']))
                    <tr>
                        <td>Company</td>
                        <td>{{ $data['customer']['company'] }}</td>
                    </tr>
                @endif
                <tr>
                    <td>Email</td>
                    <td>{{ $data['customer']['email'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Phone</td>
                    <td>{{ $data['customer']['contact'] ?? 'N/A' }}</td>
                </tr>
                @if(!empty($data['customer']['street_address']))
                    <tr>
                        <td>Street Address</td>
                        <td>{{ $data['customer']['street_address'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['apartment_type']))
                    <tr>
                        <td>Apartment</td>
                        <td>{{ $data['customer']['apartment_type'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['city']))
                    <tr>
                        <td>City</td>
                        <td>{{ $data['customer']['city'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['province']))
                    <tr>
                        <td>Province</td>
                        <td>{{ $data['customer']['province'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['postal_code']))
                    <tr>
                        <td>Postal Code</td>
                        <td>{{ $data['customer']['postal_code'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['country']))
                    <tr>
                        <td>Country</td>
                        <td>{{ $data['customer']['country'] }}</td>
                    </tr>
                @endif
            </table>

            <div class="section-title">Service Details</div>
            <table id="service-date">
                @if(!empty($data['service']['name']))
                    <tr>
                        <td>Service</td>
                        <td>{{ $data['service']['name'] }}</td>
                    </tr>
                @endif

                @if(!empty($data['serviceDetail']['date']))
                    <tr>
                        <td>Service Date</td>
                        <td>{{ $data['serviceDetail']['date'] }}</td>
                    </tr>
                @endif

                @if(!empty($data['serviceDetail']['time']))
                    <tr>
                        <td>Service Time</td>
                        <td>{{ $data['serviceDetail']['time'] }}</td>
                    </tr>
                @endif
            </table>

            <div class="service-details">
                <table id="service-date">
                    @if(!empty($data['serviceDetail']['property_size']))
                        <tr>
                            <td>Property Size</td>
                            <td>{{ $data['serviceDetail']['property_size'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['duration']))
                        <tr>
                            <td>Duration</td>
                            <td>{{ $data['serviceDetail']['duration'] }} minutes</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['number_of_cleaners']))
                        <tr>
                            <td>Number of Cleaners</td>
                            <td>{{ $data['serviceDetail']['number_of_cleaners'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['business_property']))
                        <tr>
                            <td>Property Type</td>
                            <td>{{ $data['serviceDetail']['business_property'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['cleaning_solvents']))
                        <tr>
                            <td>Cleaning Solvents</td>
                            <td>{{ $data['serviceDetail']['cleaning_solvents'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['Equipment']))
                        <tr>
                            <td>Equipment</td>
                            <td>{{ $data['serviceDetail']['Equipment'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['note']))
                        <tr>
                            <td>Special Notes</td>
                            <td>{{ $data['serviceDetail']['note'] }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            @if(isset($data['packageDetails']) && count($data['packageDetails']) > 0)
                <div class="section-title">Package Details</div>
                <table class="package-table">
                    <tr>
                        <th>Package</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                    @foreach($data['packageDetails'] as $package)
                        <tr>
                            <td>{{ $package['package']['name'] ?? 'N/A' }}</td>
                            <td>{{ $package['qty'] ?? '0' }}</td>
                            <td>Rs. {{ number_format($package['price'], 2) }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if(isset($data['payment']) && !empty($data['payment']))
                <div class="section-title">Payment Information</div>
                <div class="payment-details">
                    <table>
                        <tr>
                            <td>Payment Method</td>
                            <td>{{ ucfirst($data['payment']['payment_method'] ?? 'N/A') }}</td>
                        </tr>
                        <tr>
                            <td>Total Price</td>
                            <td>Rs. {{ number_format($data['order']['price'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>Payment Status</td>
                            <td>{{ ucfirst($data['payment']['status'] ?? 'N/A') }}</td>
                        </tr>
                    </table>
                </div>
            @endif

            <div class="note">
                <p>If you have any questions or need to make changes to your order, please contact our customer service
                    at <a href="mailto:support@pearlyskyplc.com">support@pearlyskyplc.com</a> or call us at (123)
                    456-7890</p>
            </div>

            <div class="footer">
                <p>Thank you for your service order!</p>
                <img src="{{ $message->embed(public_path('images/thank.jpg')) }}" alt="Thank You">
            </div>
        </div>
    </div>
</body>
</html>