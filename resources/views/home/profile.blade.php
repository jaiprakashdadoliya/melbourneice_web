@extends('layouts.default')

@section('title', 'Profile')

@section('content')

@php 
  $getSessionData = (new App\Helpers\UserHelper)->user_session_data();
@endphp

  <nav class="navbar navbar-default">
      @include('includes.navigation')
      <link rel="stylesheet" type="text/css" href="{{ asset('public/web/css/dataTables.bootstrap.min.css') }}"/>
      <script type="text/javascript" src="{{ asset('public/web/js/jquery.dataTables.min.js') }}"></script>
      <script type="text/javascript" src="{{ asset('public/web/js/dataTables.bootstrap.min.js') }}"></script>
  </nav>
  <div class="container">
      <h1 class="title">Welcome <span>{{ $userName = UserHelper::get_profile_name() }}!</span></h1>
      <div class="inner-content">
        <div class="row">
          <div class="col-md-6 col-sm-6 col-xs-6"><h3 class="page-title">User Profile</h3></div>
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
        <form id="update_profile_form" name="update_profile" method="POST" action="{{ route('profile') }}">
        {{ csrf_field() }}
          <div class="row">
            <div class="col-md-4 form-group">
              <label for="firstName">First Name <span class="red">*</span></label>
              <input type="text" name="firstName" id="firstName" class="form-control" autocomplete="false" placeholder="Enter First Name" value="@php if($profileDetail[0]->firstName){ echo $profileDetail[0]->firstName; } else { } @endphp">
              @if($errors->has('firstName'))
                <p class="validation-error">{{ $errors->first('firstName') }}</p>
              @endif
            </div>
            <div class="col-md-4 form-group">
              <label for="lastName">Last Name <span class="red">*</span></label>
              <input type="text" name="lastName" id="lastName" class="form-control" autocomplete="false" placeholder="Enter Last Name" value="@php if($profileDetail[0]->lastName){ echo $profileDetail[0]->lastName; } else { } @endphp">
              @if($errors->has('lastName'))
                <p class="validation-error">{{ $errors->first('lastName') }}</p>
              @endif
            </div>
            <div class="col-md-4 form-group">
              <label for="profileEmail">Email <!-- <span class="red">*</span> --></label>
              <input type="text" name="profileEmail" id="profileEmail" class="form-control" autocomplete="false" placeholder="Enter Email" value="@php if($profileDetail[0]->profileEmail){ echo $profileDetail[0]->profileEmail; } else { } @endphp" disabled>
              @if($errors->has('profileEmail'))
                <p class="validation-error">{{ $errors->first('profileEmail') }}</p>
              @endif
            </div>            
          </div>
          <div class="row">
            <div class="col-md-4 form-group">
              <label for="contactNumber">Contact Number <span class="red">*</span></label>
              <input type="text" name="contactNumber" id="contactNumber" class="form-control" autocomplete="false" placeholder="Enter Contact Number" value="@php if($profileDetail[0]->contactNumber){ echo $profileDetail[0]->contactNumber; } else { } @endphp">
              @if($errors->has('contactNumber'))
                <p class="validation-error">{{ $errors->first('contactNumber') }}</p>
              @endif
            </div>
            @php 
              $dateofBirth = (new App\Helpers\UserHelper)->convertDateFormat($profileDetail[0]->dateofBirth);
            @endphp
            <div class="col-md-4 form-group">
              <label for="dateofBirth">DOB <span class="red">*</span></label>
              <input type="text" name="dateofBirth" id="dateofBirth" class="form-control profile_datetime" autocomplete="false" placeholder="Enter DOB" value="@php if($profileDetail[0]->dateofBirth && $profileDetail[0]->dateofBirth != '0000-00-00'){ echo $dateofBirth; } else { } @endphp" readonly>
              @if($errors->has('dateofBirth'))
                <p class="validation-error">{{ $errors->first('dateofBirth') }}</p>
              @endif
            </div>            
            <div class="col-md-4 form-group">
              <label for="profession">Profession <span class="red">*</span></label>
              <input type="text" name="profession" id="profession" class="form-control" placeholder="Enter Profession" value="@php if($profileDetail[0]->profession){ echo $profileDetail[0]->profession; } else { } @endphp">
              @if($errors->has('profession'))
                <p class="validation-error">{{ $errors->first('profession') }}</p>
              @endif
            </div>            
          </div>
          <div class="row">

            <div class="col-md-4 form-group">
              <label for="addressLine1">Address Line 1 <span class="red">*</span></label>
              <textarea rows="1" name="addressLine1" id="addressLine1" class="form-control" placeholder="Enter Address Line 1">@php if($profileDetail[0]->addressLine1){ echo $profileDetail[0]->addressLine1; } else { } @endphp</textarea>
              @if($errors->has('addressLine1'))
                <p class="validation-error">{{ $errors->first('addressLine1') }}</p>
              @endif
            </div>  
            <div class="col-md-4 form-group">
              <label for="addressLine2">Address Line 2</label>
              <textarea rows="1" name="addressLine2" id="addressLine2" class="form-control" placeholder="Enter Address Line 2">@php if($profileDetail[0]->addressLine2){ echo $profileDetail[0]->addressLine2; } else { } @endphp</textarea>
            </div>
            
            <div class="col-md-4 form-group">
              <label for="suburb">Suburb <span class="red">*</span></label>
              <input type="text" name="suburb" id="suburb" class="form-control" autocomplete="false" placeholder="Enter Suburb" value="@php if($profileDetail[0]->suburb){ echo $profileDetail[0]->suburb; } else { } @endphp">
              @if($errors->has('suburb'))
                <p class="validation-error">{{ $errors->first('suburb') }}</p>
              @endif
            </div>
                                
          </div>
          <div class="row">
            <div class="col-md-4 form-group">
              <label for="state">State <span class="red">*</span></label>
              <input type="text" name="state" id="state" class="form-control" autocomplete="false" placeholder="Enter State" value="@php if($profileDetail[0]->state){ echo $profileDetail[0]->state; } else { } @endphp">
              @if($errors->has('state'))
                <p class="validation-error">{{ $errors->first('state') }}</p>
              @endif
            </div> 
            <div class="col-md-4 form-group">
              <label for="postcode">Postcode <span class="red">*</span></label>
              <input type="text" name="postCode" id="postCode" class="form-control" autocomplete="false" placeholder="Enter Postcode" value="@php if($profileDetail[0]->postCode){ echo $profileDetail[0]->postCode; } else { } @endphp">
              @if($errors->has('postCode'))
                <p class="validation-error">{{ $errors->first('postCode') }}</p>
              @endif
            </div> 
            <div class="col-md-4 form-group">
              <label for="country">Country <span class="red">*</span></label>
              <input type="text" name="country" id="country" class="form-control" autocomplete="false" placeholder="Enter Country" value="@php if($profileDetail[0]->country){ echo $profileDetail[0]->country; } else { } @endphp">
              @if($errors->has('country'))
                <p class="validation-error">{{ $errors->first('country') }}</p>
              @endif
            </div>             
          </div>
          <div class="row">
            <div class="col-md-4 form-group">
              <label for="yearFirstJoined">First Year Joined <!-- <span class="red">*</span> --></label>
              <input type="text" name="yearFirstJoined" id="yearFirstJoined" class="form-control" placeholder="First Year Joined" disabled value="@php if($profileDetail[0]->yearFirstJoined){ echo $profileDetail[0]->yearFirstJoined; } else { } @endphp">
              @if($errors->has('yearFirstJoined'))
                <p class="validation-error">{{ $errors->first('yearFirstJoined') }}</p>
              @endif
            </div> 
          </div>
          <div class="">
            <div class="clearfix text-right">
              <input type="submit" name="profileSubmit" value="Update" class="btn blue pull-right">
            </div>
          </div>
        </form>
      </div>

      <!-- memebership Status section start -->
      <div class="inner-content">
          <h3 class="page-title">{{ $membserships_title }}</h3>
          <div class="clearfix"></div>
          <div class="table-responsive">
          <table id="user_membership_status" class="table table-striped table-bordered table-responsive" cellspacing="0" width="100%">
            <thead>
                <tr>                        
                      <th>S.No</th>
                      <th>Name</th>
                      <th>Member Number</th>
                      <th>Membership Type</th>
                      <th>Seat</th>
                      <th>Membership Status</th>
                </tr>
            </thead>
            <tbody>
              @if(!empty($memebershipData))
                @php $i=1; @endphp
                @foreach ($memebershipData as $memberships)
                  @php 
                    
                    $membershipType = (new App\Helpers\UserHelper)->getMembershipTypes($memberships->membershipType);
                    
                    $membershipStatus = $memberships->membershipStatus;

                    if($memberships->Bay == 1){
                      $BayType = 'Bay 1';
                    } else if($memberships->Bay == 2){
                      $BayType = 'Bay 2';
                    } else if($memberships->Bay == 3){
                      $BayType = 'Bay 3';
                    } else if($memberships->Bay == 4){
                      $BayType = 'Bay 4';
                    } else if($memberships->Bay == 5){
                      $BayType = 'Bay 5';
                    } else {
                      $BayType = 'NA';
                    }

                    if($memberships->Row){
                      $RowNo = 'Row '.$memberships->Row;
                    } else {
                      $RowNo = 'NA';
                    }

                    if($memberships->seatNumber != 0){
                      $seatNumber = $memberships->seatNumber;
                    } else {
                      $seatNumber = 'NA';
                    }


                    if(!empty($memberships->memberNumber)){
                      $memberNumber = $memberships->memberNumber;
                    } else {
                      $memberNumber = 'N/A';
                    }

                    if(!empty($memberships->firstName) && !empty($memberships->lastName)){
                      $fullName = ucfirst($memberships->firstName.' '.$memberships->lastName);
                    } else {
                      $fullName = 'N/A';
                    }

                    $old_timestamp = strtotime($memberships->startDate);
                    $startDate = date('d-M-Y', $old_timestamp); 

                    
                    if($memberships->membershipYear){
                      $membershipYear = $memberships->membershipYear;  
                    } else {
                      $membershipYear = "-";
                    }

                  @endphp
                  <tr>
                    <td>{{ $i }}</td>
                    <td>{{ $fullName }}</td>
                    <td>{{ $memberNumber }}</td>
                    <td>{{ $membershipType }}</td>
                    @if($memberships->membershipType == 1 || $memberships->membershipType == 2 || $memberships->membershipType == 7)
                      <td>@if($BayType == 'NA' && $RowNo == 'NA' && $seatNumber == 'NA')<div> - </div>@else<div>{{ $BayType }}, {{ $RowNo }}, {{ $seatNumber }}</div>@endif</td>
                    @elseif($memberships->membershipType == 5)
                      <td><div>-</div></td>
                    @else
                      <td><div>GA</div></td>
                    @endif
                    <td id="changeStatusText{{ $memberships->id }}">{{ $membershipStatus }} for {{ $membershipYear }}</td>
                    <!-- <td>{{ $startDate }}</td>  
                    <td>
                      @if($getSessionData['user_id'] != $memberships->userId)
                        @if($membershipStatus == 'Active')
                          <button class="deLinkMembership" deLinkMemberName="{{ $fullName }}" memberProfileId="{{ $memberships->id }}" id="deMember{{ $memberships->id }}">De-link</button>
                        @else
                          <button class="disable-btn">De-link</button>
                        @endif
                      @endif                      
                    </td>  -->                 
                  </tr>
                @php $i++; @endphp
                @endforeach
              @else
              @endif                  
            </tbody>        
          </table>
          </div> 
          <div class="clearfix text-right">
              @php $year = date('Y') @endphp
              <a href="{{ url('newmembership') }}" class="btn blue buy-membership">Buy {{ $year }} Membership</a>
          </div>
      </div>
      <!-- memebership Status section End -->

  </div>  <!-- Main container close -->
  <script type="text/javascript">
    $(document).ready(function() {
      $('#user_membership_status').DataTable({
        pageLength: 10,
      });
    });
  </script>
@endsection
