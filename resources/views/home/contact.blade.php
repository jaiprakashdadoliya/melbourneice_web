@extends('layouts.default')

@section('title', 'Contact')
	
@section('content')
	<nav class="navbar navbar-default">
    	@include('includes.navigation')
  	</nav>
  	<div class="map"><!--img src="{{ asset('public/web/images/map.png') }}"--></div>
	<div class="container">
		<div class="inner-content contact"> 
          	<h3 class="page-title">Contact Us</h3>   
          	<p>
	          @if(Session::get('message') != '')
	            <div class="validation-error">
	              <div class="alert alert-danger alert-dismissable">
		            	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		              	{{ Session::get('message') }}
		          	</div>
	            </div>
	          @endif
	          @if(Session::get('success') != '')
	            <div class="alert alert-success alert-dismissable">
	            	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	              	{{ Session::get('success') }}
	          	</div>
	          @endif
	        </p>  
          	<form name="contactus" id="contactus" method="POST" action="{{ route('contact') }}">
          		{{ csrf_field() }}
	            <div class="row form-group">
	              	<div class="col-md-4">
	              		<label for="name">Name <span class="red">*</span> </label>
            			<input type="text" name="name" id="name" class="form-control" autocomplete="false" placeholder="Enter Name" value="@php if($contactDetails[0]->full_name){ echo $contactDetails[0]->full_name; } else { } @endphp">
	              		<!-- <p class="validation-error">{{ $errors->first('name') }}</p> -->
              		</div>
	              	<div class="col-md-4">
	              		<label for="contact_number">Contact Number <span class="red">*</span> </label>
            			<input type="text" name="contact_number" id="contact_number" class="form-control" autocomplete="false" placeholder="Enter Contact Number" value="@php if($contactDetails[0]->contactNumber){ echo $contactDetails[0]->contactNumber; } else { } @endphp">
	              		<!-- <p class="validation-error">{{ $errors->first('contact_number') }}</p> -->
              		</div>
	              	<div class="col-md-4">
	              		<label for="email">Email <span class="red">*</span> </label>
            			<input type="email" name="email" id="email" class="form-control" autocomplete="false" placeholder="Enter Email" value="@php if($contactDetails[0]->profileEmail) { echo $contactDetails[0]->profileEmail; } else { } @endphp">
	              		<!-- <p class="validation-error">{{ $errors->first('email') }}</p> -->
              		</div>
	            </div>
	            
	            <div class="row form-group">
	              	<div class="col-md-12">
	              		<label for="message">Message <span class="red">*</span> </label>
	              		<textarea name="message" id="message" class="form-control" placeholder="Enter Message">{{ old('message') }}</textarea>
              			<!-- <p class="validation-error">{{ $errors->first('message') }}</p> -->
	              </div>
	            </div>
	            <div class="row form-group">
	              <div class="col-md-12 text-right"><button class="btn blue checkoutSubmit">Submit</button></div>
	            </div>
          	</form>
      	</div>
	</div>
@endsection