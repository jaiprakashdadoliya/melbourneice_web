@extends('layouts.default')
@section('title', 'Setting')
@section('content')
	<nav class="navbar navbar-default">
      	@include('includes.navigation')
    </nav>
    <div class="container">    	
      	<h1 class="title">Welcome <span>{{ $userName = UserHelper::get_profile_name() }}!</span></h1>
	    <div class="inner-content">
	      	<div class="row">
		      	<div class="col-md-6 col-sm-6 col-xs-6"><h3 class="page-title">Change Password</h3></div>
		      	<!-- <div class="col-md-6 col-sm-6 col-xs-6 text-right"><a href=""><img src="{{ asset('public/web/images/edit.png') }}"></a></div> -->
		    </div>
		    <p>
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
	        </p>
	      	<form name="settings" method="POST" action="{{ route('setting') }}">
	      		{{ csrf_field() }}
		      	<div class="row">
			        <div class="col-md-4 form-group">
			        	<label for="password">Password <span class="red">*</span></label>
            			<input type="password" name="password" id="password" class="form-control" autocomplete="false" placeholder="Enter Password">
			        	<!-- <input type="password" name="password" id="password" placeholder="Password *"> -->
			        	@if($errors->has('password'))
		                  <p class="validation-error">{{ $errors->first('password') }}<p>
		                @endif
			        </div>
			        <div class="col-md-4 form-group">
			        	<label for="new_passsword">New Password <span class="red">*</span></label>
            			<input type="password" name="new_passsword" id="new_passsword" class="form-control" autocomplete="false" placeholder="Enter New Password">
			        	<!-- <input type="password" name="new_passsword" id="new_passsword" placeholder="New Password *"> -->
			        	@if($errors->has('new_passsword'))
		                  <p class="validation-error">{{ $errors->first('new_passsword') }}<p>
		                @endif
		        	</div>
			        <div class="col-md-4 form-group">
			        	<label for="confirm_password">Confirm Password <span class="red">*</span></label>
            			<input type="password" name="confirm_password" id="confirm_password" class="form-control" autocomplete="false" placeholder="Enter Confirm Password">
			        	<!-- <input type="password" name="confirm_password" id="confirm_password" value="" placeholder="Confirm Password *"> -->
			        	@if($errors->has('confirm_password'))
		                  <p class="validation-error">{{ $errors->first('confirm_password') }}<p>
		                @endif
			        </div>
		      	</div>
		      	<div class="row form-group">		      		
                  	<p class="warning col-md-12"><i>Note: Password must have a minimum of 8 characters and contain at least one lower case letter, one upper case letter, one number and one special character.</i></p>
		      	</div>
		      	
		      	<div class="row form-group">
			        <div class="col-md-12 text-right"><button class="btn blue">Update</button></div>
		      	</div>
		    </form>
	    </div>
  	</div>
@endsection