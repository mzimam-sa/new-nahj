@extends(getTemplate().'.layouts.app')

@push('styles_top')

@endpush

@section('content')
    <section class="cart-banner position-relative text-center">
        <h1 class="font-30 text-secondary font-weight-bold">{{ trans('cart.checkout') }}</h1>
        <span class="payment-hint font-20 text-secondary d-block">{{ handlePrice($total) . ' ' .  trans('cart.for_items',['count' => $count]) }}</span>
    </section>

    <section class="container mt-45">

        @if(!empty($totalCashbackAmount))
            <div class="d-flex align-items-center mb-25 p-15 success-transparent-alert">
                <div class="success-transparent-alert__icon d-flex align-items-center justify-content-center">
                    <i data-feather="credit-card" width="18" height="18" class=""></i>
                </div>

                <div class="ml-10">
                    <div class="font-14 font-weight-bold ">{{ trans('update.get_cashback') }}</div>
                    <div class="font-12 ">{{ trans('update.by_purchasing_this_cart_you_will_get_amount_as_cashback',['amount' => handlePrice($totalCashbackAmount)]) }}</div>
                </div>
            </div>
        @endif

        @php
            $isMultiCurrency = !empty(getFinancialCurrencySettings('multi_currency'));
            $userCurrency = currency();
            $invalidChannels = [];
        @endphp

        @if(config('app.settings.is_payment_closed'))
        <div class="text-center my-5 text-muted h4">الدفع الالكتروني متوقف الآن</div>
        @else
        <h2 class="section-title">{{ trans('financial.select_a_payment_gateway') }}</h2>

        <form action="/payments/payment-request" method="post" class=" mt-25">
            {{ csrf_field() }}
            <input type="hidden" name="order_id" value="{{ $order->id }}">

            {{-- الجزء الخاص بعرض جميع بوابات  الدفع المفعلة --}}
            <div class="row">
                @if(!empty($paymentChannels))
                    @foreach($paymentChannels as $paymentChannel)
                        @if(!isset($showPaymentChannels[$paymentChannel->class_name]) || $showPaymentChannels[$paymentChannel->class_name] === true)
                        <div class="col-6 col-lg-3 mb-40 charge-account-radio">
                            <input type="radio" name="gateway" id="{{ $paymentChannel->title }}" data-class="{{ $paymentChannel->class_name }}" value="{{ $paymentChannel->id }}">
                            <label for="{{ $paymentChannel->title }}" class="rounded-sm p-20 p-lg-45 d-flex flex-column align-items-center justify-content-center">
                                <img src="{{ $paymentChannel->image }}" height="45" alt="">

                                <p class="mt-30 mt-lg-50 font-weight-500 text-dark-blue">
                                    <span class="font-weight-bold font-14">{{ $paymentChannel->title }}</span>
                                </p>
                            </label>
                        </div>
                        @endif
                    @endforeach
                @endif

                {{--
                    <div class="col-6 col-lg-3 mb-40 charge-account-radio">
                    <input type="radio" @if(empty($userCharge) or ($total > $userCharge)) disabled @endif name="gateway" id="offline" value="credit">
                    <label for="offline" class="rounded-sm p-20 p-lg-45 d-flex flex-column align-items-center justify-content-center">
                        <img src="/assets/default/img/activity/pay.svg"  height="45" alt="">

                        <p class="mt-30 mt-lg-50 font-weight-500 text-dark-blue">
                            <span class="font-weight-bold">رصيد {{ handlePrice($userCharge) }}</span>
                        </p>
                    </label>
                </div>--}}
            </div>


            <div class="d-flex align-items-center justify-content-between mt-45">
                <span class="font-16 font-weight-500 text-gray">{{ trans('financial.total_amount') }} {{ handlePrice($total) }}</span>
                <button type="button" id="paymentSubmit" disabled class="btn btn-lg rounded btn-primary">{{ trans('public.start_payment') }}</button>
            </div>
        </form>
        @endif
        <!-- Payment by IBAN -->
        <div class="payment-by-iban">
            <div class="mb-5">
                <div class="row align-items-center">
                    <div class="col-lg-6 d-flex justify-content-center justify-content-lg-start" style="padding-bottom: 1.5rem;">
                    <h4>أو يمكنك الدفع عبر التحويل المصرفي</h4>
                    </div>
                    <div class="col-lg-6 d-flex justify-content-center justify-content-lg-end" style="padding-bottom: 1.5rem;">
                        <img src="/assets/default/img/activity/bank-jazira.svg" alt="">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 pb-4">
                    <div class="font-weight-bold text-muted mb-2">الاسم التجاري</div>
                    <div class="font-weight-bold">شركة نهج المعرفة العالي للتدريب</div>
                </div>
                <div class="col-lg-6 pb-4">
                    <div class="font-weight-bold text-muted mb-2">اسم البنك</div>
                    <div class="font-weight-bold">بنك الجزيرة</div>
                </div>
                <div class="col-lg-6 pb-4">
                    <div class="font-weight-bold text-muted mb-2">رقم الحساب</div>
                    <div class="font-weight-bold">005181558897002</div>
                </div>
                <div class="col-lg-6">
                    <div class="font-weight-bold text-muted mb-2">رقم الايبان</div>
                    <div class="font-weight-bold">SA9560100005181558897002</div>
                </div>
            </div>
            <hr style="margin: 2rem 0;">
            <div class="text-center">
                <h5 style="line-height: 1.5rem;" class="font-weight-normal"><span class="text-primary">ملاحظة:</span> بعد إتمام عملية التحويل المصرفي، يرجى التواصل بأحد الطرق أدناه لتأكيد الطلب</h5>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="row justify-content-center">
                        <div class="col-6 d-flex justify-content-center">
                            <a class="payment-by-iban-contact" href="tel:{{ $generalSettings['site_phone'] ?? '' }}">
                                <span class="icon d-inline-flex justify-content-center align-items-center"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg></span>
                                <span class="d-block">{{ $generalSettings['site_phone'] ?? '' }}</span>
                            </a>
                        </div>
                        <div class="col-6 d-flex justify-content-center">
                            <a class="payment-by-iban-contact" href="mailto:{{ $generalSettings['site_email'] ?? '' }}">
                                <span class="icon d-inline-flex justify-content-center align-items-center"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg></span>
                                <span class="d-block">{{ $generalSettings['site_email'] ?? '' }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>



@endsection

@push('scripts_bottom')
    <script src="/assets/default/js/parts/payment.min.js"></script>
@endpush
