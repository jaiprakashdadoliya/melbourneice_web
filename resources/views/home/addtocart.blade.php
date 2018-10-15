@extends('layouts.default')

@section('title', 'Cart Details')

@section('content')
<nav class="navbar navbar-default">
 	@include('includes.navigation')
</nav>
<div class="container">		
	<h1 class="title">Welcome <span>{{ $userName = UserHelper::get_profile_name() }}!</span></h1>
    <div class="inner-content">
      	<h3 class="page-title">{{ $title }}</h3>
      	<div class="clearfix"></div>
      	@if(Session::get('message') != '')
        	<div class="alert alert-danger alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ Session::get('message') }}
        	</div>
      	@endif      	
  		      
  		@if(!empty($checkoutSessionData))
				@php $i=1; $totalPaidFees = array(); @endphp
  				@foreach ($checkoutSessionData as $key => $memberships)
  				@php
            		$membershipType = (new App\Helpers\UserHelper)->getMembershipTypes($memberships['membershipType']);

					if(!empty($memberships['Bay'])){
						if($memberships['Bay'] == 1){
							$bayType = 'Bay 1';
						} else if($memberships['Bay'] == 2){
							$bayType = 'Bay 2';
						} else if($memberships['Bay'] == 3){
							$bayType = 'Bay 3';
						} else if($memberships['Bay'] == 4){
							$bayType = 'Bay 4';
						} else if($memberships['Bay'] == 5){
							$bayType = 'Bay 5';
						} else {
							$bayType = 'NA';
						}
					} else {
						$bayType = 'N/A';
					}

					if(!empty($memberships['Row'])){
						if($memberships['Row']){
							$rowNo = $memberships['Row'];
						} else {
							$rowNo = 'NA';
						}
					} else {
						$rowNo = 'N/A';
					}

					if(!empty($memberships['seatNumber'])){
						$seatNumber = $memberships['seatNumber'];
					} else {
						$seatNumber = 'N/A';
					}

					if(!empty($memberships['firstName']) && !empty($memberships['lastName'])){
						$fullName = ucfirst($memberships['firstName'].' '.$memberships['lastName']);
					} else {
						$fullName = 'N/A';
					}

					$total = $memberships['membershipFees'];
					$paidFess = $memberships['paidFees'];
					$totalPaidFees[] = $total + $paidFess;

				@endphp
				<div class="product-details">
  					<div class="cart-details">
      				
      					<div class="row">
      		
      						<div class="col-md-8 col-sm-7 col-xs-6">
      							<h3>{{ $fullName }}<span class="edit-profile" id="{{ $key }}">&#x270E;</span></h3>
                    <!-- <input type="hidden" name="sessionKey" id="sessionKey" value=""> -->
						      	<!-- <div class="">Member No. - $memberNumber</div> -->
					          
  								<div class="">Membership - {{ $membershipType }}</div>
						      	@php if($memberships['membershipType'] == 1 || $memberships['membershipType'] == 2 || $memberships['membershipType'] == 7){ @endphp
						      	<div class=""><span>{{ $bayType }}</span>, Row <span>{{ $rowNo }}</span>, Seat No. <span>{{ $seatNumber }}</span></div>
      							@php } @endphp    	
  								<div class="action"><a class="btn remove" href="{{ url('removeCardItem/'.$key) }}">Remove</a></div>
      						</div>
  							<div class="col-md-4 col-sm-5 col-xs-6 text-right amount">Membership Amount : <b>${{ $memberships['membershipFees'] }}</b></div>
      						<div class="col-md-4 col-sm-5 col-xs-6 text-right amount">Service Fees : <b>${{ $memberships['paidFees'] }}</b></div>
							</div>
					</div>
					@php $i++; @endphp
					@endforeach
               		@php $totalAmount = array_sum($totalPaidFees) @endphp
  					<div class="subtotal"><div class="pull-left"><b>Total</b></div><div class="pull-right"><b>${{ $totalAmount }}</b></div></div>
      			</div>
  		@else
		<div class="empty-cart text-center"><img src="{{ asset('public/web/images/empty-cart.png') }}"><br>Your cart is currently empty. <br><span>Add somthing in your cart.</span></p>                        
			@endif         		
	      	
      	<div class="clearfix text-right">
      	@if(!empty($checkoutSessionData))
      		<a href="{{ route('checkout') }}" class="btn blue pull-right">Check Out</a>
  		@endif
  		</div>
  	</div>
