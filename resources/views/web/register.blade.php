@extends('layouts.default')
@section('title', 'Register')
@section('content')
  <div class="login">
    <div class="container">
      <div class="login-logo text-center"><a href="{{ route('/') }}"><img src="{{ LOGIN_LOGO }}"></a></div>
      <h1 class="title">Welcome to the <span>Melbourne Ice Community</span></h1>
      <div class="login-form">
        <h3 class="page-title text-center">REGISTER</h3>
        <form name="register" action="{{ route('postRegister') }}" method="POST" autocomplete="false">
        {{ csrf_field() }}      
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
          <div class="row">
            <div class="col-md-6 form-group">
              <label for="firstName">First Name <span class="red">*</span></label>
              <input type="text" class="register_form form-control" id="firstName" autocomplete="false" name="firstName" placeholder="Enter First Name" value="{{ old('firstName') }}">
              @if($errors->has('firstName'))
                <p class="validation-error">{{ $errors->first('firstName') }}<p>
              @endif
            </div>
            <div class="col-md-6 form-group">
              <label for="lastName">Last Name <span class="red">*</span></label>
              <input type="text" name="lastName" id="lastName" class="register_form form-control" autocomplete="false" placeholder="Enter Last Name" value="{{ old('lastName') }}">
              @if($errors->has('lastName'))
                <p class="validation-error">{{ $errors->first('lastName') }}<p>
              @endif
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 form-group">
                <div class="password-alert">
                  <label for="password">Password <span class="red">*</span></label>
                  <div class="password-info">
                <input type="password" name="password" id="password" class="register_form form-control" autocomplete="user-password" placeholder="Enter Password" value="{{ old('password') }}">
                <img class="pull-right reg_password" src="{{ CHANGE_PWD_INFO_PATH }}">
              </div>
                @if($errors->has('password'))
                  <p class="validation-error">{{ $errors->first('password') }}</p>
                @endif
                <div class="password-msg">Password must have a minimum of 8 characters and contain at least one lower case letter, one upper case letter, one number and one special character.</div>
              </div>
            </div>
            <div class="col-md-6 form-group">
              <label for="password_confirmation">Confirm Password <span class="red">*</span></label>
              <input type="password" name="password_confirmation" id="password_confirmation" class="register_form form-control" autocomplete="user-password" placeholder="Enter Confirm Password" value="{{ old('password_confirmation') }}">
              @if($errors->has('password_confirmation'))
                <p class="validation-error">{{ trans('messages.password_not_match') }}</p>
              @endif
              <!-- @if($errors->has('password_confirmation'))
                <p class="validation-error">{{ $errors->first('password_confirmation') }}<p>
              @endif -->
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 form-group">
              <label for="email">Email <span class="red">*</span></label>
              <input type="email" name="email" id="email" class="form-control" autocomplete="false" placeholder="Enter Email" value="{{ old('email') }}">
              <!--   -->
              @if($errors->has('email'))
                <p class="validation-error">{{ $errors->first('email') }}</p>
              @endif
            </div>
           </div>
          <div class="row">
            <div class="col-md-12 form-group">
              <input type="submit" name="submit" class="btn login-btn register_form" value="Register">
            </div>
          </div>
        </form>
        <div class="text-center dont-account"><a href="{{ route('/') }}">Already have an account</a></div>
      </div>
    </div>
  </div> 
  <script type="text/javascript">
$("input").focus(function(){
  $("input").parents('.form-group').removeClass("is-focused");
    $(this).parents('.form-group').toggleClass("is-focused");
});
$("input").focusout(function(){
   $(this).parents('.form-group').toggleClass("is-focused");
});
  </script>
@endsection