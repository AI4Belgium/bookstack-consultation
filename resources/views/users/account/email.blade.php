@extends('layouts.plain')

@section('content')

    <section class="card content-wrap auto-height mt-l">
        <form action="{{ url('/my-account/auth/email') }}" method="post" enctype="multipart/form-data">
            {{ method_field('put') }}
            {{ csrf_field() }}

            <div class="setting-list">

                <div>
                    <div class="flex-container-row gap-l items-center wrap">
                        <div class="flex">
                            <label class="setting-list-label" for="email">{{ trans('auth.email') }}</label>
                            <p class="text-small mb-none">{{ trans('preferences.profile_email_desc') }}</p>
                        </div>
                        <div class="flex stretch-inputs">
                            @include('form.text', ['name' => 'email'])
                        </div>
                    </div>

                    <div class="flex-container-row gap-l items-center wrap">
                        <div class="flex">
                            <label class="setting-list-label" for="email-confirm">{{ trans('auth.email') }}</label>
                            <p class="text-small mb-none"></p>
                        </div>
                        <div class="flex stretch-inputs">
                            @include('form.text', ['name' => 'email-confirm'])
                        </div>
                    </div>

                </div>
            </div>

            <div class="form-group text-right">
                <button class="button">{{ trans('common.save') }}</button>
            </div>

        </form>
    </section>

@stop