</div>

<!-- Edit profile modal for cart -->
<div class="modal" id="edit_profile_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
  	<form name="editCartProfile" id="editCartProfile" action="{{ route('saveCartProfile') }}">
    	<div class="modal-content">
      <div class="modal-header alert-info">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Edit Profile</h4>
      </div>
      <div class="modal-body"> 
    	<div class="row">
            <div class="col-md-4 form-group">
              <label for="firstName"> First Name <span class="red">*</span></label>
              <input type="text" name="firstName" id="firstName" class="form-control" placeholder="Enter First Name">
            </div>
            <div class="col-md-4 form-group">
              <label for="lastName"> Last Name <span class="red">*</span></label>
              <input type="text" name="lastName" id="lastName" class="form-control" placeholder="Enter Last Name ">
            </div>
            <div class="col-md-4 form-group">
              <label for="profileEmail"> Email <span class="red">*</span></label>
              <input type="email" name="profileEmail" id="profileEmail" class="form-control" placeholder="Enter Email">
            </div>
          </div>
          <div class="row">

            <div class="col-md-4 form-group">
              <label for="contactNumber">Contact Number <span class="red">*</span></label>
              <input type="text" name="contactNumber" id="contactNumber" class="form-control" placeholder="Enter Contact Number">
            </div>

            <div class="col-md-4 form-group">
              <label for="dateofBirth"> DOB <span class="red">*</span></label>
              <input type="text" name="dateofBirth" id="dateofBirth" class="form_datetime form-control" placeholder="Enter DOB" readonly>
            </div>

            <div class="col-md-4 form-group">
              <label for="profession">Profession <span class="red">*</span></label>
              <input type="text" name="profession" id="profession" class="form-control" placeholder="Enter Profession">
            </div>                                  
          </div>
          <div class="row"> 

            <div class="col-md-4 form-group">
              <label for="addressLine1"> Address Line 1 <span class="red">*</span></label>
              <textarea rows="1" placeholder="Enter Address Line 1" class="form-control" name="addressLine1" id="addressLine1"></textarea>
            </div>  
            <div class="col-md-4 form-group">
              <label for="addressLine2"> Address Line 2</label>
              <textarea rows="1" placeholder="Enter Address Line 2" name="addressLine2" id="addressLine2" class="form-control"></textarea>
            </div>

            <div class="col-md-4 form-group"><label for="suburb"> Suburb <span class="red">*</span></label>
              <input type="text" name="suburb" id="suburb" class="form-control" placeholder="Enter Suburb">
            </div>                      
          </div>
          <div class="row">
            <div class="col-md-4 form-group"><label for="state"> State <span class="red">*</span></label>
              <input type="text" name="state" id="state" class="form-control" placeholder="Enter State">
            </div> 
            <div class="col-md-4 form-group">
              <label for="postCode"> Postcode <span class="red">*</span></label>
              <input type="text" name="postCode" id="postCode" class="form-control" placeholder="Enter Postcode">
            </div>  
            <div class="col-md-4 form-group"><label for="country">Country <span class="red">*</span></label>
              <input type="text" name="country" id="country" class="form-control" placeholder="Enter Country">
            </div> 
          </div>
          <div class="row">
            <div class="col-md-4 form-group"><label for="yearFirstJoined"> First Year Joined </label>
              <input type="text" name="yearFirstJoined" id="yearFirstJoined" class="form-control" placeholder="First Year Joined" readonly>
            </div> 
          </div>
      </div>
      <div class="modal-footer alert-info">
      	<input type="hidden" id="sessionKey" value="" name="sessionKey" />
      	<button type="submit" class="btn blue">Update</button>
      	<button type="button" class="btn btn-mine" data-dismiss="modal">Cancel</button>
      </div>
    </div>
    </form>
  </div>
</div>
@endsection
