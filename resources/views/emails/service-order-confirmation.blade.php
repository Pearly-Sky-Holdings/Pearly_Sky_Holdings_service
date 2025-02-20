<!DOCTYPE html>
<html>
<head>
    <title>Service Order Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f4f4f4; padding: 10px; text-align: center; }
        .content { padding: 20px 0; }
        .footer { font-size: 12px; color: #888; text-align: center; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Service Order Confirmation</h1>
        </div>
        
        <div class="content">
            <h2>Dear {{ $data['customer']->first_name ?? '' }} {{ $data['customer']->last_name ?? '' }},</h2>
            
            <p>Thank you for choosing our services. Your order has been received and is being processed.</p>
            
            <h3>Service Details:</h3>
            <ul>
                @if(isset($data['service']) && $data['service'])
                <li><strong>Service:</strong> {{ $data['service']->name ?? 'N/A' }}</li>
                @endif
                @if(isset($data['serviceDetail']) && $data['serviceDetail'])
                <li><strong>Date:</strong> {{ $data['serviceDetail']->date ?? 'N/A' }}</li>
                <li><strong>Time:</strong> {{ $data['serviceDetail']->time ?? 'N/A' }}</li>
                <li><strong>Price:</strong> ${{ $data['serviceDetail']->price ?? '0.00' }}</li>
                @if(isset($data['serviceDetail']->number_of_cleaners))
                <li><strong>Number of Cleaners:</strong> {{ $data['serviceDetail']->number_of_cleaners }}</li>
                @endif
                @if(isset($data['serviceDetail']->property_size))
                <li><strong>Property Size:</strong> {{ $data['serviceDetail']->property_size }}</li>
                @endif
                @endif
            </ul>
            
            @if(isset($data['packageDetails']) && count($data['packageDetails']) > 0)
            <h3>Package Details:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Package</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['packageDetails'] as $detail)
                    <tr>
                        <td>{{ $detail->package->name ?? 'N/A' }}</td>
                        <td>{{ $detail->qty ?? '0' }}</td>
                        <td>${{ $detail->price ?? '0.00' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
            
            <p>If you have any questions or need to make changes to your order, please contact our customer service at support@example.com or call us at (123) 456-7890.</p>
            
            <p>Thank you for your business!</p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} Your Company Name. All rights reserved.</p>
            <p>This email was sent to {{ $data['customer']->email ?? '' }}</p>
        </div>
    </div>
</body>
</html>