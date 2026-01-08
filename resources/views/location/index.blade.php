@extends('layouts.app')
@push('style')
    <style>
    </style>
@endpush
@section('content')
    {{-- @include('location.components.sso-loader') --}}

    {{-- @include('location.components.sso-error') --}}

    <div id="main-content" class="container-fluid px-3">
        <div class="">
        </div>
    </div>
@endsection

@push('script')
    @include('components.copyUrlScript')
    @include('components.submitForm')

    <script>
        // $(document).ready(function() {
        //     $('#sso-loader').fadeOut(300);
        // })

    </script>

    {{-- @include('location.components.sso-script') --}}
@endpush
