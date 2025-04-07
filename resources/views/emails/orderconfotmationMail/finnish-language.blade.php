<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Palvelutilausten vahvistus!</title>
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
            <h2>QR-koodi</h2>
            <p>Alla tilauksesi QR-koodi:</p>
            <img src="{{ $message->embed(storage_path('app/public/qr-codes/' . $data['qr_image'])) }}" alt="QR-koodi" width="200">
        </div>

        <div class="header">
            <h1>Palvelutilausten vahvistus!</h1>
        </div>

        <div class="content">
            <div class="greeting">
                <p>Hyvä {{ $data['customer']['first_name'] ?? '' }}
                    {{ $data['customer']['last_name'] ?? 'asiakas' }},</p>
                <p>Kiitos, että valitsit palvelumme. Tilauksesi on vastaanotettu ja sitä käsitellään parhaillaan.</p>
            </div>

            <div class="section-title">Asiakastiedot</div>
            <table>
                <tr>
                    <td>Nimi</td>
                    <td>{{ $data['customer']['first_name'] ?? '' }} {{ $data['customer']['last_name'] ?? '' }}</td>
                </tr>
                @if(!empty($data['customer']['company']))
                    <tr>
                        <td>Yritys</td>
                        <td>{{ $data['customer']['company'] }}</td>
                    </tr>
                @endif
                <tr>
                    <td>Sähköposti</td>
                    <td>{{ $data['customer']['email'] ?? 'Ei saatavilla' }}</td>
                </tr>
                <tr>
                    <td>Puhelin</td>
                    <td>{{ $data['customer']['contact'] ?? 'Ei saatavilla' }}</td>
                </tr>
                @if(!empty($data['customer']['street_address']))
                    <tr>
                        <td>Katuosoite</td>
                        <td>{{ $data['customer']['street_address'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['apartment_type']))
                    <tr>
                        <td>Asunto</td>
                        <td>{{ $data['customer']['apartment_type'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['city']))
                    <tr>
                        <td>Kaupunki</td>
                        <td>{{ $data['customer']['city'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['province']))
                    <tr>
                        <td>Maakunta</td>
                        <td>{{ $data['customer']['province'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['postal_code']))
                    <tr>
                        <td>Postinumero</td>
                        <td>{{ $data['customer']['postal_code'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['country']))
                    <tr>
                        <td>Maa</td>
                        <td>{{ $data['customer']['country'] }}</td>
                    </tr>
                @endif
            </table>

            <div class="section-title">Palvelun tiedot</div>
            <table id="service-date">
                @if(!empty($data['service']['name']))
                    <tr>
                        <td>Palvelu</td>
                        <td>{{ $data['service']['name'] }}</td>
                    </tr>
                @endif

                @if(!empty($data['serviceDetail']['date']))
                    <tr>
                        <td>Palvelun päivämäärä</td>
                        <td>{{ $data['serviceDetail']['date'] }}</td>
                    </tr>
                @endif

                @if(!empty($data['serviceDetail']['time']))
                    <tr>
                        <td>Palvelun kellonaika</td>
                        <td>{{ $data['serviceDetail']['time'] }}</td>
                    </tr>
                @endif
            </table>

            <div class="service-details">
                <table id="service-date">
                    @if(!empty($data['serviceDetail']['property_size']))
                        <tr>
                            <td>Kiinteistön koko</td>
                            <td>{{ $data['serviceDetail']['property_size'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['duration']))
                        <tr>
                            <td>Kesto</td>
                            <td>{{ $data['serviceDetail']['duration'] }} minuuttia</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['number_of_cleaners']))
                        <tr>
                            <td>Siivoojien määrä</td>
                            <td>{{ $data['serviceDetail']['number_of_cleaners'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['business_property']))
                        <tr>
                            <td>Kiinteistön tyyppi</td>
                            <td>{{ $data['serviceDetail']['business_property'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['cleaning_solvents']))
                        <tr>
                            <td>Puhdistusaineet</td>
                            <td>{{ $data['serviceDetail']['cleaning_solvents'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['Equipment']))
                        <tr>
                            <td>Varusteet</td>
                            <td>{{ $data['serviceDetail']['Equipment'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['note']))
                        <tr>
                            <td>Erityisohjeet</td>
                            <td>{{ $data['serviceDetail']['note'] }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            @if(isset($data['packageDetails']) && count($data['packageDetails']) > 0)
                <div class="section-title">Pakettitiedot</div>
                <table class="package-table">
                    <tr>
                        <th>Paketti</th>
                        <th>Määrä</th>
                        <th>Hinta</th>
                    </tr>
                    @foreach($data['packageDetails'] as $package)
                        <tr>
                            <td>{{ $package['package']['name'] ?? 'Ei saatavilla' }}</td>
                            <td>{{ $package['qty'] ?? '0' }}</td>
                            <td>{{ $package['price'] ?? '0' }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if(isset($data['payment']) && !empty($data['payment']))
                <div class="section-title">Maksutiedot</div>
                <div class="payment-details">
                    <table>
                        <tr>
                            <td>Maksutapa</td>
                            <td>{{ ucfirst($data['payment']['payment_method'] ?? 'Ei saatavilla') }}</td>
                        </tr>
                        <tr>
                            <td>Kokonaishinta</td>
                            <td>{{ ucfirst($data['order']['price'] ?? 'N/A') }}</td>
                        </tr>
                        <tr>
                            <td>Maksun tila</td>
                            <td>{{ ucfirst($data['payment']['status'] ?? 'Ei saatavilla') }}</td>
                        </tr>
                    </table>
                </div>
            @endif


            <div class="note">
                <p>Jos sinulla on kysyttävää tai haluat tehdä muutoksia tilaukseesi, ota yhteyttä asiakaspalveluumme
                    sähköpostitse <a href="mailto:support@pearlyskyplc.com">support@pearlyskyplc.com</a> tai soita numeroon (123) 456-7890</p>
            </div>

            <div class="footer">
                <p>Kiitos palvelutilauksestasi!</p>
                <img src="{{ $message->embed(public_path('images/thank.jpg')) }}" alt="Kiitos">
            </div>
        </div>
    </div>
</body>
</html>