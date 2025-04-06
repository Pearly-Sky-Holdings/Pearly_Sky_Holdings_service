<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>サービス注文確認!</title>
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
            <h2>QRコード</h2>
            <p>注文のQRコードは以下の通りです:</p>
            <img src="{{ $message->embed(storage_path('app/public/qr-codes/' . $data['qr_image'])) }}" alt="QRコード" width="200">
        </div>

        <div class="header">
            <h1>サービス注文確認!</h1>
        </div>

        <div class="content">
            <div class="greeting">
                <p>{{ $data['customer']['first_name'] ?? '' }}
                    {{ $data['customer']['last_name'] ?? 'お客様' }} 様</p>
                <p>当社サービスをご利用いただきありがとうございます。ご注文を承りました。</p>
            </div>

            <div class="section-title">お客様情報</div>
            <table>
                <tr>
                    <td>お名前</td>
                    <td>{{ $data['customer']['first_name'] ?? '' }} {{ $data['customer']['last_name'] ?? '' }}</td>
                </tr>
                @if(!empty($data['customer']['company']))
                    <tr>
                        <td>会社名</td>
                        <td>{{ $data['customer']['company'] }}</td>
                    </tr>
                @endif
                <tr>
                    <td>メールアドレス</td>
                    <td>{{ $data['customer']['email'] ?? 'なし' }}</td>
                </tr>
                <tr>
                    <td>電話番号</td>
                    <td>{{ $data['customer']['contact'] ?? 'なし' }}</td>
                </tr>
                @if(!empty($data['customer']['street_address']))
                    <tr>
                        <td>住所</td>
                        <td>{{ $data['customer']['street_address'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['apartment_type']))
                    <tr>
                        <td>建物名</td>
                        <td>{{ $data['customer']['apartment_type'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['city']))
                    <tr>
                        <td>市区町村</td>
                        <td>{{ $data['customer']['city'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['province']))
                    <tr>
                        <td>都道府県</td>
                        <td>{{ $data['customer']['province'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['postal_code']))
                    <tr>
                        <td>郵便番号</td>
                        <td>{{ $data['customer']['postal_code'] }}</td>
                    </tr>
                @endif
                @if(!empty($data['customer']['country']))
                    <tr>
                        <td>国</td>
                        <td>{{ $data['customer']['country'] }}</td>
                    </tr>
                @endif
            </table>

            <div class="section-title">サービス詳細</div>
            <table id="service-date">
                @if(!empty($data['service']['name']))
                    <tr>
                        <td>サービス名</td>
                        <td>{{ $data['service']['name'] }}</td>
                    </tr>
                @endif

                @if(!empty($data['serviceDetail']['date']))
                    <tr>
                        <td>サービス日</td>
                        <td>{{ $data['serviceDetail']['date'] }}</td>
                    </tr>
                @endif

                @if(!empty($data['serviceDetail']['time']))
                    <tr>
                        <td>サービス時間</td>
                        <td>{{ $data['serviceDetail']['time'] }}</td>
                    </tr>
                @endif
            </table>

            <div class="service-details">
                <table id="service-date">
                    @if(!empty($data['serviceDetail']['property_size']))
                        <tr>
                            <td>物件サイズ</td>
                            <td>{{ $data['serviceDetail']['property_size'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['duration']))
                        <tr>
                            <td>所要時間</td>
                            <td>{{ $data['serviceDetail']['duration'] }} 分</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['number_of_cleaners']))
                        <tr>
                            <td>清掃員数</td>
                            <td>{{ $data['serviceDetail']['number_of_cleaners'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['business_property']))
                        <tr>
                            <td>物件タイプ</td>
                            <td>{{ $data['serviceDetail']['business_property'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['cleaning_solvents']))
                        <tr>
                            <td>洗剤</td>
                            <td>{{ $data['serviceDetail']['cleaning_solvents'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['Equipment']))
                        <tr>
                            <td>設備</td>
                            <td>{{ $data['serviceDetail']['Equipment'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['serviceDetail']['note']))
                        <tr>
                            <td>特記事項</td>
                            <td>{{ $data['serviceDetail']['note'] }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            @if(isset($data['packageDetails']) && count($data['packageDetails']) > 0)
                <div class="section-title">パッケージ詳細</div>
                <table class="package-table">
                    <tr>
                        <th>パッケージ</th>
                        <th>数量</th>
                        <th>価格</th>
                    </tr>
                    @foreach($data['packageDetails'] as $package)
                        <tr>
                            <td>{{ $package['package']['name'] ?? 'なし' }}</td>
                            <td>{{ $package['qty'] ?? '0' }}</td>
                            <td>{{ number_format($package['price'], 2) }}円</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            @if(isset($data['payment']) && !empty($data['payment']['attributes']))
                <div class="section-title">支払い情報</div>
                <div class="payment-details">
                    <table>
                        <tr>
                            <td>支払い方法</td>
                            <td>{{ ucfirst($data['payment']['attributes']['payment_method'] ?? 'なし') }}</td>
                        </tr>
                        <tr>
                            <td>取引ID</td>
                            <td>{{ $data['payment']['attributes']['transaction_id'] ?? 'なし' }}</td>
                        </tr>
                        <tr>
                            <td>支払い状況</td>
                            <td>{{ ucfirst($data['payment']['attributes']['status'] ?? 'なし') }}</td>
                        </tr>
                        <tr>
                            <td>支払い日</td>
                            <td>{{ $data['payment']['attributes']['created_at'] ?? 'なし' }}</td>
                        </tr>
                    </table>
                </div>
            @endif

            @if(!empty($data['order']['attributes']['price']))
                <div class="price-section">
                    <p>合計金額: {{ number_format($data['order']['attributes']['price'], 2) }}円</p>
                </div>
            @endif

            <div class="note">
                <p>ご質問や注文内容の変更がございましたら、カスタマーサービスまでご連絡ください
                    <a href="mailto:support@pearlyskyplc.com">support@pearlyskyplc.com</a> またはお電話 (123) 456-7890</p>
            </div>

            <div class="footer">
                <p>ご注文ありがとうございます!</p>
                <img src="{{ $message->embed(public_path('images/thank.jpg')) }}" alt="ありがとう">
            </div>
        </div>
    </div>
</body>
</html>