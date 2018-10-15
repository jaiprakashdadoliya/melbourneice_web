@extends('layouts.default')

@section('title', 'Page Not Found')

@section('content')
    @php if(UserHelper::user_session_data('is_user_logged_in')){ @endphp 
    <nav class="navbar navbar-default">
      @include('includes.navigation')
    </nav>
    @php } @endphp
    <div class="container">
        <div class="page-not-found">
            <h1>404 &#9785; </h1>
            <h2>Unable to process!</h2>
            <p>Please try again.</p>
        </div>
    </div>
@endsection