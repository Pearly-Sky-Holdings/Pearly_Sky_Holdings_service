<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Customer Message</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4a6fa5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .details {
            margin-bottom: 20px;
        }
        .detail-row {
            margin-bottom: 10px;
            display: flex;
        }
        .detail-label {
            font-weight: bold;
            width: 100px;
        }
        .message-box {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #4a6fa5;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>New Customer Message Received</h2>
    </div>
    
    <div class="content">
        <div class="details">
            <div class="detail-row">
                <div class="detail-label">Id:</div>
                <div>{{ $data['id'] }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Name:</div>
                <div>{{ $data['name'] }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Email:</div>
                <div><a href="mailto:{{ $data['email'] }}">{{ $data['email'] }}</a></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Contact:</div>
                <div>{{ $data['contact'] }}</div>
            </div>
        </div>
        
        <h3>Message:</h3>
        <div class="message-box">
            {{ $data['massage'] }}
        </div>
    </div>
    
    <div class="footer">
        <p>This message was sent via the contact form on your website.</p>
        <p>© {{ date('Y') }} PearlySky PLC. All rights reserved.</p>
    </div>
</body>
</html>