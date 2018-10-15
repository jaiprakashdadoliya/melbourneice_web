<script type="text/javascript">
	$(document).ready(function(){
		$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
	    });

	    // password tooltip show hide
	    $('body').on('click','.reg_password', function(){
	    		$(".password-msg").toggle();
	    });
	    $('body').on('click','.register_form', function(){
	    		$(".password-msg").hide();
	    });

	    $('body').keyup(function(e) {
		    var code = e.keyCode || e.which;
		    if (code == '9') {
		    	$(".password-msg").hide();
		    }
	 	});

		// new membership
		$('body').on('change', '#select_membership', function(){
			if($(this).val() == 'New'){
				$("#new_user_form").show();
				$(".new_user_form_inner").show();
				$("#mim_number").fadeOut();
				$("#mim_number_label").fadeOut();
				$(".mim_number_div").fadeOut();
				$(".mim_profile_div").fadeOut();
				$(".mim_profile_inner").fadeOut();

				$(".map_div_inner").show();
				$(".map_div").show();
				$(".membership_type_div_inner").show();
				$(".membership_type_div").show();
				$("#membership_type").val($("#membership_type option:first").val());
				$("#membership_type").removeAttr('disabled', 'disabled');	
				// remove disabled attribute
				$('#membership_type option').map(function () {
					if($(this).val() != "Membership Types"){
						val = $(this).val();
						$("#membership_type").find("[value='" + val + "']").removeAttr("selected");
	    				$("#membership_type").find("[value='" + val + "']").prop("disabled", false);
					}
				});

				$(".select_bay_div").show();
				$("#select_bay_label").show();
				$("#select_bay").show();
				
				$(".select_seats_div").hide();	
				$(".select_row_div").hide();

				$(".general_option_div").show();
				$(".add_to_cart_div").show();

				$("#select_bay").val($("#select_bay option:first").val());
				$("#select_row").val($("#select_row option:first").val());

				$('#firstName').val('');
				$("#lastName").val('');
				$('#profileEmail').val('');
				$('#dateofBirth').val('');
				$('#contactNumber').val('');
				$('#postCode').val('');
				$("#state").val('');
        		$("#suburb").val('');
				$('#addressLine1').val('');
				$('#addressLine2').val('');
				$('#country').val('');
				$('#profession').val('');
				$('#yearFirstJoined').val('<?php echo date('Y') ?>');
				$("#membershipYear").val('null');
				// $('#special_condition').prop('checked', false);
				$("#idAttachment").val('');
				var imageSrc = '{{ asset("public/web/images/no.png") }}';
        		$('#image_upload_preview').attr('src', imageSrc);
        		$('#uploaded_pdf_preview').attr('src', imageSrc);

        		$("#profileIds").val('');
			} else if($(this).val() == 'Renew') {

				$(".new_user_form_inner").fadeOut();
				$("#new_user_form").fadeOut();
				$(".mim_number_div").show();
				$(".mim_profile_div").show();
				$(".mim_profile_inner").show();

				$(".map_div_inner").hide();
				$(".map_div").hide();
				$(".membership_type_div_inner").hide();
				$(".membership_type_div").hide();

				$(".select_bay_div").hide();
				$("#select_bay").hide();
				$("#select_bay_label").hide();

				$(".select_seats_div").hide();	
				$(".select_row_div").hide();

				// $("#membership_type").val($("#membership_type option:first").val());
				$("#mim_profile").val($("#mim_profile option:first").val());

				$('#firstName').val('');
				$("#lastName").val('');
				$('#profileEmail').val('');
				$('#dateofBirth').val('');
				$('#contactNumber').val('');
				$('#postCode').val('');
				$("#state").val('');
        		$("#suburb").val('');
				$('#addressLine1').val('');
				$('#addressLine2').val('');
				$('#country').val('');
				$('#profession').val('');
				$('#yearFirstJoined').val('');
			} else {

			}
		});

		// get member no from select profile and append it in to membership number input
		$('body').on('change', "#mim_profile", function(){

			if($("#mim_profile").val() == "Select Profile"){
				$("#mim_number").val('');
				return false;				
			}
			var memberNumber = $('#mim_profile :selected').attr('memberNumber');
			var profile_id = $('#mim_profile :selected').attr('profile_id');	
			$("#mim_number_label").show();
			$("#mim_number").show();

			$(".map_div_inner").show()
			$(".map_div").show();
			$(".membership_type_div_inner").show();
			$(".membership_type_div").show();
			$(".general_option_div").show();
			$(".add_to_cart_div").show();

			$("#mim_number").val(memberNumber);
			$("#profileIds").val(profile_id);

			$.ajax({
	            type:'POST',
	            url:'getUserProfileRecord',
	            data: {memberNumber:memberNumber,profile_id:profile_id},
	            dataType:'json',
	            success:function(data){
	            	if (data.code == '100') {
	            		// console.log("datadatadata :", data); return false;
	            		if(data.showMembershipType == 'hide'){
	            			$(".select_bay_div").hide();
	            			$("#select_bay_label").hide();
	            			$("#select_bay").hide();

	            			$(".select_row_div").hide();
	            			$(".select_row_form_group").hide();

	            			$(".select_seats_div").hide();
	            			$(".select_seats_form_group").hide();

	            		} else {
	            			$(".select_bay_div").show();
	            			$("#select_bay_label").show();
	            			$("#select_bay").show();

	            			// sleect bay no
	            			$('#select_bay option').map(function () {
							    if ($(this).val() == data.Bay) return this;
							}).prop('selected', true);

							// select row no
	            			if(data.Row != 'null'){
	            				$(".select_row_div").show();
	            				$(".select_row_form_group").show();

								$(".select_row_form_group").text('');
		            			$(".select_row_form_group").append(data.Row);
		            		}

							// select seat
							if(data.seatNumber != 'null'){
								$(".select_seats_div").show();
								$(".select_seats_form_group").show();

								$(".select_seats_form_group").text('');
	            				$(".select_seats_form_group").append(data.seatNumber);
							}
	            		}

	            		/*if(data.consessionType == 1){
							$('#special_condition').prop('checked', true);
						}*/

						// memnershipId
						$("#memberShipId").val(data.memberShipsId);
						$("#membershipYear").val(data.membershipYear);

						// membershipType
						if(data.membershipStatus == 'Active'){
		            		$('#membership_type option').map(function () {
							    if ($(this).val() == data.membershipValue){					    		
						    		var val = $(this).val();
						    		// $("#membership_type").find("[value='" + val + "']").prop("disabled", true);
						    		return this;
					    		} 
					    		else {
					    			if($(this).val() != "Membership Types"){
				    			 		var val = $(this).val();
					    				$("#membership_type").find("[value='" + val + "']").removeAttr("selected");
					    				$("#membership_type").find("[value='" + val + "']").prop("disabled", false);
					    			}
					    		}
							}).prop('selected', true);
						} else {
							$('#membership_type option').map(function () {
							    if ($(this).val() == data.membershipValue){					    		
						    		var val = $(this).val();
						    		// $("#membership_type").find("[value='" + val + "']").prop("disabled", true);
						    		return this;
					    		} 
					    		else {
					    			if($(this).val() != "Membership Types"){
				    			 		var val = $(this).val();
					    				$("#membership_type").find("[value='" + val + "']").removeAttr("selected");
					    				$("#membership_type").find("[value='" + val + "']").prop("disabled", false);
					    			}
					    		}
							}).prop('selected', true);
						}
	            		

	            		$(".new_user_form_inner").show();
	            		$("#new_user_form").show();

	            		$("#firstName").val(data.user_data.firstName);
	            		$("#lastName").val(data.user_data.lastName);
	            		$("#profileEmail").val(data.user_data.profileEmail);
	            		$("#contactNumber").val(data.user_data.contactNumber);
            			$("#dateofBirth").val(data.dateofBirth);
	            		$("#state").val(data.user_data.state);
	            		$("#suburb").val(data.user_data.suburb);
	            		$("#country").val(data.user_data.country);
	            		$("#postCode").val(data.user_data.postCode);
	            		$("#addressLine1").val(data.user_data.addressLine1);
	            		$("#addressLine2").val(data.user_data.addressLine2);
	            		$("#idAttachment").val(data.user_data.idAttachment);
	            		$('#profession').val(data.user_data.profession);
	            		$('#yearFirstJoined').val(data.user_data.yearFirstJoined);
	            		/*$('#membershipCategory option').map(function () {
						    if ($(this).val() == data.user_data.membershipCategory) return this;
						}).attr('selected', 'selected');*/

						var splitExtension = data.user_data.idAttachment.split('.');
	            		var getExtension = splitExtension[1];   
                		             	

	            		if(getExtension == 'pdf' || getExtension == 'PDF'){
	            			var attrApend = '<?php echo MAIN_URL.WEB_UPLOAD_PATH ?>'+data.user_data.idAttachment;
	            			$('#uploaded_pdf_preview').attr('pdfFileName', attrApend);
	            			var pdfimageSrc = "{{ asset('public/web/images/pdf_big.png') }}";

	            			$("#image_upload_preview").hide();
	                		$("#uploaded_pdf_preview").removeAttr('src');
	                		$("#uploaded_pdf_preview").attr('src', pdfimageSrc);
	                		$("#uploaded_pdf_preview").show();

	            		} else if(getExtension == 'jpg' || getExtension == 'jpeg' || getExtension == 'png'){
	            			var imageSrc = '<?php echo MAIN_URL.WEB_UPLOAD_PATH ?>'+data.user_data.idAttachment;
	            			
	            			$("#uploaded_pdf_preview").hide();
	                		$('#image_upload_preview').removeAttr('src');
	                		$("#image_upload_preview").show();
                			$('#image_upload_preview').attr('src', imageSrc);	

	            		} else {
	            			var imageSrc = '{{ asset("public/web/images/no.png") }}';
	            			$("#uploaded_pdf_preview").hide();
	                		$('#image_upload_preview').removeAttr('src');
	                		$("#image_upload_preview").show();
	            			$('#image_upload_preview').attr('src', imageSrc);	
	            		}
	            	} else {
	            		console.log('getUserProfileRecord :', data.error);
	            	}
	            }
            });
		});

		// change MIM and IBC number
		/*$("body").on('change', '#membershipCategory', function(){
			var profileId = $('#mim_profile :selected').attr('profile_id');
			var category = $('#membershipCategory :selected').val();
			if(profileId == undefined){
				return false;
			} else {
				$.ajax({
					type: 'POST',
					url: 'updateMembershipCategory',
					data: {profileId:profileId, category:category},
					dataType: 'json',
					success: function(data){
						// console.log(data);
						if(data.code == 100){
							$("#mim_number").val(data.user_data);
						} else {
							$("#mim_number").val();
						}
					}
				})
			}
		});*/

		// de link deLinkMembership
		$("body").on('click', '.deLinkMembership', function(){
			var memberProfileId = $(this).attr('memberProfileId');
			var deLinkMemberName = $(this).attr('deLinkMemberName');
			// $("#membershipCommnetModal").modal('show');
			$('#membershipCommnetModal').modal({
		      	backdrop: 'static',
		      	keyboard: false
		    });
			$("#deLinkMemberProfileId").val(memberProfileId);
			$(".de_link_member_name").text(deLinkMemberName);
		});

		$("body").on('click', '.deLinks', function(){
			if(confirm("Are you sure? Do you want to deactivate user's membership from Melbourne Ice portal?")){
				$("#membershipCommnetModal").modal('hide');
				var deLinkMemberProfileId = $("#deLinkMemberProfileId").val();
				var deLinkComment = $("#deLinkComment").val();
				var getChangeStatusText = $("#changeStatusText"+deLinkMemberProfileId).text();
				// console.log('getChangeStatusText :', getChangeStatusText); 
				var splitString = getChangeStatusText.split(" ");				
				// disabled de-link
				$('#deMember'+deLinkMemberProfileId).attr('disabled', true);
				$('#deMember'+deLinkMemberProfileId).addClass('disable-btn');
				if(splitString[2]){
					$("#changeStatusText"+deLinkMemberProfileId).text('Inactive for '+splitString[2]);
				} else {
					$("#changeStatusText"+deLinkMemberProfileId).text('Inactive for -');
				}
				// return false;
				$.ajax({
					type: 'POST',
					url: "deLinkUserMembership",
					data: {deLinkMemberProfileId:deLinkMemberProfileId, deLinkComment:deLinkComment},
					dataType: "json",
					success: function(data){
						// console.log(data);
						if(data.code == 100){
							$("#membershipCommnetModal").modal('hide');
							// window.location = "<?php //echo PROFILE_URL ?>";
						}
					}
				});
			} else {
				return false;
			}
		});

		// upload image in 
		$('body').on('change', "#upload_file", function(){
			var fileExtension = ['jpeg', 'jpg', 'png', 'pdf'];
	        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
	            alert("Only formats are allowed : "+fileExtension.join(', '));
	            return false;
	        }
	        // var getExtension = $(this).val().split('.').pop().toLowerCase();

	        $(".image_upload_button").attr('disabled', true);
	        $(".add_to_cart_btn").attr('disabled', true);
	        $("#upload_file").attr('disabled', true);
        	$("#uploadUserFile").css('display', 'none');
	        $(".show_image_loader").show();
			// return false;
			var file = this.files[0];
			formData = new FormData();
			formData.append('upload_file', file);
			$.ajax({
	            type:'POST',
	            url:'uploadImage',
	            enctype: 'multipart/form-data',
	            data: formData,
	            processData: false,
				contentType: false,
	            success:function(data){
	            	if(data.code == "100"){	 
	            	           		// 
	            		// console.log(data.attached_image);
	            		var splitExtension = data.attached_image.split('.');
	            		var getExtension = splitExtension[1];
	            		//$(window).scrollTop(0);
	        			$(".show_image_loader").hide();
	        			$("#uploadUserFile").css('display', 'block');
	        			if(getExtension == 'pdf' || getExtension == 'PDF'){
	        				$("#uploaded_image").hide();
	        				$("#uploaded_image").attr("src", "");
	        				$("#uploaded_pdf").show();
	            			$("#uploaded_pdf").attr("src", "{{ asset('public/uploads/web_images') }}/"+data.attached_image);
	        			} else {
	        				$("#uploaded_pdf").hide();
	        				$("#uploaded_pdf").attr("src", "");
	        				$("#uploaded_image").show();
	        				$("#uploaded_image").attr("src", "{{ asset('public/uploads/web_images') }}/"+data.attached_image);	
	        			}
	            		// $("#imgModal").modal('toggle');
	            		$('#imgModal').modal({
					      backdrop: 'static',
					      keyboard: false
					    });
	                	if(getExtension == 'pdf' || getExtension == 'PDF'){
	                		var attrApend = '<?php echo MAIN_URL.WEB_UPLOAD_PATH ?>'+data.attached_image;
	                		$('#uploaded_pdf_preview').attr('pdfFileName', attrApend);
	                		var pdfimageSrc = "{{ asset('public/web/images/pdf_big.png') }}";
	                		$("#image_upload_preview").hide();
	                		$("#uploaded_pdf_preview").removeAttr('src');
	                		$("#uploaded_pdf_preview").attr('src', pdfimageSrc);
	                		$("#uploaded_pdf_preview").show();
	                	} else if(getExtension == 'jpg' || getExtension == 'jpeg' || getExtension == 'png'){
                			var imageSrc = '<?php echo MAIN_URL.WEB_UPLOAD_PATH ?>'+data.attached_image;
	                		$("#uploaded_pdf_preview").hide();
	                		$('#image_upload_preview').removeAttr('src');
	                		$("#image_upload_preview").show();
                			$('#image_upload_preview').attr('src', imageSrc);	            		
	                	} else {
	                		var imageSrc = '{{ asset("public/web/images/no.png") }}';
	                		$("#uploaded_pdf_preview").hide();
	                		$('#image_upload_preview').removeAttr('src');
	                		$("#image_upload_preview").show();
	            			$('#image_upload_preview').attr('src', imageSrc);	            		
	                	}

	            		$("#idAttachment").val('');
	            		$("#idAttachment").val(data.attached_image);

	            		$("#upload_file").attr('disabled', false);
	            		$(".image_upload_button").attr('disabled', false);
	            		$(".add_to_cart_btn").attr('disabled', false);
	            	}
	            }
            });
		});

		// faq notice 
		$('body').on('click', '#faq_data', function(){
			// $("#faqModal").modal('show');
			$('#faqModal').modal({
		      backdrop: 'static',
		      keyboard: false
		    });
		});
		$('body').on('click', '#privacyTerms', function(){
			$('#termsAndCondtionModal').modal({
		      backdrop: 'static',
		      keyboard: false
		    });
		});
		$('body').on('click', '#privacyPolicyModal', function(){
			$('#privacyPolicy').modal({
		      backdrop: 'static',
		      keyboard: false
		    });
		});
		$('body').on('click', '#uploadFileInfoMsg', function(){
			$('#uploadFileInfoMsgModal').modal({
		      backdrop: 'static',
		      keyboard: false
		    });
		});

		$('body').on('click', '#uploaded_pdf_preview', function(){
			// alert($(this).attr('pdfFileName'));
			var show_pdf_file = $("#show_pdf_file").attr('src', $(this).attr('pdfFileName'));
			$('#pdf_preview_modal').modal({
		      backdrop: 'static',
		      keyboard: false
		    });
		});		

		$('body').on('click', '.edit-profile', function(){
			var sessionKey = $(this).attr('id');
			// alert(sessionKey);
			$.ajax({
	            type:'POST',
	            url:'editCartProfile',
				dataType:'json',
	            data: {sessionKey:sessionKey},
	            success:function(data){
					if(data.code == "100"){
						var user_details = data.details;
						// console.log(data.details);						
						$('#firstName').val(user_details.firstName);
						$('#lastName').val(user_details.lastName);
						$('#profileEmail').val(user_details.profileEmail);
						$('#contactNumber').val(user_details.contactNumber);
						$('#dateofBirth').val(data.dob);
						$('#profession').val(user_details.profession);
						$('#addressLine1').val(user_details.addressLine1);
						$('#addressLine2').val(user_details.addressLine2);
						$('#suburb').val(user_details.suburb);
						$('#state').val(user_details.state);
						$('#postCode').val(user_details.postCode);
						$('#country').val(user_details.country);
						$('#sessionKey').val(sessionKey);
						$('#yearFirstJoined').val(user_details.yearFirstJoined);
						$('#edit_profile_modal').modal({
						  backdrop: 'static',
						  keyboard: false
						});	
					}
	            	
	            }
	        });
			/*$('#edit_profile_modal').modal({
		      backdrop: 'static',
		      keyboard: false
		    });*/
		});	
		$( "#editCartProfile" ).validate({
		  	rules: {
			    firstName: {
			      	required: true
			    },
			    lastName: {
			      	required: true
			    },
			    profileEmail: {
			    	required: true
			    },
			    contactNumber: {
			    	required: true,
			    	number: true
			    },
			    dateofBirth: {
			      required: true
			    },
			    profession: {
			    	required: true
			    },
			    addressLine1: {
			      required: true
			    },
			    suburb: {
			    	required: true
			    },
			    state: {
			    	required: true
			    },
			    country: {
			    	required: true
			    },
			    postCode: {
			    	required: true,
			    	number: true
			    }
		  	},
		  	messages:{
		  		firstName: "Please enter first name.",
		  		lastName: "Please enter last name.",
		  		profileEmail: "Please enter valid email.",
		  		contactNumber: {
		  			required: "Please enter contact number.",
		  			number: "Please enter number only."
		  		},
		  		dateofBirth: "Please select date of birth.",
		  		profession: "Please enter profession.",
		  		addressLine1: "Please enter address line 1.",
		  		suburb: "Please enter suburb.",
		  		state: "Please enter state.",
		  		country: "Please enter country.",
		  		postCode: {
					required: "Please enter postcode.",
					number: "Please enter number only."
				},
		  		
		  	},
		  	errorElement: "div",
        	wrapper: "div",  // a wrapper around the error message
	        errorPlacement: function(error, element) {
	            offset = element.offset();
	            error.insertAfter(element)
	            error.addClass('validation-error');  // add a class to the wrapper
	        },
		  	submitHandler: function(form) {		  		
		    	var formData = $('form').serialize();
		    	// console.log("formData :", formData);
		        $.ajax({
		            type:'POST',
		            url:'saveCartProfile',
		            data: formData,
		            dataType:'json',
		            success:function(data){
						if(data.code == "100"){
		              		location.reload();
						}
		            }
		        });
		  	}		    
		});

		// product and services (proceed_services)
		$('body').on('click', '.proceed_services', function(){
			var getValue = $("input[name=select_service]:checked").val();			
			if(getValue == null || getValue == undefined){
				$('.select_atleast_one').show();
				$('.select_atleast_one').text('');
				$('.select_atleast_one').append('<div class="alert alert-warning alert-dismissable">  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Please select at least one.</div>');
				return false;				
			} 
		});

		// Select membership type 
		$('body').on('change', '#membership_type', function() {
			if($(this).val() == '1' || $(this).val() == '2' ||  $(this).val() == '7'){
				$(".select_bay_div").show();
				$("#select_bay").show();
				$("#select_bay_label").show();
				if($("#membershipYear").val() != <?php echo MEMBERSHIP_YEAR ?>){
					$("#select_bay").val($("#select_bay option:first").val());
				}
			} else {
				$(".select_bay_div").hide();
				$("#select_bay").hide();
				$("#select_bay_label").hide();

				$(".select_row_div").hide();				
				$(".select_seats_div").hide();
				$("#seatNumber").hide();
			}

			if($(this).val() == '6'){
				$('#membershipAlertMsg').modal({
			      	backdrop: 'static',
			      	keyboard: false
			    });			    
			    $("#membershipAlertMsg .modal-body").text('Free entry in the OBGA on match days and the child is to be seated on accompanying adults’ lap. To have a dedicated seat for Under 3 children, please select the Seated Juniors option.');
				$("#membership_type").val($("#membership_type option:first").val());
				return false;
			}
			if($(this).val() == '8'){
				$('#membershipAlertMsg').modal({
			      	backdrop: 'static',
			      	keyboard: false
			    });			    
			    $("#membershipAlertMsg .modal-body").text('Free entry in the OBGA on match days and can access standing area only with the accompanying adult.');
				$("#membership_type").val($("#membership_type option:first").val());
				return false;
			}

		});

		// Choose Seats
		$('body').on('change', '#select_bay', function() {			
			$(".select_row_div").show();
			$(".select_row_form_group").show('');
			var bayNo = $("#select_bay").val();
			$.ajax({
				type:'POST',
				url:'getRowNumber',
				data:{bayNo:bayNo},
				dataType:'json',
				success:function(data){
					// console.log('data :', data);					
					$(".select_row_form_group").text('');
					$(".select_row_form_group").append(data.result);
				}
			})
			$("#select_row").val($("#select_row option:first").val());			
		});

		// choose row 
		$('body').on('change', '#select_row', function() {
			$(".select_seats_form_group").show();
			$(".select_seats_div").show();
			var bayNo = $("#select_bay").val();
			var rowNo = $(this).val();
			$("#seatNumber").val($("#seatNumber option:first").val());
			$.ajax({
	            type:'POST',
	            url:'getSeatNumber',
	            data: {bayNo:bayNo, rowNo:rowNo},
	            dataType:'json',
	            success:function(data){
	            	// console.log(data);
	            	$(".select_seats_form_group").text('');
	            	$(".select_seats_form_group").append(data.result);
	            }
			});
		});

		// DatePicker
		$(function () {
			$('.form_datetime').datepicker({
				format: 'dd-mm-yyyy',
           		autoclose: true,
           		todayHighlight : true,
           		endDate: new Date()
			});
			$('.profile_datetime').datepicker({
          		format: 'dd-mm-yyyy',
                autoclose: true,
                todayHighlight : true,
                endDate: new Date()
	        });
        });

		// validate and submit memebership form
        jQuery.validator.setDefaults({
		  debug: true,
		  success: "valid"
		});
		$( "#membership_form" ).validate({
		  	rules: {
			    select_membership: {
			      	required: true,
			    },
			    select_bay: {
			      	required: true
			    },
			    membership_type: {
			      	required: true
			    },
			    seatNumber: {
			      	required: true
			    },			    
			    mim_profile: {
			    	required: true
			    },
			    firstName: {
			      	required: true
			    },
			    lastName: {
			      	required: true
			    },
			    profileEmail: {
			    	required: true
			    },
			    contactNumber: {
			    	required: true,
			    	number: true
			    },
			    dateofBirth: {
			      required: true
			    },
			    profession: {
			    	required: true
			    },
			    addressLine1: {
			      required: true
			    },
			    suburb: {
			    	required: true
			    },
			    state: {
			    	required: true
			    },
			    country: {
			    	required: true
			    },
			    postCode: {
			    	required: true,
			    	number: true
			    },
			    privacyAcknowledged: {
			    	required: true
			    },
			    select_row: {
			    	required: true
			    }
		  	},
		  	messages:{
		  		select_membership: "Please select your membership.",
		  		select_bay: "Please select bay.",
		  		membership_type: "Please select membership type.",
		  		seatNumber: "Please select seat.",
		  		mim_profile: "Please select profile.",
		  		firstName: "Please enter first name.",
		  		lastName: "Please enter last name.",
		  		profileEmail: "Please enter valid email.",
		  		contactNumber: {
		  			required: "Please enter contact number.",
		  			number: "Please enter number only."
		  		},
		  		dateofBirth: "Please select date of birth.",
		  		profession: "Please enter profession.",
		  		addressLine1: "Please enter address line 1.",
		  		suburb: "Please enter suburb.",
		  		state: "Please enter state.",
		  		country: "Please enter country.",
		  		postCode: {
					required: "Please enter postcode.",
					number: "Please enter number only."
				},
		  		privacyAcknowledged : "Please agree on terms and conditions.",
		  		select_row: "Please select row."

		  	},
		  	errorElement: "div",
        	wrapper: "div",  // a wrapper around the error message
	        errorPlacement: function(error, element) {
	            offset = element.offset();
	            error.insertAfter(element)
	            error.addClass('validation-error');  // add a class to the wrapper
	        },
		  	submitHandler: function(form) {		  		
		    	var formData = $('form').serialize();
		    	// console.log("formData :", formData);
		        $.ajax({
		            type:'POST',
		            url:'registerMembership',
		            data: formData,
		            dataType:'json',
		            success:function(data){
		                // console.log(data);
		                $('.membership_alert_msg').text('');
		                $(".membershipButton").attr("disabled", true);
		                if(data.code == '100'){ //success	                	
		                	window.location = "<?php echo REDIRECT_ON_SAME_PAGE ?>";	                		                
		                } else if(data.code == '201') { // error
		                	$(window).scrollTop(0);
		                	$('.membership_alert_msg').show();
		                	$('.membership_alert_msg').text('');
		                	$('.membership_alert_msg').append('<div class="alert alert-danger alert-dismissable">  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+data.message+'</div>');	
		                	$(".membershipButton").attr("disabled", false);
		                } else {
		                	console.log('error while register user profile');
		                }
		            }
		        });
		  	}		    
		});
		
		// checkout form validation
		$("#checkout_payment_form").validate({
			rules: {
			    payment: {
			    	required: true,
			    },
			    setFrequency: {
			    	required: true,
			    },
			    setCycle: {
			    	required: true,
			    }
			},
			messages: {
				payment: "Please select payment method.",
				setFrequency: "Please select plan.",
				setCycle: "Please select frequency.",
			},
			errorElement: "div",
        	wrapper: "div",  // a wrapper around the error message
	        errorPlacement: function(error, element) {
	            offset = element.offset();
	            error.insertAfter(element)
	            error.addClass('validation-error');  // add a class to the wrapper
	        },
	        submitHandler: function(form) {
	        	// $("#setFrequency").attr("disabled", true);
	        	// $("#setCycle").attr("disabled", true);
	        	$(".proceed_to_payment").attr("disabled", true);
	        	$('.show_loader').show();
	        	$('#payment-process').css('display', 'none');
	        	document.getElementById("checkout_payment_form").submit();
        	}
		});

		$("body").on('change', "#setFrequency", function(){
			if($("#setFrequency").val() == "full"){
				$("#cycleInMonth").hide();
			} else {
				$("#cycleInMonth").show();
			}
		});

		// profile validation
		$("#update_profile_form").validate({
			rules: {
				firstName: {
			      	required: true,
			    },
			    lastName: {
			    	required: true,
			    },
			    dateofBirth: {
			    	required: true,
			    },
			    contactNumber: {
			    	required: true,
			    	number: true
			    },
			    profession: {
			    	required: true,
			    },
			    addressLine1: {
			    	required: true,
			    },
			    suburb: {
			    	required: true,
			    },
			    state: {
			    	required: true,
			    },
			    postCode: {
			    	required: true,
			    	number: true
			    },
			    country: {
			    	required: true,
			    }
			},
			messages: {
				firstName: "Please enter first name.",
				lastName: "Please enter last name.",
				dateofBirth: "Please select date of birth.",
				contactNumber:{
						required: 'Please enter contact number.',
						number: 'Please enter number only.'
					},
				profession: "Please enter profession.",
				addressLine1: "Please enter address line1.",
				suburb: "Please enter suburb.",
				state: "Please enter state.",
				postCode: {
					required: "Please enter postcode.",
					number: "Please enter number only."
				},
				country: "Please enter country.",
			},
			errorElement: "div",
        	wrapper: "div",  // a wrapper around the error message
	        errorPlacement: function(error, element) {
	            offset = element.offset();
	            error.insertAfter(element)
	            error.addClass('validation-error');  // add a class to the wrapper
	        },
	        submitHandler: function(form) {
	        	document.getElementById("update_profile_form").submit();
        	}
		});

		// contact us validation 
		$("#contactus").validate({
			rules: {
				name: {
					required: true
				},
				contact_number: {
					required: true,
					number: true
				},
				email: {
					required: true
				},
				message: {
					required: true
				}
			},
			messages: {
				name: "Please enter name.",
				contact_number: {
					required: "Please enter contact number.",
					number: "Please enter number only."
				},
				email: "Please enter email.",
				message: "Please enter message."
			},
			errorElement: "div",
        	wrapper: "div",  // a wrapper around the error message
	        errorPlacement: function(error, element) {
	            offset = element.offset();
	            error.insertAfter(element)
	            error.addClass('validation-error');  // add a class to the wrapper
	        },
	        submitHandler: function(form) {
	        	document.getElementById("contactus").submit();
        	}
		});

		// payway payment gateway alert
		$('body').on('click', '.payway_payment_gateway', function(){
			$(".proceed_to_payment").attr("disabled", true);
			$('.payway_alert_msg').show();
        	$('.payway_alert_msg').text('');
        	$('.payway_alert_msg').append('<div class="alert alert-warning alert-dismissable">  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>Coming soon.</div>');
        	$(".paypal_required_fields").hide();
        	$("#setFrequency").val($("#setFrequency option:first").val());
		});

		$('body').on('click', '.paypal_payment_gateway', function(){
			$(".proceed_to_payment").attr("disabled", false);
			$('.payway_alert_msg').hide();
			$(".paypal_required_fields").show();
		});

		// Get the modal
		var modal = document.getElementById('myModalImage');
		// Get the image and insert it inside the modal - use its "alt" text as a caption
		var img = document.getElementById('image_upload_preview');
		var modalImg = document.getElementById("img01");
		// var captionText = document.getElementById("caption");
		if(img != null){
			img.onclick = function(){
			    modal.style.display = "block";
			    modalImg.src = this.src;
			    // captionText.innerHTML = this.alt;
			}
		}
		// Get the <span> element that closes the modal
		var span = document.getElementsByClassName("fancy-close")[0];
		if(span != null){
			span.onclick = function() { 
			    modal.style.display = "none";
			}
		}

		$('body').on('click', '.termsOk', function(){
			$("#privacyAcknowledged").prop('checked', true);
		});
		$('body').on('click', '.privacyOk', function(){
			$("#privacyAcknowledged").prop('checked', true);
		});		    	

	});
	
	//  form label focus
	$("input").focus(function(){
	  	$("input").parents('.form-group').removeClass("is-focused");
	    $(this).parents('.form-group').toggleClass("is-focused");
	});
	$("input").focusout(function(){
	  	$(this).parents('.form-group').toggleClass("is-focused");
	});

