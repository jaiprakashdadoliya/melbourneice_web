@extends('layouts.default')

@section('title', 'Forgot Password')

@section('content')
<div class="login">
  <div class="container">        
      <div class="login-logo text-center"><a href="{{ route('/') }}"><img src="{{ LOGIN_LOGO }}"></a></div>
      <h1 class="title">Welcome to the <span>Melbourne Ice Community</span></h1>
      <div class="login-form">
        <h3 class="page-title text-center">FORGOT PASSWORD</h3>
          <!-- if there are signin errors, show them here -->
          @if(Session::get('message') != '')
            <div class="alert alert-danger alert-dismissable">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              {{ Session::get('message') }}
            </div>
          @endif
          @if(Session::get('success') != '')
            <div class="alert alert-success alert-dismissable">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              {{ Session::get('success') }}
            </div>
          @endif          
          <form name="resetpassword" action="{{ route('resetpassword') }}" method="POST">
            {{ csrf_field() }}
            <div class="form-group">
              <label for="email">Email <span class="red">*</span></label>
              <input type="email" name="email" id="email" class="form-control" autocomplete="off" placeholder="Enter Email" value="{{ old('email') }}">
              <!-- <input type="email" name="email" id="email" placeholder="Email" value="{{ old('email') }}"> -->
              @if($errors->has('email'))
                <p class="validation-error">{{ trans('messages.alert_email_wrong') }}<p>
              @endif
            </div>
            <div class="form-group">
              <input type="submit" name="submit" class="btn login-btn" value="Submit">
              <div class="forget-password text-right"><a href="{{ route('/') }}">Back to Login?</a></div>
            </div>
          </form>
          <div class="text-center dont-account"><a href="{{ route('getRegister') }}">Don't have an account</a></div>
      </div>
  </div>
</div>
@endsection