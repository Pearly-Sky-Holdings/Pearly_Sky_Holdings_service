<!-- resources/views/emails/service-order-confirmation.blade.php -->
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
            /* Changed paragraph text color to black */
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

    </style>
</head>

<body>
    <div class="container">
        <div class="qr-code">
            <h2>QR Code</h2>
            <p>Please find the QR code for your order below:</p>
            <!-- Use CID attachment method to embed the image directly -->
            <img src="{{ $message->embed(storage_path('app/public/qr-codes/' . basename($data['qr_image']))) }}" alt="QR Code"
                width="200">
        </div>

        <div class="header">
            <h1>Service Order Confirmation!</h1>
        </div>

        <div class="content">
            <div class="greeting">
                <p>Dear {{ $data['customer']->first_name ?? '' }}
                    {{ $data['customer']->last_name ?? 'Valued Customer' }},</p>
                <p>Thank you for choosing our services. Your order has been received and is being processed.</p>
            </div>

            <div class="section-title" >Customer Details</div>
            <table>
                <tr>
                    <td>Name</td>
                    <td>{{ $data['customer']->first_name ?? '' }} {{ $data['customer']->last_name ?? '' }}</td>
                </tr>
                @if(isset($data['customer']->company) && $data['customer']->company)
                    <tr>
                        <td>Company</td>
                        <td>{{ $data['customer']->company }}</td>
                    </tr>
                @endif
                <tr>
                    <td>Email</td>
                    <td>{{ $data['customer']->email ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Phone</td>
                    <td>{{ $data['customer']->contact ?? 'N/A' }}</td>
                </tr>
                @if(isset($data['customer']->street_address) && $data['customer']->street_address)
                    <tr>
                        <td>Street Address</td>
                        <td>{{ $data['customer']->street_address }}</td>
                    </tr>
                @endif
                @if(isset($data['customer']->apartment_type) && $data['customer']->apartment_type)
                    <tr>
                        <td>Apartment</td>
                        <td>{{ $data['customer']->apartment_type }}</td>
                    </tr>
                @endif
                @if(isset($data['customer']->city) && $data['customer']->city)
                    <tr>
                        <td>City</td>
                        <td>{{ $data['customer']->city }}</td>
                    </tr>
                @endif
                @if(isset($data['customer']->province) && $data['customer']->province)
                    <tr>
                        <td>Province</td>
                        <td>{{ $data['customer']->province }}</td>
                    </tr>
                @endif
                @if(isset($data['customer']->postal_code) && $data['customer']->postal_code)
                    <tr>
                        <td>Postal Code</td>
                        <td>{{ $data['customer']->postal_code }}</td>
                    </tr>
                @endif
                @if(isset($data['customer']->country) && $data['customer']->country)
                    <tr>
                        <td>Country</td>
                        <td>{{ $data['customer']->country }}</td>
                    </tr>
                @endif
            </table>

            <div class="section-title">Service Details</div>
            <table id="service-date">
                @if(isset($data['service']->name))
                    <tr>
                        <td>Service</td>
                        <td>{{ $data['service']->name }}</td>
                    </tr>
                @endif

                @if(isset($data['serviceDetail']->date))
                    <tr>
                        <td>Date</td>
                        <td>{{ date('Y-m-d', strtotime($data['serviceDetail']->date)) }}</td>
                    </tr>
                @endif

                @if(isset($data['serviceDetail']->time))
                    <tr>
                        <td>Time</td>
                        <td>{{ date('H:i:s', strtotime($data['serviceDetail']->time)) }}</td>
                    </tr>
                @endif

                @if(isset($data['serviceDetail']->property_size) && $data['serviceDetail']->property_size)
                    <tr>
                        <td>Property Size</td>
                        <td>{{ $data['serviceDetail']->property_size }}</td>
                    </tr>
                @endif

                @if(isset($data['serviceDetail']->business_property) && $data['serviceDetail']->business_property)
                    <tr>
                        <td>Property Type</td>
                        <td>{{ $data['serviceDetail']->business_property }}</td>
                    </tr>
                @endif

                @if(isset($data['serviceDetail']->duration) && $data['serviceDetail']->duration)
                    <tr>
                        <td>Time Duration</td>
                        <td>{{ $data['serviceDetail']->duration }} minutes</td>
                    </tr>
                @endif

                @if(isset($data['serviceDetail']->number_of_cleaners) && $data['serviceDetail']->number_of_cleaners)
                    <tr>
                        <td>Number of Cleaners</td>
                        <td>{{ $data['serviceDetail']->number_of_cleaners }}</td>
                    </tr>
                @endif

                @if(isset($data['serviceDetail']->frequency) && $data['serviceDetail']->frequency)
                    <tr>
                        <td>Frequency</td>
                        <td>{{ $data['serviceDetail']->frequency }}</td>
                    </tr>
                @endif

                @if(isset($data['serviceDetail']->cleaning_solvents) && $data['serviceDetail']->cleaning_solvents)
                    <tr>
                        <td>Cleaning Solvents</td>
                        <td>{{ $data['serviceDetail']->cleaning_solvents }}</td>
                    </tr>
                @endif

                @if(isset($data['serviceDetail']->Equipment) && $data['serviceDetail']->Equipment)
                    <tr>
                        <td>Cleaning Equipment</td>
                        <td>{{ $data['serviceDetail']->Equipment }}</td>
                    </tr>
                @endif

                @if(isset($data['serviceDetail']->note) && $data['serviceDetail']->note)
                    <tr>
                        <td>Special Requirements</td>
                        <td>{{ $data['serviceDetail']->note }}</td>
                    </tr>
                @endif
            </table>

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
                            <td>${{ $package['price'] ?? '0' }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if(isset($data['order']->price))
                <div class="price-section">
                    <p>Total Price: {{ $data['order']->price }}</p>
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