</script>	

	<!-- Terms and condtion modal -->
	<div class="modal" id="termsAndCondtionModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header alert-info">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Melbourne Ice Membership Charter, Terms and Conditions</h4>
	      </div>
	      <div class="modal-body charter"> 
		    <h1>Introduction</h1>
		    <h2>The Melbourne Ice Limited</h2>
		    <p>The <b>Melbourne Ice</b> is a semi-professional ice hockey team in the Australian Ice Hockey League [AIHL]</p>
		    <p>The team plays its home games at the O'Brien Group Arena [OBGA] in Melbourne, which was completed in 2010. </p>
		    <p>The Melbourne Ice are Australia’s pre-eminent Ice Hockey Team and have won the National Championship four times over the last 15 years winning a three-peat of premierships in 2010 – 2012 and then again in 2017.</p>
		    <p>In 2017 the club transformed into an unlisted public company and is now known as the The Melbourne Ice Limited. </p>
		    <p>Melbourne Ice offers fans and supporters the opportunity to purchase memberships on an annual basis which includes Club Membership, Discounted Seats, Discounted Merchandise and other exclusive benefits.</p>
		    <h1>Membership Types</h1>
		    <p>Melbourne Ice Limited offers four classification of Members:</p>
		    <div class="section">
			    <h2>Reserved Seat Memberships</h2>
			    <p class="without-margin">Referred to as Reserved Seat Membership and does include the same numbered seat for the entire AIHL season. </p>
			    <p class="without-margin">Adult: Any person over the age of 18#</p>
			    <p class="without-margin">Concession & Youth (15-18): IHV Registered Player# Membership or Holder of a Statutory Concession Card.</p>
			    <p class="without-margin">Junior:  Any child up to the age of 15# requiring a reserved seat or General Admission Seat. Babe in Arms: Any child under the age of three, held by the parents or guardian.</p>
			</div>
			<div class="section">
				<h2>General Admission (STANDING Only)</h2>
				<p class="without-margin">Referred to as General Admission and does not include a dedicated or guaranteed seat in the stands.</p>
				<p class="without-margin">Adult: Any person over the age of 18#</p>
				<p class="without-margin">Concession & Youth (15-18):  IHV Player Membership or Holder of a Statutory Concession Card.</p>
				<p class="without-margin">Junior: Any child up to the age of 14# requiring a reserved seat or General Admission Seat. </p>
				<p class="without-margin">Babe in Arms: Any child under the age of three, held by the parents or guardian.</p>
			</div>
			<div class="section">
				<h2>Distance or Remote Supporters </h2>
				<p>This is non-ticketed distant membership which does not include game entry or a seat to any AIHL game throughout the season.</p>
			</div>
			<h2>Life Members</h2>
			<p>Complimentary Memberships that will be offered by the club including a dedicated Numbered Seat. </p>
			<h2>Leadership Team & Volunteers</h2>
			<p>Subsidized Memberships including a Numbered Seat. </p>
			<p>The club may at its discretion introduce a new or modified classification of Membership at any time during the year.</p>
			<h1>Membership Purchase</h1>
			<p>Memberships are only available to purchase online through the Melbourne Ice Community Portal. Payments are accepted by Credit Card, Pay Pal and also provide for a Payment Plan option.</p>
			<p>If payment plan options are selected, the Payment Plan must be complete before the first of April every year. </p>
			<p>If you are successful in your application for membership, the Club will send you an acceptance letter confirming your membership and membership benefits.</p>
			<p>The Club reserves the right to not accept any memberships application at its absolute discretion.</p>
			<h1>Membership Terms</h1>
			<p>Upon the successful purchase for membership, Melbourne Ice will make available to the member fulfillment pack and membership card.</p>
			<p>Member fulfillment packs and Membership cards will be available either by personal pick-up at a nominated Pre-season launch event or by direct mail.</p>
			<p>Melbourne Ice reserves the right to not accept any application form at its absolute discretion and also reserves the right to suspend or cancel a membership without refund to any member that breaches these terms and conditions or, in the opinion of the Club’s board of directors, is guilty of conduct  unbecoming of a member or prejudicial to the interests of the Club. </p>
			<p>Memberships are not transferable and can ONLY be used for the person nominated on the Membership Card. Melbourne Ice reserve the right to not admit any person not carrying a dedicated named Membership Card.</p>
			<p>The membership card remains the property of Melbourne Ice at all times. Membership cards may not be transferred, sold, exploited for commercial use, or used for promotional purposes or campaigns without the express written permission of Melbourne Ice Limited.</p>
			<h1>Membership Rights</h1>
			<p>Applicable Games </p>
			<ul>
			    <li>All home games at the Obrien Group Arena [April-September]</li>
			    <li>All Melbourne Ice Women’s Team Home games [October – March]</li>
			</ul>
			<h2>Non-Applicable Games</h2>
			<ul>
			    <li>Pre-season or Post Season Tournaments [either home or away].</li>
			    <li>Exhibition Games.</li>
			    <li>Away Derbies where the Melbourne Mustangs have the ‘Home Game’.</li>
			    <li>All-star weekends.</li>
			    <li>Finals weekend.</li>
			    <li>Seats in the VIP or Corporate Hospitality areas throughout the rink.</li>
			</ul>
			<h2>Non-Transferable</h2>
			<p>Membership Cards and the associated numbered seats are not transferable for any games or for any reason.</p>
			<h2>Access to the Pre-Season & Jersey Presentation Event</h2>
			<p>Members will have free access to the annual pre-season and Jersey Presentation event.</p>
			<h2>Access to the Post Season Event</h2>
			<p>Members will receive free access to our post season wrap-up and celebration event.</p>
			<h2>Access to the Post Game Clubhouse</h2>
			<p>Members gave free access to the post game events social club with location of this post match function to be promoted through all membership communication. Members must present their 2017/18 membership card upon request and if you cannot produce your 2017/18 membership card, you will not be granted free admittance and will be requested to pay the entry fee.</p>
			<h2>Discount on Club Merchandise</h2>
			<p>Members will also receive 20% discount on all club merchandise purchased on the Melbourne Ice On-Line Store. Members will also be able to pick up these purchases at the next home game as long as the purchases take place by ‘close of business’ Tuesday prior to that home game. Purchases that are made after ‘close of business’ Tuesday prior to the next home game can elect to pick up at the next game or receive by surface mail at the standard delivery cost. This discount does not apply to Delivery or Handling charges.</p>
			<p><b>Note:</b> Discounts are not available to Limited edition ‘Fanware and Memorabillia” [including Pre-Worn Game Jerseys]. </p>
			<h2>Meet and Skate with the Players</h2>
			<p>Members will have free access to at least one annual meet and skate with the players, leadership team and coaches.</p>
			<h2>Member Feedback Sessions </h2>
			<p>Members are entitled to attend Member Feedback sessions on a range of subjects throughout the year.</p>
			<h1>Lost Cards </h1>
			<p>The Melbourne Ice Ltd. is not responsible for membership cards that are lost, stolen or destroyed. </p>
			<p>Lost membership cards must be reported immediately by emailing Member Services on XXXXXXXX and a replacement card will be issued within 10 business days. Please note that the charge of $20.00 per card will be applied. A temporary admission ticket will only be allowed if the Member has reported the lost card via the process above. </p>
			<p>Admission to games will require an accepted form of identification.</p>
			<h1>Refunds </h1>
			<p>Refunds may only be requested for consideration up to the beginning of the Annual AIHL Season. </p>
			<p>Any person seeking a refund must do so in writing, addressed to the Membership Manager @ memberships@melbourneice.com.au. By doing so, the member’s request will be taken in to consideration, however they are not guaranteed a refund. Members will need to return their core membership pack and membership card to the Club before a refund is processed. If approved a refund will be processed and made within 20 business days.</p>
			<h1>Special Needs</h1>
			<p>If a member has special needs, and holds a state issued Companion card, the member is eligible to receive a second membership, of the same category as they purchase, at no charge. This membership can then be used by a caretaker when attending ice hockey matches with that member. Note the Companion Card must reference the relevant Melbourne Ice member.</p>
			<h1>Members Code of Conduct</h1>
			<p>It is important that all spectators at a Melbourne Ice AIHL approved game are able to enjoy the game in a safe and comfortable environment;</p>
			<p class="without-margin">Accordingly, each person present at a scheduled Melbourne Ice AIHL game must;</p>
			<ul>
			    <li>Respect the decisions of match officials</li>
			    <li>Respect the rights of each person present at a game</li>
			    <li>Not engage in the use of violence or intimidation at any time</li>
			    <li>Comply with the venue condition of entry</li>
			    <li>Conduct themselves in a manner and goodwill of the family nature of Melbourne Ice.</li>
			</ul>
			<p>Any person that does not comply with this code or who in the opinion of the club chooses not to adhere to these requirements, may be subject to sanctions</p>

	      </div>
	      <div class="modal-footer alert-info">
	      <button type="button" class="btn blue termsOk" data-dismiss="modal">OK</button>
	      <!-- <a href="{{ route('termsAndcondition') }}" class="btn btn-mine">Download as PDF</a> -->
	      </div>
	    </div>
	  </div>
	</div>
	<!-- FAQ modal -->
	<div class="modal" id="faqModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header alert-info">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">FAQs</h4>
	      </div>
	      <div class="modal-body faq">	
	      	<ul>
	      		<li>What does ‘First year joined’ field indicate?<br><span>This is the first year when you became a member of Melbourne Ice.</span></li>
	      		<li>Can I edit ‘First year joined’?<br><span>No, this information is collected from our members’ database. However, if this is incorrect please advise our Membership Manager at: <a href="mailto:pratik.garud@melbourneice.com.au">pratik.garud@melbourneice.com.au</a> with the correct information details.</span></li>
	      		<li>Can I update my user profile at any time?<br><span>Whenever you make that important change in your life, we would like you to tell us about it. You can update your user details at any time.</span></li>
	      		<li>Who should select “New” membership?<br><span>If you <u>NEVER</u> have been a Melbourne Ice member before (between 2010 to 2017), then you should choose “New” membership.</span></li>
	      		<li>Who should select “Renew” membership?<br><span>If you have been a Melbourne Ice member before (any time between 2010 to 2017), then you should choose “Renew”.</span></li>
	      		<li>How many “New” memberships can I buy?<br><span>Other than your own membership, you can add multiple “New” memberships for your family, friends and loved ones using the same account.</span></li>
	      		<li>How many “Renew” memberships can I buy?<br><span>You can <u>only</u> “Renew” membership profiles linked with your account. However, you can add multiple “New” memberships for your family, friends and loved ones using the same account.</span></li>
	      		<li>How are “Renew” membership profiles linked?<br><span>Members (2017 and prior) with the same email address against their name have been linked together in one account.</span></li>
	      		<li>What do I do if the “Renew” membership profile linked to my account is wrong or does not belong to me?<br><span>In such a case, kindly advise our Membership Manager at: <a href="mailto:pratik.garud@melbourneice.com.au">pratik.garud@melbourneice.com.au</a></span></li>
	      		<li>What are the membership categories and options?<br><span>Please refer to the ‘Home’ page for detailed information regarding the 2018 season membership.</span></li>
	      		<li>What are my rights, benefits and responsibilities as a member?<br><span>For this information, please read our Member Charter and Terms & Conditions carefully.</span></li>
	      		<li>Is my personal information safe with Melbourne Ice?<br><span>Please read our Privacy policy to know more about how Melbourne Ice manages and uses your personal information.</span></li>
	      		<li>How can I pay for my Melbourne Ice membership(s)?<br><span>Melbourne Ice uses PayPal as its payment gateway. It enables Melbourne Ice to offer you the option to pay for your membership(s) in instalments (up to 30th March 2018).</span></li>
	      		<li>What does it mean when I chose to pay in instalments?<br><span>If you choose to pay in instalments, then we can only disperse the membership cards after receiving the final instalment.</span></li>
	      		<li>What happens if I miss one of my payments?<br><span>Don’t worry! Melbourne Ice will contact you to work out an alternative payment solution plan so that you can still obtain your membership.</span></li>
	      		<li>When will the 2017 season reserved seats open for purchase to other fans and members?<br><span>On 1st March 2018, the reserved seats which are not renewed by 2017 members, will be available for anyone to purchase.</span></li>
	      		<li>Who should NOT buy membership with this portal?<br><span>You should not buy a membership if you are one of the following:</span>
	      			<ul>
	      				<li><u>Hold a player’s pass</u>: The club will get in touch with you regarding the payment for this seat. Meanwhile, you can contact us via email with your seating preference. You will be assigned a unique membership number starting with “MIP xxxx”</li>
	      				<li><u>IBC member</u>: The seats will be allotted by the club. You will be assigned a unique IBC membership number starting with “IBC xxxx”</li>
	      				<li><u>Life member</u>: If you are a life member, then the club has already sent out an email communication regarding your complimentary seats. If you have not received this email, then do write to us.</li>
	      				<li><u>Sponsor/VIP</u>: Sponsor seating is handled by the club and you don’t need to buy your membership through this portal.</li>
	      			</ul>
	      		</li>
	      	</ul>
	      	<p>If you were not able to find an answer to your questions here, please write to our Membership Manager at: <a href="mailto:pratik.garud@melbourneice.com.au">pratik.garud@melbourneice.com.au</a>. Please allow us at least 1 business day to get back to your query. Thank you!</p>
			    
	      </div>
	      <!-- <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div> -->
	    </div>
	  </div>
	</div>

	<!-- pdf preview modal -->
	<div class="modal" id="pdf_preview_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header alert-info">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">ID Attachment</h4>
	      </div>
	      <div class="modal-body">
	      	<embed src="" id="show_pdf_file">
	      </div>
	      <div class="modal-footer alert-info">
	        <button type="button" class="btn blue ok-btn" data-dismiss="modal">OK</button>
	      </div>
	    </div>
	  </div>
	</div>
	
	<!-- Image modal -->
	<div class="modal" id="imgModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header alert-info">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">ID Attachment</h4>
	      </div>
	      <div class="modal-body upload_id">
	      	<div class="alert alert-success alert-dismissable">ID attachment uploaded successfully.</div>
	      		<embed src="" id="uploaded_pdf" frameborder="0" style="display: none;">
	      		<img src="" id="uploaded_image" style="display: none;">
	      </div>
	      <div class="modal-footer alert-info">
	        <button type="button" class="btn blue ok-btn" data-dismiss="modal">OK</button>
	      </div>
	    </div>
	  </div>
	</div>
	<!-- user Commnet model -->
	<div class="modal" id="membershipCommnetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header alert-info">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Confirmation!</h4>
	      </div>
	      <div class="modal-body">
	      		<input type="hidden" name="deLinkMemberProfileId" id="deLinkMemberProfileId">
	      		<div class="de_link_member_name"></div>
	      		<textarea class="form-control" name="deLinkComment" id="deLinkComment" placeholder="Comments"></textarea>
	      </div>
	      <div class="modal-footer alert-info">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	      	<button type="button" class="btn btn-primary deLinks">De-link</button>
	      </div>
	    </div>
	  </div>
	</div>
	<!-- membership type alert model -->
	<div class="modal" id="membershipAlertMsg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header alert-info">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Alert!</h4>
	      </div>
	      <div class="modal-body">
	      </div>
	      <div class="modal-footer alert-info">
	        <button type="button" class="btn blue" data-dismiss="modal">OK</button>	      	
	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal" id="privacyPolicy" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header alert-info">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Privacy Policy</h4>
	      </div>
	      <div class="modal-body privacy-policy">	        	
		        <p class="tnc-pp-date">Last Updated: January 2018</p>
		        <p>At MELBOURNE ICE, we take privacy very seriously. We’ve updated our privacy policy (Policy) to ensure that we communicate to you, in the clearest way possible, how we treat personal information. We encourage you to read this Policy carefully. It will help you make informed decisions about sharing your personal information with us.</p>
		        <p>The defined terms in this Policy have the same meaning as in our Terms of Use, which you should read together with this Policy. By accessing our Website and using our Service, You consent to the terms of this Policy and agree to be bound by it and our Terms of Use.</p>
		        <h2>MELBOURNE ICE collects your personal information</h2>
		        <p>The MELBOURNE ICE Community Portal is an online ecosystem that lets you login, anytime, anywhere on your Mac, PC, tablet or phone to get a real-time view of your membership and club services. </p>
		        <p>The Service involves the storage of Data about a company or individual. That Data can include personal information. “Personal information” (being information about a person from which their identity is apparent or can reasonably be determined). This information can include names, dates of birth, email addresses, home and work addresses, telephone numbers, bank account details, videos, and photographs. We will collect such information by lawful and fair means and not in an unreasonably intrusive way. MELBOURNE ICE may collect personal information directly from you when you use the Community Portal.</p>
		        <p>You can always choose not to provide your personal information to MELBOURNE ICE, but it may mean that we are unable to provide you with the Service. </p>
		        <h2>MELBOURNE ICE may receive personal information from you about others</h2>
		        <p>Through Your use of the Service, MELBOURNE ICE may also collect information from you about someone else. If You provide MELBOURNE ICE with personal information about someone else, You must ensure that You are authorised to disclose that information to MELBOURNE ICE and that, without MELBOURNE ICE taking any further steps required by applicable data protection or privacy laws, MELBOURNE ICE may collect, use and disclose such information for the purposes described in this Policy.</p>
		        <p>This means that You must take reasonable steps to ensure the individual concerned is aware of and/or consents to the various matters detailed in this Policy, including the fact that their personal information is being collected, the purposes for which that information is being collected, the intended recipients of that information, the individual's right to obtain access to that information, MELBOURNE ICE’s identity, and how to contact MELBOURNE ICE.</p>
		        <p>Where requested to do so by MELBOURNE ICE, You must also assist MELBOURNE ICE with any requests by the individual to access or update the personal information you have collected from them and entered into the Service.</p>
		        <h2>MELBOURNE ICE collects, holds, and uses your personal information for limited purposes</h2>
		        <p>MELBOURNE ICE collects your personal information so that we can provide you with the Service and any related services you may request. In doing so, MELBOURNE ICE may use the personal information we have collected from you for purposes related to the Services including to: verify Your identity, administer the Service, notify You of new or changed services offered in relation to the Service, carry out marketing or training relating to the Service, assist with the resolution of technical support issues or other issues relating to the Service, comply with laws and regulations in applicable jurisdictions, and communicate with You.</p>
		        <p>By using the Service, You consent to your personal information being collected, held and used in this way and for any other use you authorise. MELBOURNE ICE will only use your personal information for the purposes described in this Policy or with your express permission.</p>
		        <p>It is your responsibility to keep your password to the Service safe. You should notify us as soon as possible if you become aware of any misuse of your password, and immediately change your password within the Service or via the “Forgotten Password” process.</p>
		        <h2>MELBOURNE ICE can aggregate your non-personally identifiable data</h2>
		        <p>By using the Service, You agree that MELBOURNE ICE can access, aggregate and use non-personally identifiable data MELBOURNE ICE has collected from you. This data will in no way identify you or any other individual.</p>
		        <p>MELBOURNE ICE may use this aggregated non-personally identifiable data to: assist us to better understand how our customers are using the Service, provide our customers with further information regarding the uses and benefits of the Service, otherwise to improve the Service.</p>
		        <h2>MELBOURNE ICE may hold your personal information on servers located outside of Australia.</h2>
		        <p>MELBOURNE ICE will store personal information on data servers that are controlled by MELBOURNE ICE and are located within the geographical borders of Australia where reasonable possible. However, in some circumstances the personal information that MELBOURNE ICE collects may be disclosed to certain recipients, and stored at certain destinations, located outside Australia from time to time. For example, your personal information may be transferred outside of Australia if any of MELBOURNE ICE’s servers from time to time are located outside Australia or if one of MELBOURNE ICE’s service providers or suppliers is located in a country outside Australia. As at the date of this Policy, MELBOURNE ICE utilises service providers and suppliers in the USA and Canada. MELBOURNE ICE may also disclose your personal information outside of Australia in accordance with section 5 (Disclosing Your Personal Information), including to facilitate the registration of a player by an international ice hockey association.</p>
		        <h2>MELBOURNE ICE takes steps to protect your personal information</h2>
		        <p>MELBOURNE ICE is committed to protecting the security of your personal information and we take all reasonable precautions to protect it from unauthorised access, modification or disclosure. </p>
		        <p>However, the Internet is not in itself a secure environment and we cannot give an absolute assurance that your information will be secure at all times. Transmission of personal information over the Internet is at your own risk and you should only enter, or instruct the entering of, personal information to the Service within a secure environment.</p>
		        <p>We will advise you at the first reasonable opportunity upon discovering or being advised of a security breach where your personal information is lost, stolen, accessed, used, disclosed, copied, modified, or disposed of by any unauthorised persons or in any unauthorised manner.</p>
		        <h2>MELBOURNE ICE only discloses Your Personal Information in limited circumstances</h2>
		        <p>MELBOURNE ICE will only disclose the personal information you have provided to us to entities outside the MELBOURNE ICE group of companies if it is necessary and appropriate to facilitate the purpose for which your personal information was collected pursuant to this Policy, including the provision of the Service.</p>
		        <p>MELBOURNE ICE will not otherwise disclose your personal information to a third party unless you have provided your express consent. However, you should be aware that MELBOURNE ICE may be required to disclose your personal information without your consent in order to comply with any court orders, subpoenas, or other legal process or investigation including by tax authorities, if such disclosure is required by law. Where possible and appropriate, we will notify you if we are required by law to disclose Your personal information.</p>
		        <p>The third parties who host our servers do not control, and are not permitted to access or use your personal information except for the limited purpose of storing the information. This means that, for the purposes of Australian privacy legislation and Australian users of the Service, MELBOURNE ICE does not currently “disclose” personal information to third parties located overseas.</p>
		        <h2>MELBOURNE ICE does not store your credit card details</h2>
		        <p>If you choose to pay for the Service by credit card, your credit card details are not stored by the Service and cannot be accessed by MELBOURNE ICE staff. Your credit card details are encrypted and securely stored by PayPal to enable MELBOURNE ICE to automatically bill your credit card or PayPal account on a recurring basis. You should review PayPals Privacy Policy to ensure you are happy with it.</p>
		        <h2>You may request access to your personal information</h2>
		        <p>It is your responsibility to ensure that the personal information you provide to us is accurate, complete and up-to-date. You may request access to the information we hold about you, or request that we update or correct any personal information we hold about you, by setting out your request in writing and sending it to us at info@melbourneice.com.</p>
		        <p>MELBOURNE ICE will process your request as soon as reasonably practicable, provided we are not otherwise prevented from doing so on legal grounds. If we are unable to meet your request, we will let you know why. For example, it may be necessary for us to deny your request if it would have an unreasonable impact on the privacy or affairs of other individuals, or if it is not reasonable and practicable for us to process your request in the manner you have requested. In some circumstances, it may be necessary for us to seek to arrange access to your personal information through a mutually agreed intermediary (for example, the Subscriber).</p>
		        <p>We’ll only keep your personal information for as long as we require it for the purposes of providing you with the Service. However, we may also be required to keep some of your personal information for specified periods of time, for example under certain laws relating to corporations, money laundering, and financial reporting legislation.</p>
		        <h2>MELBOURNE ICE uses cookies</h2>
		        <p>In providing the Service, MELBOURNE ICE utilises "cookies". A cookie is a small text file that is stored on your computer for record-keeping purposes. A cookie does not identify you personally or contain any other information about you but it does identify Your computer.</p>
		        <p>We and some of our affiliates and third-party service providers may use a combination of “persistent cookies” (cookies that remain on Your hard drive for an extended period of time) and “session ID cookies” (cookies that expire when You close Your browser) on the Website to, for example, track overall site usage, and track and report on Your use and interaction with ad impressions and ad services.</p>
		        <p>You can set your browser to notify you when you receive a cookie so that you will have an opportunity to either accept or reject it in each instance. However, you should note that refusing cookies may have a negative impact on the functionality and usability of the Website.</p>
		        <h2>We do not respond to or honour “Do Not Track” requests at this time.</h2>
		        <p>You can opt-out of any email communications</p>
		        <p>MELBOURNE ICE sends billing information, product information, Service updates and Service notifications to you via email. Our emails will contain clear and obvious instructions describing how you can choose to be removed from any mailing list not essential to the Service. MELBOURNE ICE will remove you at your request.</p>
		        <p>You are responsible for transfer of your data to third-party applications</p>
		        <p>The Service may allow you, the Subscriber, or another Invited User within the relevant subscription to the Service to transfer Data, including your personal information, electronically to and from third-party applications. MELBOURNE ICE has no control over, and takes no responsibility for, the privacy practices or content of these applications. You are responsible for checking the privacy policy of any such applications so that you can be informed of how they will handle personal information.</p>
		        <h2>MELBOURNE ICE has a privacy complaints process</h2>
		        <p>If you wish to complain about how we have handled your personal information, please provide our Privacy Officer with full details of your complaint and any supporting documentation:  by e-mail at info@melbourneice.com.au, or by letter to The Privacy Officer, MELBOURNE ICE PTY LTD, North Building 3/333 Collins Street Melbourne 3000.</p>
		        <p>Our Privacy Officer will endeavour to: provide an initial response to Your query or complaint within 10 business days, and investigate and attempt to resolve Your query or complaint within 30 business days or such longer period as is necessary and notified to you by our Privacy Officer.</p>
		        <p>MELBOURNE ICE reserves the right to change this Policy at any time, and any amended Policy is effective upon posting to this Website. MELBOURNE ICE will make every effort to communicate any significant changes to you via email or notification via the Service. Your continued use of the Service will be deemed acceptance of any amended policy.</p>
	      	
	      </div>
	      <div class="modal-footer alert-info">
	        <button type="button" class="btn blue privacyOk" data-dismiss="modal">OK</button>
	        <!-- <a href="{{ route('privacyAndpolicy') }}" class="btn btn-mine" >Download as PDF</a> -->
	      </div>
	    </div>
	  </div>
	</div>

	<div class="modal" id="uploadFileInfoMsgModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header alert-info">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Alert!</h4>
	      </div>
	      <div class="modal-body">
      			<p><i>Concession memberships apply to:</i></p>
				<p><i>-Australian Pensioner Concession Card</i></p>
				<p><i>-Full-time Student Card</i><p>
				<p><i>-Healthcare Card</i><p>
				<p><i>-Disability Concession</i><p>
				<p><i>-Seniors Business Discount Card holders or DVA Gold Card holders and</i><p>
				<p><i>-All IHV registered players</i><p>
				<p><i>-Youth (15 to 18) as of 31/12/2017 should provide a valid age proof</i><p>
	      </div>
	      <div class="modal-footer alert-info">
	        <button type="button" class="btn blue" data-dismiss="modal">OK</button>	      	
	      </div>
	    </div>
	  </div>
	</div>

</body>
</html>