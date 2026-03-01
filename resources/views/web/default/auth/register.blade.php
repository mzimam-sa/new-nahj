@extends(getTemplate() . '.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
    <link rel="stylesheet" href="/assets/default/vendors/daterangepicker/daterangepicker.min.css">
@endpush

@section('content')
    @php
        $registerMethod = getGeneralSettings('register_method') ?? 'mobile';
        $showOtherRegisterMethod = getFeaturesSettings('show_other_register_method') ?? false;
        $showCertificateAdditionalInRegister = getFeaturesSettings('show_certificate_additional_in_register') ?? false;
        $selectRolesDuringRegistration = getFeaturesSettings('select_the_role_during_registration') ?? null;
    @endphp

    <div class="container">
        <form method="post" action="/register" class="mt-35">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="row">

                <div class="col-lg-12">
                    <h1 class="font-20 font-weight-bold text-center" style="margin-top: 50px; margin-bottom: 50px;">
                        {{ trans('auth.signup') }}
                    </h1>
                </div>


                <div class="col-6">
                    
                    {{-- الاسم --}}
                    <!--<div class="form-group">-->
                    <!--    <label class="input-label" for="full_name">{{ trans('auth.full_name') }}:</label>-->
                    <!--    <input name="full_name" type="text" value="{{ old('full_name') }}"-->
                    <!--        class="form-control @error('full_name') is-invalid @enderror">-->
                    <!--    @error('full_name')-->
                    <!--        <div class="invalid-feedback">-->
                    <!--            {{ $message }}-->
                    <!--        </div>-->
                    <!--    @enderror-->
                    <!--</div>-->
                    
                      <div class="row lg-12 ">
                        {{-- الاسم --}}
                    

                        <div class="form-group p-20">
                            <label class="input-label" for="first_name">{{ trans('auth.first_name') }}</label>
                            <input name="first_name" required type="text" value="{{ old('first_name') }}"
                                class="form-control @error('first_name') is-invalid @enderror">
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    

                        <div class="form-group p-20">
                            <label class="input-label" for="middle_name"> {{ trans('auth.middle_name') }}</label>
                            <input name="middle_name" required type="text" value="{{ old('middle_name') }}"
                                class="form-control @error('middle_name') is-invalid @enderror">
                            @error('middle_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    {{-- رقم الهوية الوطنية وهو الحقل الاضافي الموجود في اعدادات الادمن خانة الخصائص--}}
                    @if ($showCertificateAdditionalInRegister)
                        <div class="form-group">
                            <label class="input-label"
                                for="certificate_additional">{{ trans('update.certificate_additional') }}</label>
                            <input name="certificate_additional"  maxlength="10" id="certificate_additional"
                                class="form-control @error('certificate_additional') is-invalid @enderror" />
                            @error('certificate_additional')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @endif

                    {{-- كلمة المرور --}}
                    <div class="form-group">
                        <label class="input-label" for="password">{{ trans('auth.password') }}:</label>
                        <input name="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" aria-describedby="passwordHelp">
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- وحدة زمنية --}}
                    @if (getFeaturesSettings('timezone_in_register'))
                        @php
                            $selectedTimezone = getGeneralSettings('default_time_zone');
                        @endphp

                        <div class="form-group">
                            <label class="input-label">{{ trans('update.timezone') }}</label>
                            <select name="timezone" class="form-control select2" data-allow-clear="false">
                                <option value="" {{ empty($user->timezone) ? 'selected' : '' }} disabled>
                                    {{ trans('public.select') }}</option>
                                @foreach (getListOfTimezones() as $timezone)
                                    <option value="{{ $timezone }}" @if ($selectedTimezone == $timezone) selected @endif>
                                        {{ $timezone }}</option>
                                @endforeach
                            </select>
                            @error('timezone')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @endif


                    <div class="form-group">
                        <label class="input-label" for="educational_qualification">{{ trans('auth.educational_qualification') }}</label>
                        <input name="educational_qualification" id="educational_qualification" class="form-control" />
                        @error('educational_qualification')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>


                </div>


                <div class="col-6">
                    
                        <div class="form-group">
                            <label class="input-label" for="last_name">{{ trans('auth.last_name') }}</label>
                            <input name="last_name" required type="text" value="{{ old('last_name') }}"
                                class="form-control @error('last_name') is-invalid @enderror">
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    {{-- الجوال والايميل --}}
                    @if ($registerMethod == 'mobile')
                        @include('web.default.auth.register_includes.mobile_field')
                        @if ($showOtherRegisterMethod)
                            @include('web.default.auth.register_includes.email_field', [
                                'optional' => false,
                            ])
                        @endif
                    @else
                        @include('web.default.auth.register_includes.email_field')
                        @if ($showOtherRegisterMethod)
                            @include('web.default.auth.register_includes.mobile_field', [
                                'optional' => false,
                            ])
                        @endif
                    @endif

                    {{-- تأكيد كلمة المرور --}}
                    <div class="form-group ">
                        <label class="input-label" for="confirm_password">{{ trans('auth.retype_password') }}:</label>
                        <input name="password_confirmation" type="password"
                            class="form-control @error('password_confirmation') is-invalid @enderror" id="confirm_password"
                            aria-describedby="confirmPasswordHelp">
                        @error('password_confirmation')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- رمز الدعوة --}}
                    @if (!empty($referralSettings) and $referralSettings['status'])
                        <div class="form-group">
                            <label class="input-label" for="referral_code">{{ trans('financial.referral_code') }} (اختياري):</label>
                            <input name="referral_code" type="text"
                                class="form-control @error('referral_code') is-invalid @enderror" id="referral_code"
                                value="{{ !empty($referralCode) ? $referralCode : old('referral_code') }}"
                                aria-describedby="confirmPasswordHelp">
                            @error('referral_code')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @endif

                </div>

                <div class="js-form-fields-card">
                    @if (!empty($formFields))
                        {!! $formFields !!}
                    @endif
                </div>

                @if (!empty(getGeneralSecuritySettings('captcha_for_register')))
                    @include('web.default.includes.captcha_input')
                @endif

                <div class="custom-control custom-checkbox" style="margin: auto; top: 30px;">
                    <input type="checkbox" name="term" value="1"
                        {{ (!empty(old('term')) and old('term') == '1') ? 'checked' : '' }}
                        class="custom-control-input @error('term') is-invalid @enderror" id="term">
                    <label class="custom-control-label font-14" for="term">{{ trans('auth.i_agree_with') }}
                        <a href="pages/terms" target="_blank"
                            class="text-secondary font-weight-bold font-14">{{ trans('auth.terms_and_rules') }}</a>
                    </label>

                    @error('term')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                @error('term')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

                <div class="col-lg-12">
                    <div class="text-center" style="margin-top: 50px; margin-bottom: 50px;">
                        <button type="submit" class="btn btn-primary btn-block mt-20 rounded-pill" style="width: 50%;">
                            {{ trans('auth.signup') }}
                        </button>
                    </div>

                </div>


            </div>
        </form>

        <div class="text-center mt-20">
            <span class="text-secondary">
                {{ trans('auth.already_have_an_account') }}
                <a href="/login" class="text-secondary font-weight-bold">{{ trans('auth.login') }}</a>
            </span>
        </div>

    </div>


    {{-- التصميم القديم --}}
    {{--
        <div class="container">
            <div class="row login-container">
                <div class="col-12 col-md-6">
                    <div class="login-card">
                        @if (!empty($selectRolesDuringRegistration) and count($selectRolesDuringRegistration))
                            <div class="form-group">
                                <label class="input-label">{{ trans('financial.account_type') }}</label>

                                <div class="d-flex align-items-center wizard-custom-radio mt-5">
                                    <div class="wizard-custom-radio-item flex-grow-1">
                                        <input type="radio" name="account_type" value="user" id="role_user" class=""
                                            checked>
                                        <label class="font-12 cursor-pointer px-15 py-10"
                                            for="role_user">{{ trans('update.role_user') }}</label>
                                    </div>

                                    @foreach ($selectRolesDuringRegistration as $selectRole)
                                        <div class="wizard-custom-radio-item flex-grow-1">
                                            <input type="radio" name="account_type" value="{{ $selectRole }}"
                                                id="role_{{ $selectRole }}" class="">
                                            <label class="font-12 cursor-pointer px-15 py-10"
                                                for="role_{{ $selectRole }}">{{ trans('update.role_' . $selectRole) }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    --}}

@endsection

@push('scripts_bottom')
    <script src="/assets/default/vendors/select2/select2.min.js"></script>
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <script src="/assets/default/js/parts/forms.min.js"></script>
    <script src="/assets/default/js/parts/register.min.js"></script>
      <script>
document.querySelector('form').addEventListener('submit', function(e) {
    const emailInput = document.getElementById('email');
    const email = emailInput.value.trim();
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if(email && !regex.test(email)) {
        e.preventDefault(); // منع الإرسال
        emailInput.classList.add('is-invalid');

        let feedback = emailInput.nextElementSibling;
        if(!feedback || !feedback.classList.contains('invalid-feedback')){
            feedback = document.createElement('div');
            feedback.classList.add('invalid-feedback');
            emailInput.after(feedback);
        }
        feedback.textContent = "يرجى إدخال بريد إلكتروني صالح";
    } else {
        emailInput.classList.remove('is-invalid');
        if(emailInput.nextElementSibling && emailInput.nextElementSibling.classList.contains('invalid-feedback')){
            emailInput.nextElementSibling.textContent = "";
        }
    }
});
</script>
@endpush
