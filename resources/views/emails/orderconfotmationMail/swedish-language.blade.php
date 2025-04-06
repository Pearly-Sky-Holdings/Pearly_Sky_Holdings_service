<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bekräftelse på servicebeställning!</title>
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
            <h2>QR-kod</h2>
            <p>Här är QR-koden för din beställning:</p>
            <img src="{{ $message->embed(storage_path('app/public/qr-codes/' . $data['qr_image'])) }}" alt="QR-kod" width="200">
        </div>

        <div class="header">
            <h1>Bekräftelse på servicebeställning!</h1>
        </div>

        <div class="content">
            <div class="greeting">
                <p>Bästa {{ $data['customer']['first_name'] ?? '' }}
                    {{ $data['customer']['last_name'] ?? 'kund' }},</p>
                <p>Tack för att du valt våra tjänster. Din beställning har mottagits och behandlas.</p>
            </div>

            <div class="section-title">Kundinformation</div>
            <table>
                <tr>
                    <td>Namn</td>
                    <td>{{ $data['customer']['first_name'] ?? '' }} {{ $data['customer']['last_name'] ?? '' }}</td>
                </tr>
                @if(!empty($data['customer']['company']))
                    <tr>
                        <td>Företag</td>
                        <td>{{ $data['customer']['company'] }}</td>
                    </tr>
                @endif
                <tr>
                    <td>E-post</td>
                    <td>{{ $data['customer']['email'] ?? 'Saknas' }}</td>
                </tr>
                <tr>
                    <td>Telefon</td>
                    <td>{{ $data['customer']['contact'] ?? 'Saknas' }}</td>
                </tr>
                @if(!empty($data['customer']['street_address']))
                    <tr>
                        <td>Gatuadress</td>
                        <td>{{ $data['customer']['street_address'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['apartment_type']))
                    <tr>
                        <td>Lägenhet</td>
                        <td>{{ $data['customer']['apartment_type'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['city']))
                    <tr>
                        <td>Stad</td>
                        <td>{{ $data['customer']['city'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['province']))
                    <tr>
                        <td>Län</td>
                        <td>{{ $data['customer']['province'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['postal_code']))
                    <tr>
                        <td>Postnummer</td>
                        <td>{{ $data['customer']['postal_code'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['country']))
                    <tr>
                        <td>Land</td>
                        <td>{{ $data['customer']['country'] }}</td>
                    </tr>
                @endif
            </table>

            <div class="section-title">Serviceinformation</div>
            <table id="service-date">
                @if(!empty($data['service']['name']))
                    <tr>
                        <td>Tjänst</td>
                        <td>{{ $data['service']['name'] }}</td>
                    </tr>
                @endif

                @if(!empty($data['serviceDetail']['date']))
                    <tr>
                        <td>Servicedatum</td>
                        <td>{{ $data['serviceDetail']['date'] }}</td>
                    </tr>
                @endif

                @if(!empty($data['serviceDetail']['time']))
                    <tr>
                        <td>Servicetid</td>
                        <td>{{ $data['serviceDetail']['time'] }}</td>
                    </tr>
                @endif
            </table>

            <div class="service-details">
                <table id="service-date">
                    @if(!empty($data['serviceDetail']['property_size']))
                        <tr>
                            <td>Fastighetsstorlek</td>
                            <td>{{ $data['serviceDetail']['property_size'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['duration']))
                        <tr>
                            <td>Varaktighet</td>
                            <td>{{ $data['serviceDetail']['duration'] }} minuter</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['number_of_cleaners']))
                        <tr>
                            <td>Antal städare</td>
                            <td>{{ $data['serviceDetail']['number_of_cleaners'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['business_property']))
                        <tr>
                            <td>Fastighetstyp</td>
                            <td>{{ $data['serviceDetail']['business_property'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['cleaning_solvents']))
                        <tr>
                            <td>Rengöringsmedel</td>
                            <td>{{ $data['serviceDetail']['cleaning_solvents'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['Equipment']))
                        <tr>
                            <td>Utrustning</td>
                            <td>{{ $data['serviceDetail']['Equipment'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['note']))
                        <tr>
                            <td>Speciella instruktioner</td>
                            <td>{{ $data['serviceDetail']['note'] }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            @if(isset($data['packageDetails']) && count($data['packageDetails']) > 0)
                <div class="section-title">Paketinformation</div>
                <table class="package-table">
                    <tr>
                        <th>Paket</th>
                        <th>Antal</th>
                        <th>Pris</th>
                    </tr>
                    @foreach($data['packageDetails'] as $package)
                        <tr>
                            <td>{{ $package['package']['name'] ?? 'Saknas' }}</td>
                            <td>{{ $package['qty'] ?? '0' }}</td>
                            <td>{{ number_format($package['price'], 2) }} kr</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if(isset($data['payment']) && !empty($data['payment']['attributes']))
                <div class="section-title">Betalningsinformation</div>
                <div class="payment-details">
                    <table>
                        <tr>
                            <td>Betalningsmetod</td>
                            <td>{{ ucfirst($data['payment']['attributes']['payment_method'] ?? 'Saknas') }}</td>
                        </tr>
                        <tr>
                            <td>Transaktions-ID</td>
                            <td>{{ $data['payment']['attributes']['transaction_id'] ?? 'Saknas' }}</td>
                        </tr>
                        <tr>
                            <td>Betalningsstatus</td>
                            <td>{{ ucfirst($data['payment']['attributes']['status'] ?? 'Saknas') }}</td>
                        </tr>
                        <tr>
                            <td>Betalningsdatum</td>
                            <td>{{ $data['payment']['attributes']['created_at'] ?? 'Saknas' }}</td>
                        </tr>
                    </table>
                </div>
            @endif

            @if(!empty($data['order']['attributes']['price']))
                <div class="price-section">
                    <p>Totalt pris: {{ number_format($data['order']['attributes']['price'], 2) }} kr</p>
                </div>
            @endif

            <div class="note">
                <p>Om du har några frågor eller behöver göra ändringar i din beställning, vänligen kontakta vår kundservice
                    på <a href="mailto:support@pearlyskyplc.com">support@pearlyskyplc.com</a> eller ring oss på (123) 456-7890</p>
            </div>

            <div class="footer">
                <p>Tack för din servicebeställning!</p>
                <img src="{{ $message->embed(public_path('images/thank.jpg')) }}" alt="Tack">
            </div>
        </div>
    </div>
</body>
</html>