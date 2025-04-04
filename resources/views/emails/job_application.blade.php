<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Job Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f7f7f7;
            padding: 15px;
            border-bottom: 2px solid #ddd;
        }
        .content {
            padding: 20px 0;
        }
        .footer {
            border-top: 1px solid #ddd;
            padding-top: 15px;
            font-size: 12px;
            color: #777;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        table tr:last-child td {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            width: 30%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Job Application Received</h2>
        </div>
        
        <div class="content">
            <p>A new job application has been submitted. Please find the details and attached resume below:</p>
            
            <table>
                <tr>
                    <td class="label">Application ID:</td>
                    <td>{{ $data->id }}</td>
                </tr>
                <tr>
                    <td class="label">Applicant:</td>
                    <td>{{ $data->first_name }} {{ $data->last_name }}</td>
                </tr>
                @if(isset($data->email))
                <tr>
                    <td class="label">Email:</td>
                    <td>{{ $data->email }}</td>
                </tr>
                @endif
                @if(isset($data->phone))
                <tr>
                    <td class="label">Phone:</td>
                    <td>{{ $data->phone }}</td>
                </tr>
                @endif
                @if(isset($data->position))
                <tr>
                    <td class="label">Position Applied:</td>
                    <td>{{ $data->position }}</td>
                </tr>
                @endif
                @if(isset($data->applied_date))
                <tr>
                    <td class="label">Date Applied:</td>
                    <td>{{ date('F j, Y', strtotime($data->created_at)) }}</td>
                </tr>
                @endif
            </table>
            
            <p>The applicant's resume is attached to this email.</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message from the Pearly Sky PLC recruitment system. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} Pearly Sky PLC. All rights reserved.</p>
        </div>
    </div>
</body>
</html>