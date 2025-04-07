<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>¡Confirmación de orden de servicio!</title>
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
            <h2>Código QR</h2>
            <p>Por favor encuentre abajo el código QR para su orden:</p>
            <img src="{{ $message->embed(storage_path('app/public/qr-codes/' . $data['qr_image'])) }}" alt="Código QR" width="200">
        </div>

        <div class="header">
            <h1>¡Confirmación de orden de servicio!</h1>
        </div>

        <div class="content">
            <div class="greeting">
                <p>Estimado(a) {{ $data['customer']['first_name'] ?? '' }}
                    {{ $data['customer']['last_name'] ?? 'Cliente' }},</p>
                <p>Gracias por elegir nuestros servicios. Hemos recibido su orden y está siendo procesada.</p>
            </div>

            <div class="section-title">Datos del Cliente</div>
            <table>
                <tr>
                    <td>Nombre</td>
                    <td>{{ $data['customer']['first_name'] ?? '' }} {{ $data['customer']['last_name'] ?? '' }}</td>
                </tr>
                @if(!empty($data['customer']['company']))
                    <tr>
                        <td>Empresa</td>
                        <td>{{ $data['customer']['company'] }}</td>
                    </tr>
                @endif
                <tr>
                    <td>Correo electrónico</td>
                    <td>{{ $data['customer']['email'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Teléfono</td>
                    <td>{{ $data['customer']['contact'] ?? 'N/A' }}</td>
                </tr>
                @if(!empty($data['customer']['street_address']))
                    <tr>
                        <td>Dirección</td>
                        <td>{{ $data['customer']['street_address'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['apartment_type']))
                    <tr>
                        <td>Departamento</td>
                        <td>{{ $data['customer']['apartment_type'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['city']))
                    <tr>
                        <td>Ciudad</td>
                        <td>{{ $data['customer']['city'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['province']))
                    <tr>
                        <td>Provincia/Estado</td>
                        <td>{{ $data['customer']['province'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['postal_code']))
                    <tr>
                        <td>Código postal</td>
                        <td>{{ $data['customer']['postal_code'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['country']))
                    <tr>
                        <td>País</td>
                        <td>{{ $data['customer']['country'] }}</td>
                    </tr>
                @endif
            </table>

            <div class="section-title">Detalles del Servicio</div>
            <table id="service-date">
                @if(!empty($data['service']['name']))
                    <tr>
                        <td>Servicio</td>
                        <td>{{ $data['service']['name'] }}</td>
                    </tr>
                @endif

                @if(!empty($data['serviceDetail']['date']))
                    <tr>
                        <td>Fecha del servicio</td>
                        <td>{{ $data['serviceDetail']['date'] }}</td>
                    </tr>
                @endif

                @if(!empty($data['serviceDetail']['time']))
                    <tr>
                        <td>Hora del servicio</td>
                        <td>{{ $data['serviceDetail']['time'] }}</td>
                    </tr>
                @endif
            </table>

            <div class="service-details">
                <table id="service-date">
                    @if(!empty($data['serviceDetail']['property_size']))
                        <tr>
                            <td>Tamaño de la propiedad</td>
                            <td>{{ $data['serviceDetail']['property_size'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['duration']))
                        <tr>
                            <td>Duración</td>
                            <td>{{ $data['serviceDetail']['duration'] }} minutos</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['number_of_cleaners']))
                        <tr>
                            <td>Número de limpiadores</td>
                            <td>{{ $data['serviceDetail']['number_of_cleaners'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['business_property']))
                        <tr>
                            <td>Tipo de propiedad</td>
                            <td>{{ $data['serviceDetail']['business_property'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['cleaning_solvents']))
                        <tr>
                            <td>Productos de limpieza</td>
                            <td>{{ $data['serviceDetail']['cleaning_solvents'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['Equipment']))
                        <tr>
                            <td>Equipo</td>
                            <td>{{ $data['serviceDetail']['Equipment'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['note']))
                        <tr>
                            <td>Notas especiales</td>
                            <td>{{ $data['serviceDetail']['note'] }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            @if(isset($data['packageDetails']) && count($data['packageDetails']) > 0)
                <div class="section-title">Detalles del Paquete</div>
                <table class="package-table">
                    <tr>
                        <th>Paquete</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                    </tr>
                    @foreach($data['packageDetails'] as $package)
                        <tr>
                            <td>{{ $package['package']['name'] ?? 'N/A' }}</td>
                            <td>{{ $package['qty'] ?? '0' }}</td>
                            <td>${{ number_format($package['price'], 2) }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if(isset($data['payment']) && !empty($data['payment']))
                <div class="section-title">Información de Pago</div>
                <div class="payment-details">
                    <table>
                        <tr>
                            <td>Método de pago</td>
                            <td>{{ ucfirst($data['payment']['payment_method'] ?? 'N/A') }}</td>
                        </tr>
                        <tr>
                            <td>Precio total</td>
                            <td>${{ number_format($data['order']['price'], 2) }}</td>
                        </tr>
                        <tr>
                            <td>Estado del pago</td>
                            <td>{{ ucfirst($data['payment']['status'] ?? 'N/A' )}}</td>
                        </tr>
                    </table>
                </div>
            @endif

            <div class="note">
                <p>Si tiene alguna pregunta o necesita hacer cambios a su orden, por favor contacte a nuestro servicio al cliente
                    en <a href="mailto:support@pearlyskyplc.com">support@pearlyskyplc.com</a> o llámenos al (123) 456-7890</p>
            </div>

            <div class="footer">
                <p>¡Gracias por su orden de servicio!</p>
                <img src="{{ $message->embed(public_path('images/thank.jpg')) }}" alt="Gracias">
            </div>
        </div>
    </div>
</body>
</html>