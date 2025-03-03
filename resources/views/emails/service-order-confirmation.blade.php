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
        .note {
            margin-top: 30px;
            font-size: 12px;
        }
        .price-section {
            margin-top: 25px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Service Order Confirmation!</h1>
        </div>
        
        <div class="content">
            <div class="greeting">
                <p>Dear {{ $data['customer']->name }},</p>
                <p>Thank you for choosing our services. Your order has been received and is being processed.</p>
            </div>
            
            <div class="section-title">Service Details</div>
            <table>
                <tr>
                    <td>Service</td>
                    <td>{{ $data['service']->name }}</td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td>{{ date('Y-m-d', strtotime($data['serviceDetail']->date)) }}</td>
                </tr>
                <tr>
                    <td>Time</td>
                    <td>{{ date('H:i:s', strtotime($data['serviceDetail']->time)) }}</td>
                </tr>
                <tr>
                    <td>Property Size</td>
                    <td>{{ $data['serviceDetail']->property_size ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Time Duration</td>
                    <td>{{ $data['serviceDetail']->duration ?? '-' }} hours</td>
                </tr>
                <tr>
                    <td>Number of Cleaners</td>
                    <td>{{ $data['serviceDetail']->number_of_cleaners ?? '1' }}</td>
                </tr>
                <tr>
                    <td>Frequency</td>
                    <td>{{ $data['serviceDetail']->frequency ?? 'Weekly' }}</td>
                </tr>
                <tr>
                    <td>Cleaning Solvents</td>
                    <td>{{ $data['serviceDetail']->cleaning_solvents ?? 'Provided By The Company' }}</td>
                </tr>
                <tr>
                    <td>Cleaning Equipments</td>
                    <td>{{ $data['serviceDetail']->Equipment ?? 'Provided By The Company' }}</td>
                </tr>
                <tr>
                    <td>Requirements</td>
                    <td>{{ $data['serviceDetail']->note ?? 'N/A' }}</td>
                </tr>
            </table>
            
            <div class="section-title">Package Details</div>
            <table>
                <tr>
                    <td>Package</td>
                    <td>Quantity</td>
                    <td>Price</td>
                </tr>
                @foreach($data['packageDetails'] as $package)
                <tr>
                    <td>{{ $package->package->name ?? 'N/A' }}</td>
                    <td>{{ $package->qty ?? '0' }}</td>
                    <td>${{ $package->price ?? '0' }}</td>
                </tr>
                @endforeach
            </table>
            
            <div class="price-section">
                <p>Price: ${{ $data['order']->price }}</p>
            </div>
            
            <div class="note">
                <p>If you have any questions or need to make changes to your order, please contact our customer service at <a href="mailto:support@pearlyskyplc.com">support@pearlyskyplc.com</a> or call us at (123) 456-7890</p>
            </div>
            
            <div class="footer">
                <p>Thank you for your service order!</p>
            </div>
        </div>
    </div>
</body>
</html>