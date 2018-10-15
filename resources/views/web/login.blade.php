@extends('layouts.default')
@section('title', 'Login')
@section('content')
  <div class="login">
    <div class="container">
        <div class="login-logo text-center"><a href="{{ route('/') }}"><img src="{{ LOGIN_LOGO }}"></a></div>
        <h1 class="title">Welcome to the <span>Melbourne Ice Community</span></h1>
        <div class="login-form">
          <h3 class="page-title text-center">LOGIN</h3>
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
          <form name="login" action="{{ route('login') }}" method="POST">
            {{ csrf_field() }}
            <div class="form-group">
              <label for="email">Email <span class="red">*</span></label>
              <input type="email" name="email" id="email" class="form-control" autocomplete="off" placeholder="Enter Email" value="{{ old('email') }}">
              <!-- <input type="email" name="email" id="email" placeholder="Email *" value="{{ old('email') }}">   -->
              @if($errors->has('email'))
                <p class="validation-error">{{ trans('messages.alert_email_wrong') }}<p>
              @endif              
            </div>
            <div class="form-group">
              <label for="password">Password <span class="red">*</span></label>
              <input type="password" name="password" id="password" class="form-control" autocomplete="off" placeholder="Enter Password" value="{{ old('password') }}">
              <!-- <input type="password" name="password" id="password" placeholder="Password *" value="{{ old('password') }}"> -->
              @if($errors->has('password'))            
                <p class="validation-error">{{ trans('messages.alert_password_wrong') }}<p>
              @endif              
            </div>
            <div class="form-group">
              <input type="submit" name="submit" value="Login" class="btn login-btn"> 
              <div class="forget-password text-right"><a href="{{ route('forget-password') }}">First time login or forgot password</a></div>
            </div>
            <div class="text-center dont-account"><a href="{{ route('getRegister') }}">Don’t have an account (not an Melbourne Ice member)</a></div>
              <!-- <div class="or"><span>OR</span></div> -->
              <div class="row">
               <!--  <div class="col-sm-6 col-md-6"><a href="{{ url('redirect/facebook') }}" class="facebook">Login with Facebook</a></div>
                <div class="col-md-6 col-sm-6 "><a href="{{ url('redirect/google') }}" class="googleplus">Login with Google+</a></div> -->
              </div>
          </form>
        </div>
    </div>
  </div>
@endsection