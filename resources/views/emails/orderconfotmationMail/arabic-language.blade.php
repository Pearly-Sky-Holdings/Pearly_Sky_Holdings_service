<!DOCTYPE html>
<html dir="rtl">

<head>
    <meta charset="utf-8">
    <title>!تأكيد طلب الخدمة</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.6;
            text-align: right;
            direction: rtl;
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
            text-align: right;
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
            text-align: right;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .package-table th {
            font-weight: bold;
            text-align: right;
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
            <h2>رمز الاستجابة السريعة</h2>
            <p>الرجاء العثور على رمز الاستجابة السريعة لطلبك أدناه:</p>
            <img src="{{ $message->embed(storage_path('app/public/qr-codes/' . $data['qr_image'])) }}" alt="رمز الاستجابة السريعة" width="200">
        </div>

        <div class="header">
            <h1>!تأكيد طلب الخدمة</h1>
        </div>

        <div class="content">
            <div class="greeting">
                <p>عزيزي {{ $data['customer']['first_name'] ?? '' }}
                    {{ $data['customer']['last_name'] ?? 'العميل' }},</p>
                <p>شكرًا لاختيارك خدماتنا. لقد تم استلام طلبك وهو قيد المعالجة الآن.</p>
            </div>

            <div class="section-title">تفاصيل العميل</div>
            <table>
                <tr>
                    <td>الاسم</td>
                    <td>{{ $data['customer']['first_name'] ?? '' }} {{ $data['customer']['last_name'] ?? '' }}</td>
                </tr>
                @if(!empty($data['customer']['company']))
                    <tr>
                        <td>الشركة</td>
                        <td>{{ $data['customer']['company'] }}</td>
                    </tr>
                @endif
                <tr>
                    <td>البريد الإلكتروني</td>
                    <td>{{ $data['customer']['email'] ?? 'غير متوفر' }}</td>
                </tr>
                <tr>
                    <td>الهاتف</td>
                    <td>{{ $data['customer']['contact'] ?? 'غير متوفر' }}</td>
                </tr>
                @if(!empty($data['customer']['street_address']))
                    <tr>
                        <td>عنوان الشارع</td>
                        <td>{{ $data['customer']['street_address'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['apartment_type']))
                    <tr>
                        <td>الشقة</td>
                        <td>{{ $data['customer']['apartment_type'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['city']))
                    <tr>
                        <td>المدينة</td>
                        <td>{{ $data['customer']['city'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['province']))
                    <tr>
                        <td>المحافظة</td>
                        <td>{{ $data['customer']['province'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['postal_code']))
                    <tr>
                        <td>الرمز البريدي</td>
                        <td>{{ $data['customer']['postal_code'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['country']))
                    <tr>
                        <td>البلد</td>
                        <td>{{ $data['customer']['country'] }}</td>
                    </tr>
                @endif
            </table>

            <div class="section-title">تفاصيل الخدمة</div>
            <table id="service-date">
                @if(!empty($data['service']['name']))
                    <tr>
                        <td>الخدمة</td>
                        <td>{{ $data['service']['name'] }}</td>
                    </tr>
                @endif

                @if(!empty($data['serviceDetail']['date']))
                    <tr>
                        <td>تاريخ الخدمة</td>
                        <td>{{ $data['serviceDetail']['date'] }}</td>
                    </tr>
                @endif

                @if(!empty($data['serviceDetail']['time']))
                    <tr>
                        <td>وقت الخدمة</td>
                        <td>{{ $data['serviceDetail']['time'] }}</td>
                    </tr>
                @endif
            </table>

            <div class="service-details">
                <table id="service-date">
                    @if(!empty($data['serviceDetail']['property_size']))
                        <tr>
                            <td>مساحة العقار</td>
                            <td>{{ $data['serviceDetail']['property_size'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['duration']))
                        <tr>
                            <td>المدة</td>
                            <td>{{ $data['serviceDetail']['duration'] }} دقيقة</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['number_of_cleaners']))
                        <tr>
                            <td>عدد عمال النظافة</td>
                            <td>{{ $data['serviceDetail']['number_of_cleaners'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['business_property']))
                        <tr>
                            <td>نوع العقار</td>
                            <td>{{ $data['serviceDetail']['business_property'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['cleaning_solvents']))
                        <tr>
                            <td>مواد التنظيف</td>
                            <td>{{ $data['serviceDetail']['cleaning_solvents'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['Equipment']))
                        <tr>
                            <td>المعدات</td>
                            <td>{{ $data['serviceDetail']['Equipment'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['note']))
                        <tr>
                            <td>ملاحظات خاصة</td>
                            <td>{{ $data['serviceDetail']['note'] }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            @if(isset($data['packageDetails']) && count($data['packageDetails']) > 0)
                <div class="section-title">تفاصيل الباقة</div>
                <table class="package-table">
                    <tr>
                        <th>الباقة</th>
                        <th>الكمية</th>
                        <th>السعر</th>
                    </tr>
                    @foreach($data['packageDetails'] as $package)
                        <tr>
                            <td>{{ $package['package']['name'] ?? 'غير متوفر' }}</td>
                            <td>{{ $package['qty'] ?? '0' }}</td>
                            <td>{{ number_format($package['price'], 2) }} ر.س</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if(isset($data['payment']) && !empty($data['payment']))
                <div class="section-title">معلومات الدفع</div>
                <div class="payment-details">
                    <table>
                        <tr>
                            <td>طريقة الدفع</td>
                            <td>{{ ucfirst($data['payment']['payment_method'] ?? 'غير متوفر') }}</td>
                        </tr>
                        <tr>
                            <td>حالة الدفع</td>
                            <td>{{ ucfirst($data['payment']['status'] ?? 'غير متوفر') }}</td>
                        </tr>
                        <tr>
                            <td>السعر الإجمالي: </td>
                            <td> {{ number_format($data['order']['price'], 2) }} ر.س</td>
                        </tr>
                    </table>
                </div>
            @endif

            <div class="note">
                <p>إذا كان لديك أي استفسارات أو تحتاج إلى إجراء تغييرات على طلبك، يرجى الاتصال بخدمة العملاء لدينا
                    على <a href="mailto:support@pearlyskyplc.com">support@pearlyskyplc.com</a> أو اتصل بنا على (123) 456-7890</p>
            </div>

            <div class="footer">
                <p>شكرًا لك على طلب الخدمة!</p>
                <img src="{{ $message->embed(public_path('images/thank.jpg')) }}" alt="شكرًا">
            </div>
        </div>
    </div>
</body>
</html>