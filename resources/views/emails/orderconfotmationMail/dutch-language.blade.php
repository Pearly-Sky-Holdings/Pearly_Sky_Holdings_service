<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bevestiging van servicebestelling!</title>
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
            <p>Hieronder vindt u de QR-code voor uw bestelling:</p>
            <img src="{{ $message->embed(storage_path('app/public/qr-codes/' . $data['qr_image'])) }}" alt="QR Code" width="200">
        </div>

        <div class="header">
            <h1>Bevestiging van servicebestelling!</h1>
        </div>

        <div class="content">
            <div class="greeting">
                <p>Beste {{ $data['customer']['first_name'] ?? '' }}
                    {{ $data['customer']['last_name'] ?? 'Klant' }},</p>
                <p>Bedankt voor het kiezen van onze services. Uw bestelling is ontvangen en wordt verwerkt.</p>
            </div>

            <div class="section-title">Klantgegevens</div>
            <table>
                <tr>
                    <td>Naam</td>
                    <td>{{ $data['customer']['first_name'] ?? '' }} {{ $data['customer']['last_name'] ?? '' }}</td>
                </tr>
                @if(!empty($data['customer']['company']))
                    <tr>
                        <td>Bedrijf</td>
                        <td>{{ $data['customer']['company'] }}</td>
                    </tr>
                @endif
                <tr>
                    <td>E-mail</td>
                    <td>{{ $data['customer']['email'] ?? 'N.v.t.' }}</td>
                </tr>
                <tr>
                    <td>Telefoon</td>
                    <td>{{ $data['customer']['contact'] ?? 'N.v.t.' }}</td>
                </tr>
                @if(!empty($data['customer']['street_address']))
                    <tr>
                        <td>Adres</td>
                        <td>{{ $data['customer']['street_address'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['apartment_type']))
                    <tr>
                        <td>Appartement</td>
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
                        <td>Provincie</td>
                        <td>{{ $data['customer']['province'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['postal_code']))
                    <tr>
                        <td>Postcode</td>
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

            <div class="section-title">Servicedetails</div>
            <table id="service-date">
                @if(!empty($data['service']['name']))
                    <tr>
                        <td>Service</td>
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
                        <td>Servicetijd</td>
                        <td>{{ $data['serviceDetail']['time'] }}</td>
                    </tr>
                @endif
            </table>

            <div class="service-details">
                <table id="service-date">
                    @if(!empty($data['serviceDetail']['property_size']))
                        <tr>
                            <td>Oppervlakte</td>
                            <td>{{ $data['serviceDetail']['property_size'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['duration']))
                        <tr>
                            <td>Duur</td>
                            <td>{{ $data['serviceDetail']['duration'] }} minuten</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['number_of_cleaners']))
                        <tr>
                            <td>Aantal schoonmakers</td>
                            <td>{{ $data['serviceDetail']['number_of_cleaners'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['business_property']))
                        <tr>
                            <td>Type pand</td>
                            <td>{{ $data['serviceDetail']['business_property'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['cleaning_solvents']))
                        <tr>
                            <td>Schoonmaakmiddelen</td>
                            <td>{{ $data['serviceDetail']['cleaning_solvents'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['Equipment']))
                        <tr>
                            <td>Apparatuur</td>
                            <td>{{ $data['serviceDetail']['Equipment'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['note']))
                        <tr>
                            <td>Speciale notities</td>
                            <td>{{ $data['serviceDetail']['note'] }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            @if(isset($data['packageDetails']) && count($data['packageDetails']) > 0)
                <div class="section-title">Pakketdetails</div>
                <table class="package-table">
                    <tr>
                        <th>Pakket</th>
                        <th>Aantal</th>
                        <th>Prijs</th>
                    </tr>
                    @foreach($data['packageDetails'] as $package)
                        <tr>
                            <td>{{ $package['package']['name'] ?? 'N.v.t.' }}</td>
                            <td>{{ $package['qty'] ?? '0' }}</td>
                            <td>€ {{ number_format($package['price'], 2) }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if(isset($data['payment']) && !empty($data['payment']))
                <div class="section-title">Betalingsinformatie</div>
                <div class="payment-details">
                    <table>
                        <tr>
                            <td>Betalingsmethode</td>
                            <td>{{ ucfirst($data['payment']['payment_method'] ?? 'N.v.t.') }}</td>
                        </tr>
                        <tr>
                            <td>Totaalbedrag</td>
                            <td>€ {{ number_format($data['order']['price'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>Betalingsstatus</td>
                            <td>{{ ucfirst($data['payment']['status'] ?? 'N.v.t.') }}</td>
                        </tr>
                    </table>
                </div>
            @endif


            <div class="note">
                <p>Als u vragen heeft of wijzigingen in uw bestelling wilt aanbrengen, neem dan contact op met onze klantenservice
                    via <a href="mailto:support@pearlyskyplc.com">support@pearlyskyplc.com</a> of bel ons op (123) 456-7890</p>
            </div>

            <div class="footer">
                <p>Bedankt voor uw servicebestelling!</p>
                <img src="{{ $message->embed(public_path('images/thank.jpg')) }}" alt="Bedankt">
            </div>
        </div>
    </div>
</body>
</html>