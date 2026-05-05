<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

@php
    if (!isset($generalSettings)) {
        $generalSettings = getGeneralSettings();
    }
    $rtlLanguages = !empty($generalSettings['rtl_languages']) ? $generalSettings['rtl_languages'] : [];

    $isRtl =
        (in_array(mb_strtoupper(app()->getLocale()), $rtlLanguages) or
        !empty($generalSettings['rtl_layout']) and $generalSettings['rtl_layout'] == 1);
@endphp

<head>
    @include('web.default.includes.metas')
    <title>
        {{ isset($pageTitle) && $pageTitle ? $pageTitle . ' | ' : '' }}{{ isset($generalSettings) ? $generalSettings['site_name'] : '' }}
    </title>

    <!-- General CSS File -->
    <link rel="stylesheet"
        href="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.css?v={{ config('app.asset_ver') }}">
    <link rel="stylesheet" href="/assets/default/vendors/toast/jquery.toast.min.css?v={{ config('app.asset_ver') }}">
    <link rel="stylesheet" href="/assets/default/vendors/simplebar/simplebar.css?v={{ config('app.asset_ver') }}">
    <link rel="stylesheet" href="/assets/default/css/app.css?v={{ config('app.asset_ver') }}">

    @if ($isRtl)
        <link rel="stylesheet" href="/assets/default/css/rtl-app.css?v={{ config('app.asset_ver') }}">
    @endif

    <!-- Snap Pixel Code -->
    <script type='text/javascript'>
        (function(e, t, n) {
            if (e.snaptr) return;
            var a = e.snaptr = function() {
                a.handleRequest ? a.handleRequest.apply(a, arguments) : a.queue.push(arguments)
            };
            a.queue = [];
            var s = 'script';
            r = t.createElement(s);
            r.async = !0;
            r.src = n;
            var u = t.getElementsByTagName(s)[0];
            u.parentNode.insertBefore(r, u);
        })(window, document,
            'https://sc-static.net/scevent.min.js');

        snaptr('init', '0727db36-5e14-4959-9ee1-839bb4dc3b58', {});

        snaptr('track', 'PAGE_VIEW');
    </script>
    <!-- End Snap Pixel Code -->


    {{-- <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-16805556842"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-16805556842');
</script> --}}



    <!-- Google Tag (gtag.js) -->
    {{-- <script async src="https://www.googletagmanager.com/gtag/js?id=AW-17791157195"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'AW-17791157195');
    </script> --}}

    <!-- Google Tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-17791157195"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'AW-17791157195');
    </script>


    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-KNLSXP3Z');
    </script>
    <!-- End Google Tag Manager -->


    @stack('styles_top')
    @stack('scripts_top')

    <style>
        {!! !empty(getCustomCssAndJs('css')) ? getCustomCssAndJs('css') : '' !!} {!! getThemeFontsSettings() !!} {!! getThemeColorsSettings() !!}
    </style>


    @if (!empty($generalSettings['preloading']) and $generalSettings['preloading'] == '1')
        @include('admin.includes.preloading')
    @endif
</head>

<body class="@if ($isRtl) rtl @endif">

    <div id="app"
        class="{{ (!empty($floatingBar) and $floatingBar->position == 'top' and $floatingBar->fixed) ? 'has-fixed-top-floating-bar' : '' }}">
        @if (!empty($floatingBar) and $floatingBar->position == 'top')
            @include('web.default.includes.floating_bar')
        @endif

        @if (!isset($appHeader))
            @include('web.default.includes.top_nav')
            @include('web.default.includes.navbar')
        @endif

        @if (!empty($justMobileApp))
            @include('web.default.includes.mobile_app_top_nav')
        @endif

        @yield('content')

        @if (!isset($appFooter))
            @include('web.default.includes.footer')
        @endif

        @include('web.default.includes.advertise_modal.index')

        @if (!empty($floatingBar) and $floatingBar->position == 'bottom')
            @include('web.default.includes.floating_bar')
        @endif
    </div>
    <!-- Template JS File -->
    <script src="/assets/default/js/app.js"></script>
    <script src="/assets/default/vendors/feather-icons/dist/feather.min.js"></script>
    <script src="/assets/default/vendors/moment.min.js"></script>
    <script src="/assets/default/vendors/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/assets/default/vendors/toast/jquery.toast.min.js"></script>
    <script type="text/javascript" src="/assets/default/vendors/simplebar/simplebar.min.js"></script>

    @if (empty($justMobileApp) and checkShowCookieSecurityDialog())
        @include('web.default.includes.cookie-security')
    @endif


    <script>
        var deleteAlertTitle = '{{ trans('public.are_you_sure') }}';
        var deleteAlertHint = '{{ trans('public.deleteAlertHint') }}';
        var deleteAlertConfirm = '{{ trans('public.deleteAlertConfirm') }}';
        var deleteAlertCancel = '{{ trans('public.cancel') }}';
        var deleteAlertSuccess = '{{ trans('public.success') }}';
        var deleteAlertFail = '{{ trans('public.fail') }}';
        var deleteAlertFailHint = '{{ trans('public.deleteAlertFailHint') }}';
        var deleteAlertSuccessHint = '{{ trans('public.deleteAlertSuccessHint') }}';
        var forbiddenRequestToastTitleLang = '{{ trans('public.forbidden_request_toast_lang') }}';
        var forbiddenRequestToastMsgLang = '{{ trans('public.forbidden_request_toast_msg_lang') }}';
    </script>

    @if (session()->has('toast'))
        <script>
            (function() {
                "use strict";

                $.toast({
                    heading: '{{ session()->get('toast')['title'] ?? '' }}',
                    text: '{{ session()->get('toast')['msg'] ?? '' }}',
                    bgColor: '@if (session()->get('toast')['status'] == 'success') #43d477 @else #f63c3c @endif',
                    textColor: 'white',
                    hideAfter: 10000,
                    position: 'bottom-right',
                    icon: '{{ session()->get('toast')['status'] }}'
                });
            })(jQuery)
        </script>
    @endif

    @stack('styles_bottom')
    @stack('scripts_bottom')

    <script src="/assets/default/js/parts/main.min.js"></script>

    <script>
        @if (session()->has('registration_package_limited'))
            (function() {
                "use strict";

                handleLimitedAccountModal('{!! session()->get('registration_package_limited') !!}')
            })(jQuery)

            {{ session()->forget('registration_package_limited') }}
        @endif

        {!! !empty(getCustomCssAndJs('js')) ? getCustomCssAndJs('js') : '' !!}
    </script>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KNLSXP3Z" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <!-- NELC Entity Verification Badge -->
    <iframe
        src="https://services.futurex.sa/entity-badge?code=vCmXYwlkPGBUNtYt"
        width="250"
        height="60"
        referrerpolicy="unsafe-url"
        style="border: none; position: fixed; bottom: 0; left: 10px; z-index: 9999;"
        title="NELC Verified Entity"
    ></iframe>
    <!-- End NELC Entity Verification Badge -->
</body>

</html>
