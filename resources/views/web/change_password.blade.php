@extends('layouts.default')
@section('title', 'Forgot Password')
@section('content')
<div class="login">
  <div class="container">      
      <div class="login-logo text-center"><img src="{{ CHANGE_PASSWORD_PAGE_LOGIN_LOGO }}"></div>
      <h1 class="title">Welcome to the <span>Melbourne Ice Community</span></h1>
      <div class="login-form">
        <h3 class="page-title text-center">UPDATE PASSWORD</h3>
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
        <form class="row" name="resetpassword" action="{{ route('savePassword') }}" method="POST">
          {{ csrf_field() }}
          <div class="col-md-12 form-group">
            <div class="password-alert">
            <label for="new_password">New Password <span class="red">*</span></label>
            <div class="password-info">
            <input type="password" name="new_password" id="new_password" class="form-control register_form" autocomplete="off" placeholder="Enter Password" value="{{ old('new_password') }}"><img class="pull-right reg_password" src="{{ CHANGE_PWD_INFO_PATH }}">
            <div class="password-msg update-password">Password must have a minimum of 8 characters and contain at least one lower case letter, one upper case letter, one number and one special character.</div>
          </div>
        </div>

            <!-- <input type="password" name="new_password" id="new_password" placeholder="New Password" value="{{ old('new_password') }}"> -->
            @if($errors->has('new_password'))
              <p class="validation-error">{{ $errors->first('new_password') }}<p>
            @endif
            
          </div>
          <div class="col-md-12 form-group">
            
              <label for="password_confirmation">Confirm Password <span class="red">*</span></label>
              
              <input type="password" name="password_confirmation" id="password_confirmation" class="form-control register_form" autocomplete="off" placeholder="Enter Confirm Password" value="{{ old('password_confirmation') }}">
            
            <!-- <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" value="{{ old('password_confirmation') }}"> -->
            @if($errors->has('password_confirmation'))
              <p class="validation-error">{{ trans('messages.password_not_match') }}<p>
            @endif
          </div>
          <div class="col-md-12">
            <input type="hidden" name="id" id="id" value="{{ $id }}">
            <input type="hidden" name="token" id="token" value="{{ $token }}">
          </div>
          <div class="col-md-12 form-group">
            <input type="submit" name="submit" class="btn login-btn" value="Submit">
          </div>
        </form>
      </div>
  </div>
</div>
@endsection