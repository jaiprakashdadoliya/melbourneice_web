@extends('layouts.default')

@section('title', 'Checkout')

@section('content')

  	<nav class="navbar navbar-default">
      @include('includes.navigation')
      <link rel="stylesheet" type="text/css" href="{{ asset('public/web/css/dataTables.bootstrap.min.css') }}"/>
		  <script type="text/javascript" src="{{ asset('public/web/js/jquery.dataTables.min.js') }}"></script>
		  <script type="text/javascript" src="{{ asset('public/web/js/dataTables.bootstrap.min.js') }}"></script>


    </nav>
	<div class="container">		
      	<h1 class="title">welcome <span>{{ $userName = UserHelper::get_profile_name() }}!</span></h1>
	    <div class="inner-content">
	      	<h3 class="page-title">{{ $title }}</h3>
	      	<table id="user_membership_status" class="table table-striped table-bordered table-responsive" cellspacing="0" width="100%">
                    <thead>
                    	<tr>                    		
                           	<th>S.No</th>
                           	<th>Name</th>
                           	<th>Membership Type</th>
                           	<th>Conssesion Type</th>
                           	<th>Membership Status</th>
                           	<th>Created Date</th>
                    	</tr>
                	</thead>
                	<tbody>
                		@if(!empty($memebershipData))
                			@php $i=1; @endphp
                			@foreach ($memebershipData as $memberships)
                				@php 
                					if($memberships->membershipType == 1){
                						$membershipType = 'RS - Adult';
                					} else if($memberships->membershipType == 2){
                						$membershipType = 'RS - Concession';
                					} else if($memberships->membershipType == 3){
                						$membershipType = 'GA - Concession';
                					} else if($memberships->membershipType == 4){
                						$membershipType = 'GA - Adult';
                					} else {}

                					if($memberships->consessionType == 1){
                						$consessionType = 'Yes';
                					} else {
                						$consessionType = 'No';
                					}

                					if($memberships->membershipStatus == 1){
                						$membershipStatus = 'Inactive';
                					} else {
                						$membershipStatus = 'Active';
                					}

                					$old_timestamp = strtotime($memberships->startDate);
									        $startDate = date('d-m-Y', $old_timestamp); 
                				@endphp
		                		<tr>
		                			<td>{{ $i }}</td>
		                			<td>{{ (new App\Helpers\UserHelper)->get_name($memberships->profileId)}}</td>
		                			<td>{{ $membershipType }}</td>
		                			<td>{{ $consessionType }}</td>
		                			<td>{{ $membershipStatus }}</td>
		                			<td>{{ $startDate }}</td>
		                		</tr>
	                		@php $i++; @endphp
	                		@endforeach
                		@else
                			<tr>
                        <td>
	                			  <p>No Record Found.</p>                          
                        </td>
	                		</tr>
                		@endif               		
                	</tbody>				
           </table>
           <div class="clearfix text-right">
                <input type="submit" name="submit" value="Buy 2018 Membership" class="btn blue">
              </div>
    	</div>
  </div>
  <script type="text/javascript">
  	$(document).ready(function() {
	    $('#user_membership_status').DataTable({
	    	"pageLength": 10
	    });
	});
  </script>
@endsection