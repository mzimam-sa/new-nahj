@php
    $socials = getSocials();
    if (!empty($socials) and count($socials)) {
        $socials = collect($socials)
            ->sortBy('order')
            ->toArray();
    }

    $footerColumns = getFooterColumns();
@endphp

<footer class="footer bg-secondary position-relative user-select-none">
    <div class="container">
        <div class="row">
            {{-- <div class="col-12">
                <div class=" footer-subscribe d-block d-md-flex align-items-center justify-content-between">
                    <div class="flex-grow-1">
                        <strong>{{ trans('footer.join_us_today') }}</strong>
                        <span class="d-block mt-5 text-white">{{ trans('footer.subscribe_content') }}</span>
                    </div>
                    <div class="subscribe-input bg-white p-10 flex-grow-1 mt-30 mt-md-0">
                        <form action="/newsletters" method="post">
                            {{ csrf_field() }}

                            <div class="form-group d-flex align-items-center m-0">
                                <div class="w-100">
                                    <input type="text" name="newsletter_email" class="form-control border-0 @error('newsletter_email') is-invalid @enderror" placeholder="{{ trans('footer.enter_email_here') }}"/>
                                    @error('newsletter_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary rounded-pill">{{ trans('footer.join') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>

    @php
        $columns = ['first_column', 'second_column', 'third_column'];
    @endphp

    <div class="container">
        <div class="row">

            <div class="col-12 col-md-3">

                <div class="mt-20 fend-element">
                    <p class="d-block text-white font-weight-bold font-24">نهج المعرفة</p>
                    <br>
                    <div class="location-container" style="color: white">
                        <i data-feather="map-pin" width="20" height="20" class="mr-10"
                           style="vertical-align: middle;"></i>
                        <p class="font-weight-500 font-14 pt-0" style="display: inline-block; vertical-align: middle;">
                            مكة المكرمة - الشرائع
                        </p>
                    </div>
                </div>
                <br>


                <div class="d-flex align-items-center text-white font-14">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-phone ml-10">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                    <a href="tel:{{ $generalSettings['site_phone'] ?? '' }}" style="color: white">
                        {{ $generalSettings['site_phone'] ?? '' }}
                    </a>

                    <div class="border-left mx-5 mx-lg-15 h-100"></div>

                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-mail ml-10">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    <a href="mailto:{{ $generalSettings['site_email'] ?? '' }}" style="color: white">
                        {{ $generalSettings['site_email'] ?? '' }}
                    </a>
                </div>


            </div>

            <div class="col-6 col-md-2">

                <div class="mt-20 fend-element">
                    <p>
                        <a href="/">
                            <span style="color: #ffffff;">الرئيسية</span>
                        </a>
                    </p>


                    <p>
                        <a href="/categories/qualifying-courses">
                            <span style="color: #ffffff;">الدورات التأهيلية</span>
                        </a>
                    </p>

                    <p>
                        <a href="/categories/development-courses">
                            <span style="color: #ffffff;">الدورات التطويرية</span>
                        </a>
                    </p>
                    <p>
                        <a href="/categories/قسم-الدبلومات">
                            <span style="color: #ffffff;">الدبلومات</span>
                        </a>
                    </p>
                    <p>
                        <a href="/forms/book-test">
                            <span style="color: #ffffff;">حجز اختبار</span>
                        </a>
                    </p>
                </div>

            </div>

            <div class="col-6 col-md-2">

                <div class="mt-20 fend-element">
                    <p>
                        <a href="/pages/about">
                            <span style="color: #ffffff;">عن نهج المعرفة</span>
                        </a>
                    </p>

                    <p>
                        <a href="/certificate_validation">
                            <span style="color: #ffffff;">التحقق من الشهادة</span>
                        </a>
                    </p>

                    <p>
                        <a href="/pages/terms">
                            <span style="color: #ffffff;">شروط التسجيل</span>
                        </a>
                    </p>


                    <p>
                        <a href="/contact">
                            <span style="color: #ffffff;">اتصل بنا</span>
                        </a>
                    </p>

                </div>

            </div>

            <div class="col-6 col-md-3">

                <div class="mt-20 fend-element">
                    <p>
                        <a href="/pages/privacy-policy">
                            <span style="color: #ffffff;">سياسة الخصوصية</span>
                        </a>
                    </p>
                    <p>
                        <a href="/pages/electronic-attendance-policy">
                            <span style="color: #ffffff;">سياسة الحضور الإلكتروني</span>
                        </a>
                    </p>
                    <p>
                        <a href="/pages/communication-policy-between-beneficiaries-in-the-e-learning-environment">
                            <span style="color: #ffffff;">سياسة التواصل بين المستفيدين في بيئة التعليم الإلكتروني</span>
                        </a>
                    </p>
                    <p>
                        <a href="/pages/integrity-policy">
                            <span style="color: #ffffff;">سياسة النزاهة</span>
                        </a>
                    </p>


                </div>

            </div>

            <div class="col-6 col-md-2">

                <div class="mt-20 fend-element">
                    <p>
                        <a href="https://nahj.com.sa/%D8%A7%D9%84%D9%85%D8%AA%D8%AF%D8%B1%D8%A8.pdf" target="_blank">
                            <span style="color: #ffffff;">دليل المتدرب</span>
                        </a>
                    </p>
                    <p>
                        <a target="_blank" href="https://nahj.com.sa/%D8%A7%D9%84%D9%85%D8%AF%D8%B1%D8%A8.pdf">
                            <span style="color: #ffffff;">دليل المدرب</span>
                        </a>
                    </p>
                    <p>
                        <a href="https://nahj.com.sa/contact#support">
                            <span style="color: #ffffff;">الدعم الفني</span>
                        </a>
                    </p>
                </div>

            </div>

            <div class="col-12 col-md-3">

                <div class="mt-20">
                    <div class="footer-social" style="margin-top: 40px">
                        @if (!empty($socials) and count($socials))
                            @foreach ($socials as $social)
                                <a href="{{ $social['link'] }}" class="smlink">
                                    <img src="{{ $social['image'] }}" alt="{{ $social['title'] }}" class="mr-15">
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{--
        @if (getOthersPersonalizationSettings('platform_phone_and_email_position') == 'footer')
        <div class="footer-copyright-card">
            <div class="container d-flex align-items-center justify-content-between py-15">
                <div class="font-14 text-white">{{ trans('update.platform_copyright_hint') }}</div>

                <div class="d-flex align-items-center justify-content-center">
                    @if (!empty($generalSettings['site_phone']))
                        <div class="d-flex align-items-center text-white font-14">
                            <i data-feather="phone" width="20" height="20" class="ml-10"></i>
                            {{ $generalSettings['site_phone'] }}
                        </div>
                    @endif

                    @if (!empty($generalSettings['site_email']))
                        <div class="border-left mx-5 mx-lg-15 h-100"></div>

                        <div class="d-flex align-items-center text-white font-14">
                            <i data-feather="mail" width="20" height="20" class="ml-10"></i>
                            {{ $generalSettings['site_email'] }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    --}}

    <br>
    <br>
</footer>
