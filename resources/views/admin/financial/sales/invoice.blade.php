<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>نسخة إلكترونية</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- General CSS File -->
    <link rel="stylesheet" href="/assets/admin/vendor/bootstrap/bootstrap.min.css"/>
    <link rel="stylesheet" href="/assets/vendors/fontawesome/css/all.min.css"/>


    <link rel="stylesheet" href="/assets/admin/css/style.css?v={{ config('app.asset_ver') }}">
    <link rel="stylesheet" href="/assets/admin/css/custom.css?v={{ config('app.asset_ver') }}">
    <link rel="stylesheet" href="/assets/admin/css/components.css?v={{ config('app.asset_ver') }}">

    <style>
        {!! !empty(getCustomCssAndJs('css')) ? getCustomCssAndJs('css') : '' !!}
    </style>
</head>
<body>

<div id="app">
    <section class="section">
        <div class="container mt-5">
            <div class="row">
                <div class="col-12 col-md-10 offset-md-1 col-lg-10 offset-lg-1">

                <div class="row m-0">
                            <div class="col-12 col-md-12">
                            <div class="invoice">
                                            <div class="invoice-print">
                                                <div class="row">
                                                    <div class="col-lg-12">

                                                        <div class="row align-items-center">
                                                            <div class="col-lg-6">
                                                            <img src="/store/1/Logo%20(1).svg"  alt="" />
                                                            </div>
                                                            <div class="col-lg-6 text-left">
                                                                <h1 style="font-size: 20px; margin: 0;">نسخة إلكترونية</h1>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            @if($sale->daftra_invoice_no)
                                                            <div class="col-md-6 pb-2">
                                                                <div>
                                                                    <strong>رقم الفاتورة</strong>
                                                                    <br>
                                                                    {{  $sale->daftra_invoice_no }}
                                                                </div>
                                                            </div>
                                                            @endif
                                                            <div class="col-md-6  pb-2 text-md-right">
                                                                <div>
                                                                    <strong>تاريخ الفاتورة:</strong><br>
                                                                    {{ dateTimeFormat($sale->created_at,'Y M j | H:i') }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6  pb-2">
                                                                <div>
                                                                    <strong>اسم المتدرب:</strong>
                                                                    <br>
                                                                    {{ !empty($sale->buyer) ? $sale->buyer->full_name : '-' }}
                                                                    <br>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6  pb-2 text-md-right">
                                                                <div>
                                                                    <strong>{{ trans('home.platform_address') }}:</strong><br>
                                                                    {!! nl2br(getContactPageSettings('address')) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div>
                                                                    <strong>رقم التواصل بالشركة:</strong>
                                                                    <br>
                                                                    {{ $generalSettings['site_phone'] ?? '' }}
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mt-4">
                                                    <div class="col-md-12">
                                                        <div class="section-title">{{ trans('home.order_summary') }}</div>
                                                        <div class="table-responsive">
                                                            <table class="table table-striped table-hover table-md">
                                                                <tr>
                                                                    <th>البند</th>
                                                                    <th>الكمية</th>
                                                                    <th class="text-center">{{ trans('public.price') }}</th>
                                                                    <th class="text-center">الخصم</th>
                                                                    <th class="text-right">الإجمالي شامل قيمة الضريبة المضافة (15%)</th>
                                                                </tr>

                                                                <tr>
                                                                    <td>{{ $webinar->title }}</td>
                                                                    <td>1</td>
                                                                    <td class="text-center">
                                                                        @if(!empty($sale->amount))
                                                                            {{ handlePrice($sale->amount) }}
                                                                        @else
                                                                            {{ trans('public.free') }}
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if(!empty($sale->discount))
                                                                            {{ handlePrice($sale->discount) }}
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-right">
                                                                        @if(!empty($sale->total_amount))
                                                                            {{ handlePrice($sale->total_amount) }}
                                                                        @else
                                                                            0
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                        <div class="row mt-4">

                                                            <div class="col-lg-12 text-right">
                                                                <div class="invoice-detail-item">
                                                                    <div class="invoice-detail-name">المجموع قبل الضريبة</div>
                                                                    <div class="invoice-detail-value">{{ handlePrice($sale->amount) }}</div>
                                                                </div>
                                                                @if(!empty($sale->tax))
                                                                <div class="invoice-detail-item">
                                                                    <div class="invoice-detail-name">{{ trans('cart.tax') }}</div>
                                                                    <div class="invoice-detail-value">
                                                                            {{ handlePrice($sale->tax) }}
                                                                    </div>
                                                                </div>
                                                                @endif
                                                                <div class="invoice-detail-item">
                                                                    <div class="invoice-detail-name">{{ trans('public.discount') }}</div>
                                                                    <div class="invoice-detail-value">
                                                                        @if(!empty($sale->discount))
                                                                            {{ handlePrice($sale->discount) }}
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <hr class="mt-2 mb-2">
                                                                <div class="invoice-detail-item">
                                                                    <div class="invoice-detail-name">المجموع شامل ضريبة القيمة المضافة (15%)</div>
                                                                    <div class="invoice-detail-value invoice-detail-value-lg">
                                                                        @if(!empty($sale->total_amount))
                                                                            {{ handlePrice($sale->total_amount) }}
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if($sale->online_payment_method)
                                            <hr>
                                            @if(in_array($sale->online_payment_method,['حوالة بنكية','شبكة']))
                                            <div>فاتورة بطريقة الدفع {{ $sale->online_payment_method }} بالرقم المرجعي {{ $sale->online_payment_method_id }}</div>
                                            @else
                                            <div>
                                                فاتورة من الدفع الألكتروني {{ $sale->online_payment_method }} بالرقم المرجعي {{ $sale->online_payment_method_id }}
                                            </div>
                                            @endif
                                            @endif
                                            <hr>
                                            <div class="text-md-right">

                                                <button type="button" onclick="window.print()" class="btn btn-warning btn-icon icon-left"><i class="fas fa-print"></i> طباعة</button>
                                            </div>
                                        </div>
                            </div>

                        </div>

                </div>
            </div>
        </div>
    </section>
</div>
</body>
