@extends('layouts.default')

@section('title', 'Membership')

@section('content')
	  <nav class="navbar navbar-default">
      @include('includes.navigation')
    </nav>
    
    @php 
      if(!empty($getMembershipStatus)){
  
        $membershipType = (new App\Helpers\UserHelper)->getMembershipTypes($getMembershipStatus->membershipType);
        $memberStatus = $getMembershipStatus->membershipStatus;

      } else {
          $memberStatus = 'N/A';
          $membershipType = 'N/A';
      }
    @endphp
    <div class="container">      
      <h1 class="title">Welcome <span>{{ $userName = UserHelper::get_profile_name() }}!</span></h1>
      <div class="description">Membership Status <b>{{ $memberStatus }}</b><br> Information about Membership <b>{{ $membershipType }}</b><br> <a href="javascript:void(0);" id="faq_data">FAQs</a></div>
      
    <div class="">
      <div class="membership_alert_msg" style="display: none;"></div>
      <form name="membership_form" id="membership_form" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="inner-content">
          <h3 class="page-title">Buy @if($year) {{ $year }} @endif Membership</h3>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="select_membership">Select Membership <span class="red">*</span></label>
              	<select name="select_membership" id="select_membership" class="form-control">
      	          	<option selected disabled>Select Membership</option>
      	          	<option>New</option>
      	          	<option>Renew</option>
      	        </select>
              </div>  
            </div>
            <!-- <div class="col-md-6">
              <div class="form-group">
                <label for="membershipCategory">Select Membership Category <span class="red">*</span></label>
                <select name="membershipCategory" id="membershipCategory" class="form-control">
                    <option selected disabled>Select Membership Category</option>
                    <option value="MIM">MIM</option>
                    <option disabled value="IBC">IBC</option>
                </select>
              </div>  
            </div>  -->  
          </div>   
        </div> 

        <div class="inner-content mim_profile_inner" style="display: none;">
          <h3 class="page-title">Profile</h3>
          <div class="row">
            <div class="col-md-6 mim_profile_div" style="display: none;">
              <div class="form-group">
                <label for="mim_profile" id="mim_profile_label"> Profile <span class="red">*</span></label>
                <select name="mim_profile" id="mim_profile" class="form-control">
                  <option disabled>Select Profile</option>
                  @if(!empty($profileIds))
                    @foreach ($profileIds as $profileId)
                      <option memberNumber="{{ $profileId['member_no'] }}" profile_id="{{ $profileId['profile_id'] }}">{{ (new App\Helpers\UserHelper)->get_name($profileId['profile_id']) }}</option>
                    @endforeach
                  @else
                    <option selected disabled>No record found</option>
                  @endif
                </select>
              </div>
            </div>

            <div class="col-md-6 mim_number_div" style="display: none;">
              <div class="form-group">
                <label for="mim_number" id="mim_number_label" style="display: none;"> MIM/IBC Number <!-- <span class="red">*</span> --></label>
                <input type="text" name="mim_number" id="mim_number" readonly="readonly" class="form-control" placeholder="MIM/IBC Number" style="display: none;">
              </div>
            </div>  
          </div>
        </div>

        <div class="inner-content new_user_form_inner" style="display: none;">  
          <div class="" id="new_user_form" style="display: none;">
            <h3 class="page-title">Member Details</h3>
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
                <div class="col-md-4 form-group"><label for="country"> Country <span class="red">*</span></label>
                  <input type="text" name="country" id="country" class="form-control" placeholder="Enter Country">
                </div> 
              </div>

              <div class="row">
                <div class="col-md-4 form-group"><label for="yearFirstJoined"> First Year Joined </label>
                  <input type="text" name="yearFirstJoined" id="yearFirstJoined" class="form-control" placeholder="First Year Joined" readonly value="@php echo date('Y') @endphp">
                </div> 
              </div>

              <input type="hidden" name="memberShipId" id="memberShipId">
              <input type="hidden" name="membershipYear" id="membershipYear">
          </div>
        </div>

        <div class="inner-content map_div_inner" style="display: none;">  
          <div class="form-group map_div" style="display:none;"><img src="{{ asset('public/web/images/area.png') }}"></div>
        </div>

        <div class="inner-content membership_type_div_inner" style="display: none;">
          <div class="row">
            <div class="col-md-6 membership_type_div" style="display: none;">
              <div class="form-group">
                <label for="membership_type"> Membership Type <span class="red">*</span></label>
                <select name="membership_type" id="membership_type" class="form-control">
                  <option selected disabled>Membership Types </option>
                  @foreach ($memebershipTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->memberShipTypeName }} - ${{ $type->memberShipTypePrice }}</</option>
                  @endforeach
                </select>
              </div>            
            </div>
          
            <div class="col-md-6 select_bay_div" style="display: none;">
              <div class="form-group">
                <label for="select_bay" id="select_bay_label" style="display: none;"> Select Bay <span class="red">*</span></label>
                <select name="select_bay" id="select_bay" style="display: none;" class="form-control">
                  <option selected disabled>Select Bay</option>
                  @foreach ($bays as $bay)
                    <option value="{{ $bay->bayNumber }}">Bay {{ $bay->bayNumber }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 select_row_div" style="display: none;">
              <div class="form-group select_row_form_group" style="display: none;">                
                
              </div>
            </div>

            <div class="col-md-6 select_seats_div" style="display: none;">
              <div class="form-group select_seats_form_group" style="display: none;">
                
              </div>
            </div>   
          </div>
          <div class="row general_option_div" style="display: none;">
            <!-- <div class="col-md-12 form-group">
              <label><input type="checkbox" name="special_condition" id="special_condition">Select Under 18, Pensioners, Students, Specially-abled</label>
            </div> -->
            <div class="col-md-12 form-group tnc">
              <label><input type="checkbox" name="privacyAcknowledged" id="privacyAcknowledged">I agree to the Melbourne Ice Membership Charter, <a href="javascript:void(0);" id="privacyTerms">Terms & Conditions</a> and <a href="javascript:void(0);" id="privacyPolicyModal">Privacy Policy</a><span class="red"> *</span></label>

            </div>
            <div class="col-md-12">
            <!-- <img id="upload_file_preview" width="30%"> -->
              <label class="btn btn-mine image_upload_button">
                  <span id="uploadUserFile">Upload ID</span>
                  <div class="show_image_loader" style="display: none;"></div>
                   <input type="file" name="upload_file" id="upload_file" style="display: none;">
                  <input type="hidden" name="idAttachment" id="idAttachment"> 
              </label> 
              <!-- <div class="show_image_loader"><img src="{{ asset('public/web/images/ajax-loader.gif') }}"></div> --> 

              <img id="image_upload_preview" style="display: none;" />  
              <img id="uploaded_pdf_preview" style="display: none;">              
              <span class="fileinput-filename"></span>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-6">
              <span class="fileinput-new"><a href="javascript:void(0);" id="uploadFileInfoMsg">Concession and youth must upload a valid proof of ID</a>
              <p>Please attach a clear photo (JPEG, JPG or PNG) or scanned PDF of your ID</p>
              </span> 
            </div>            
          </div>
          
            <div class="add_to_cart_div" style="display: none;">
              <!-- <div class="text-left col-md-8">
                <a target="_blank" class="btn btn-mine btn-default" href="{{ url('privacy') }}">Privacy Policy</a>
              </div> -->
              <div class="text-right">
                <input type="submit" name="save" class="btn blue add_to_cart_btn" value="Add to Cart">
              </div>
            </div>
          
          <!-- add a modal for caption -->
          <div id="myModalImage" class="fancyModal">
            <span class="fancy-close">&times;</span>
            <img class="fancy-modal-content" id="img01">
            <!-- <div id="caption"></div> -->
          </div>

        </div> 
        <input type="hidden" name="upcoming_year" id="upcoming_year" value="{{ $year }}">
        <!-- profileId for profile renew -->
        <input type="hidden" name="profileIds" id="profileIds">
      </form>
    </div>
  </div>
@endsection
