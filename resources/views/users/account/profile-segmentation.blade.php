@extends('layouts.plain')

@section('content')

    <section class="tw-w-full md:tw-w-96 tw-mt-5 tw-bg-white tw-p-5 tw-rounded tw-m-auto">
        <form action="{{ url('/my-account/auth/email') }}" method="post" enctype="multipart/form-data" class="tw-grid tw-grid-cols-1 tw-gap-2">
            {{ method_field('put') }}
            {{ csrf_field() }}

            @include('users.account.parts.segmentation', ['showLanguage' => true, 'showDescription' => false])

            <div class="form-group text-right">
                <button class="button">{{ trans('common.save') }}</button>
            </div>

        </form>
    </section>

@stop
