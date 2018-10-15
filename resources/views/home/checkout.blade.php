@extends('layouts.default')

@section('title', 'Checkout')

@section('content')
  	<nav class="navbar navbar-default">
      @include('includes.navigation')
    </nav>
	<div class="container">		
		<h1 class="title">Welcome <span>{{ $userName = UserHelper::get_profile_name() }}!</span></h1>
	    <div class="inner-content">
	      	<h3 class="page-title">{{ $title }}</h3>
	      	
          	<div class="payway_alert_msg" style="display: none;">
          	</div>
	        
	      	<form class="checkout" id="checkout_payment_form" name="payment_checkout_form" method="POST" action="{{ route('payment_process') }}">
	      		{{ csrf_field() }}
		        <div class="row">
			        <div class="col-md-12 form-group">
			        	<!-- <label for="comment"> Comments <span class="red">*</span></label> -->
			        	<!-- <textarea name="comment" id="comment"></textarea> -->
			        	<input type="text" name="comment" id="comment" class="form-control" placeholder="Add a message (optional)">
		        	</div>
		      	</div>
		        <div class="row form-group">
		        <div class="col-md-12" id="paypal_amount">
		          <div class="col-md-6 col-sm-6 col-xs-6"><h2>Total</h2></div>		          
		          	@php
		          		$checkoutSessionData = UserHelper::checkout_session_data();
		          		$totalPaidFees = array();
  						foreach ($checkoutSessionData as $key => $memberships){
  							$total = $memberships['membershipFees'];
		  					$paidFess = $memberships['paidFees'];
		  					$totalPaidFees[] = $total + $paidFess;
  						}
  						$totalAmount = array_sum($totalPaidFees)
       				@endphp
		          <div class="col-md-6 col-sm-6 col-xs-6"><h2 class="text-right">${{ $totalAmount }}</h2></div>
		        </div>
		      </div>

			    <div class="payment-methode">
	        		<div class="row">
				        <div class="col-md-4 form-group">
				        	<label>
				        		<input type="radio" name="payment" id="paypal" value="paypal" class="paymentGateway paypal_payment_gateway"><img src="{{ asset('public/web/images/paypal.png') }}">
			        		</label> 
				        </div>
				        <!-- <div class="col-md-3 form-group">
				        	<label>
				        		<input type="radio" name="payment" id="payway" value="payway" required class="paymentGateway payway_payment_gateway"><img src="{{ asset('public/web/images/payway.png') }}">
			        		</label> 
			        	</div> -->
				        <!-- <div class="col-md-4 form-group"><label><input type="radio" name="payment" id="stripe" value="stripe" class="paymentGateway" required><img src="{{ asset('public/web/images/stripe.png') }}"></label> </div> -->
			      	</div>
			      	<p></p>
			      	<div class="row paypal_required_fields" style="display: none;">
			      		<h3 class="page-title" style="margin-left: 17px;">Payment Details</h3>
				        <div class="col-md-6 form-group">
				        	<label for="set_cycle"> Payment Plan <span class="red">*</span></label>
				        	<select name="setFrequency" id="setFrequency" class="form-control">
				        		<option selected disabled>Select Plan</option>
				        		<option value="full">Full</option>
				        		<!-- <option value="1">1 Months</option> -->
				        		<!-- <option value="2">2 Months</option> -->
			        		</select>
			        	</div>
			        	<div class="col-md-6 form-group" id="cycleInMonth" style="display: none;">
				        	<label for="set_freq_interval"> Payment Frequency <span class="red">*</span></label>
				        	<select name="setCycle" id="setCycle" class="form-control"> 
				        		<option selected disabled>Select Frequency</option>
				        		<option value="Week">Weekly</option>
				        		<option value="Fortnight">Fortnightly</option>
				        		<option value="Month">Monthly</option>
				        	</select>
			        	</div>
			      	</div>
		      	</div>	
		      	<div class="row">
			      	<div class="col-md-12 main_submit_button">
				        <button class="btn pull-right blue proceed_to_payment" ><span id="payment-process">Proceed to Pay</span>
				        <div class="show_loader" style="display: none;"></div>
				        </button>
		    		</div>	  
	    		</div>
		    </form>
    	</div>
  </div>
@endsection