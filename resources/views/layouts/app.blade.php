@extends('layouts.clean')

@section('childContent')
    @include('layouts.partial.header')

    <!-- Main Content -->
    @yield('content')

    @include('layouts.partial.footer')
    
    <!-- AI Chatbot -->
    @include('components.chatbot')
@endsection
