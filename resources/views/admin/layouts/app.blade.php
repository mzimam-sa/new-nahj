<html lang="{{ app()->getLocale() }}">
@php
    $rtlLanguages = !empty($generalSettings['rtl_languages']) ? $generalSettings['rtl_languages'] : [];


    $isRtl = ((in_array(mb_strtoupper(app()->getLocale()), $rtlLanguages)) or (!empty($generalSettings['rtl_layout']) and $generalSettings['rtl_layout'] == 1));
@endphp




<head>
    <!-- FontAwesome 4.7 for star icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>{{ $pageTitle ?? '' }} </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- General CSS File -->
    <link rel="stylesheet" href="/assets/admin/vendor/bootstrap/bootstrap.min.css?v={{ config('app.asset_ver') }}"/>
    <link rel="stylesheet" href="/assets/vendors/fontawesome/css/all.min.css?v={{ config('app.asset_ver') }}"/>
    <link rel="stylesheet" href="/assets/default/vendors/toast/jquery.toast.min.css">




    @stack('libraries_top')


    <link rel="stylesheet" href="/assets/admin/css/style.css?v={{ config('app.asset_ver') }}">
    <link rel="stylesheet" href="/assets/admin/css/custom.css?v={{ config('app.asset_ver') }}">
    <link rel="stylesheet" href="/assets/admin/css/components.css?v={{ config('app.asset_ver') }}">
    @if($isRtl)
        <link rel="stylesheet" href="/assets/admin/css/rtl.css?v={{ config('app.asset_ver') }}">
    @endif
    <link rel="stylesheet" href="/assets/admin/vendor/daterangepicker/daterangepicker.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900;1000&display=swap" rel="stylesheet">


    @stack('styles_top')
    @stack('scripts_top')


    <style>
        {!! !empty(getCustomCssAndJs('css')) ? getCustomCssAndJs('css') : '' !!}


        {!! getThemeColorsSettings(true) !!}
    </style>
</head>
<body class="@if($isRtl) rtl @endif">
<!-- {{-- DEBUG مؤقت --}}
<div style="background:#333; color:white; padding:5px; font-size:12px;">
    impersonated: {{ session()->get('impersonated', 'NULL') }} |
    impersonator_id: {{ session()->get('impersonator_id', 'NULL') }} |
    auth id: {{ auth()->id() }}
</div> -->


@if(session()->has('impersonated'))
    <div style="background:#e74c3c; color:white; padding:8px; text-align:center; position:sticky; top:0; z-index:9999;">
        أنت تتصفح الآن كـ: <strong>{{ auth()->user()->full_name }}</strong>
        <a href="{{ getAdminPanelUrl() }}/users/stop-impersonate"
           style="color:white; font-weight:bold; margin-right:15px; text-decoration:underline;">
            ← إنهاء والعودة لحسابك
        </a>
    </div>
@endif


<div id="app">
    <div class="main-wrapper">
        @include('admin.includes.navbar')


        @include('admin.includes.sidebar')




        <div class="main-content">


            @yield('content')


        </div>
    </div>


    <div class="modal fade" id="fileViewModal" tabindex="-1" aria-labelledby="fileViewModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <div class="modal-body">
                    <img src="" class="w-100" height="350px" alt="">
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('public.close') }}</button>
                </div>
            </div>
        </div>
    </div>


</div>


{{-- AI Contents --}}
@if(!empty(getAiContentsSettingsName("status")) and !empty(getAiContentsSettingsName("active_for_admin_panel")))
    @include('admin.includes.aiContent.generator')
@endif




<!-- General JS Scripts -->
<script src="/assets/admin/vendor/jquery/jquery-3.3.1.min.js"></script>
<script src="/assets/admin/vendor/poper/popper.min.js"></script>
<script src="/assets/admin/vendor/bootstrap/bootstrap.min.js"></script>
<script src="/assets/admin/vendor/nicescroll/jquery.nicescroll.min.js"></script>
<script src="/assets/admin/vendor/moment/moment.min.js"></script>
<script src="/assets/admin/js/stisla.js"></script>
<script src="/assets/default/vendors/toast/jquery.toast.min.js"></script>


<script>
    (function () {
        "use strict";


        window.csrfToken = $('meta[name="csrf-token"]');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        window.adminPanelPrefix = '{{ getAdminPanelUrl() }}';


        @if(session()->has('toast'))
        $.toast({
            heading: '{{ session()->get('toast')['title'] ?? '' }}',
            text: '{{ session()->get('toast')['msg'] ?? '' }}',
            bgColor: '@if(session()->get('toast')['status'] == 'success') #43d477 @else #f63c3c @endif',
            textColor: 'white',
            hideAfter: 10000,
            position: 'bottom-right',
            icon: '{{ session()->get('toast')['status'] }}'
        });
        @endif
    })(jQuery);
</script>


<script src="/assets/admin/vendor/daterangepicker/daterangepicker.min.js"></script>
<script src="/assets/default/vendors/select2/select2.min.js"></script>


<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<!-- Template JS File -->
<script src="/assets/admin/js/scripts.js"></script>


@stack('styles_bottom')
@stack('scripts_bottom')


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
    var generatedContentLang = '{{ trans('update.generated_content') }}';
    var copyLang = '{{ trans('public.copy') }}';
    var doneLang = '{{ trans('public.done') }}';
</script>


<script src="/assets/admin/js/custom.js"></script>
<script src="/assets/default/js/panel/ai-content-generator.min.js"></script>


<script>
    {!! !empty(getCustomCssAndJs('js')) ? getCustomCssAndJs('js') : '' !!}
</script>
</body>
</html>

