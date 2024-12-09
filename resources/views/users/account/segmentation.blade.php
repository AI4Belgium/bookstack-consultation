@extends('users.account.layout')

@section('main')

    <section class="card content-wrap auto-height">
        <form action="{{ url('/segmentation') }}" method="post" enctype="multipart/form-data">
            {{ method_field('put') }}
            {{ csrf_field() }}

            @include('users.account.profile-segmentation')

        </form>
    </section>

@stop
