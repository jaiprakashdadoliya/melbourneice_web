@extends('layouts.default')
@section('title', 'Fees')
@section('content')
	<nav class="navbar navbar-default">
      	@include('includes.navigation')
    </nav>
    <div class="container">    	
      	<h1 class="title">Welcome <span>{{ $userName = UserHelper::get_profile_name() }}!</span></h1>
	    <div class="inner-content">
	      	<div class="row">
      			<div class="col-md-6 col-sm-6 col-xs-6"><h3 class="page-title">MI Fees</h3></div>
  			</div>
  			@if(Session::get('feesSuccess') != '')
	            <div class="alert alert-success alert-dismissable">
	            	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	              {{ Session::get('feesSuccess') }}
	          	</div>
          	@endif
          	@if(Session::get('message') != '')
	            <div class="alert alert-success alert-dismissable">
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
	        <form name="addFees" id="addFees" method="POST" action="{{ route('updateFees') }}">
	      		{{ csrf_field() }}
		      	<div class="row">		      		
		      		<div class="col-md-4 form-group">
	      				<label for="membership_fees">Membership Fees ($)<span class="red">*</span></label>
	      				<input type="text" name="membershipFees" id="membershipFees" class="form-control" placeholder="Enter membership fees" @if(!empty($user_details)) { value="{{ $user_details->membershipFees}}" } @else { value="{{ $user_details->membershipFees}}" } @endif >
	      				@if($errors->first('membershipFees') == "The selected membership fees is invalid.")
	                  		<p class="validation-error">The entered fee is invalid.<p>
	                  	@else
	                  		<p class="validation-error">{{ $errors->first('membershipFees') }}<p>
                  		@endif
		      		</div>

		      		<div class="col-md-4 form-group">
	      				<label for="serviceFees">Service Fees ($)<span class="red">*</span></label>
	      				<input type="text" name="serviceFees" id="serviceFees" class="form-control" placeholder="Enter paid fees" @if(!empty($user_details)) { value="{{ $user_details->paidFees}}" } @else { value="{{ $user_details->paidFees}}" } @endif >
	      				@if($errors->has('serviceFees'))
	      					@if($errors->first('serviceFees') == "The selected service fees is invalid.")
		                  		<p class="validation-error">The entered service fee is invalid.<p>
		                  	@else
		                  		<p class="validation-error">{{ $errors->first('serviceFees') }}<p>
	                  		@endif
		                @endif
		      		</div>
		      		
		      	</div>
		      	<div class="row form-group">
			        <div class="col-md-12 text-right"><button class="btn blue">Update</button></div>
		      	</div>
	      	</form>
	    </div>  			
  	</div>
@endsection