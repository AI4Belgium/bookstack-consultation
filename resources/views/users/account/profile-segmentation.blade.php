@extends('layouts.simple')

@section('content')

    <section class="tw-w-full sm:tw-w-96 md:tw-w-[800px] tw-mt-5 tw-bg-white tw-p-5 tw-rounded tw-m-auto">
        <form action="{{ url('/segmentation') }}" method="post" enctype="multipart/form-data" class="tw-grid tw-grid-cols-1 tw-gap-2">
            {{ method_field('put') }}
            {{ csrf_field() }}

            @include('users.account.parts.segmentation', ['showLanguage' => true, 'showDescription' => false])

            <div>
                {{trans('segmentation.notice')}}
            </div>

        </form>
    </section>

@stop
