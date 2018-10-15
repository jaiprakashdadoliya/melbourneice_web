<?php
/**
* Application globally constants are define here and this file is auto loaded on whole project
* Author: Jaiprakash Dadoliya
* Author Email: @gmail.com
*/ 

/* APP Name */
define('APP_NAME', 'MelbourneIce');
/* General paths */
define('ADMIN_PATH', 'admin');
/* DB Tables */
define('USER', 'users');
define('ADMIN_USER', 'admin_users');
define('PROFILE', 'profiles');
define('USER_LOGONS', 'userLogons');
define('MEMBERSHIP', 'memberShips');
define('SEAT_ALLOCATION', 'seatAllocations');
define('TRANS_HISTORY', 'transaction_history');
define('BAY', 'bay');
define('ROW', 'row');
define('SEATS', 'seats');
define('HISTORY', 'membershipHistory');
define('MEMBER_PRICE', 'memberShipTypePrices');
define('MEMBER_TYPES', 'memberShipTypes');

// Peoject URl
define('CHECKOUT_URL', 'http://community.melbourneice.com.au/checkout');
define('REDIRECT_ON_SAME_PAGE', 'http://community.melbourneice.com.au/membership');
define('MAIN_URL', 'http://community.melbourneice.com.au/');
define('PROFILE_URL', 'http://community.melbourneice.com.au/profile');

// http://fxbytes.com/Client/melbourneIce/checkout

/* Uploads Path */
define('ADMIN_UPLOAD_PATH', 'public/uploads/admin_images/');
define('WEB_UPLOAD_PATH', 'public/uploads/web_images/');
define('LOGIN_LOGO', 'public/web/images/MI_keyline_logo.png');
define('HOME_PAGE_LOGO', 'public/web/images/MI_logo.png');
define('CHANGE_PASSWORD_PAGE_LOGIN_LOGO', 'http://community.melbourneice.com.au/public/web/images/MI_keyline_logo.png');
define('CHANGE_PWD_INFO_PATH', 'http://community.melbourneice.com.au/public/web/images/info.png');

/* Rest API Constants */
define('SUCCESS', '100');
define('ERROR', '200');
define('SERVER_ERROR', '300');
define('MISSING_PARAM', '400');
define('SUCCESS_MSG', 'SUCCESS');
define('ERROR_MSG', 'ERROR');
define('SERVER_MSG', 'SERVER_ERROR');
define('GENERAL_ERROR', '201');

define('PAID_FEES', '5.00');
define('MEMBERSHIP_YEAR', 2017);

/* Email constants */
define('FROM_EMAIL', 'office@melbourneice.com.au');
define('ADMIN_EMAIL', 'pratik.garud@melbourneice.com.au'); 
define('WEB_URL', 'http://community.melbourneice.com.au');
define('CHANGE_PASSWORD_FRONT_SLUG', '/change-password/');
define('EMAIL_VERIFICATION_FRONT_SLUG', '/verify-email/');

define('FORGOT_PASSWORD_SUBJECT', 'Password Reset for Melbourne Ice account');

/* Message Constants for admin */
// define('FORGOT_PASSWORD_MESSAGE', 'Dear %s,<br/><br/>Here is your new password : <b>%s</b><br/><br/>If you need any further support, please do not hesitate to contact us at any time.<br/><br/><br/>Regards,<br/>Site Manager');

/* Message for web */
define('FORGOT_PASSWORD_MESSAGE_WEB', 'Hi %s,<br/><br/>Please click on the below link to create a new password and login to the Melbourne Ice Community Portal.<br/><a href="%s">Click here</a><br/><br/>Thank you,<br/>Melbourne Ice Crew');


define('FORGOT_PASSWORD_SUBJECT_NOT_FOUND', 'Unable to generate password for Melbourne Ice portal');
define('FORGOT_PASSWORD_MESSAGE_WEB_NOT_FOUND', 'Hi User,<br/><br/>Sorry we do not have the email address you entered on file.  Please contact the Membership Manager Pratik Garud (pratik.garud@melbourneice.com.au) and advise your new email address.<br/><br/>Thank you,<br/>Melbourne Ice Crew');

/* Mail varification */
define('EMAIL_VERIFICATION_SUBJECT', 'Verification email for Melbourne Ice account');
define('EMAIL_VERIFICATION_MESSAGE', 'Hi %s,<br/>You are successfully linked with Melbourne Ice Community.<br/><br/>Please click on the below link to verify your email account.<br/><a href="%s">Click here</a><br/><br/>Thank you,<br/>Melbourne Ice Crew');

define('EMAIL_VERIFICATION_MESSAGE_WITH_PASSWORD', 'Hi %s,<br/>Temporary Password is <b>%s</b>.<br/><br/>Please click on the below link to verify your email account.<br/><a href="%s">Click here</a><br/><br/>Thank you,<br/>Melbourne Ice Crew');

/* De-link mail */
define('DE_LINK_SUBJECT', 'De-link from Melbourne Ice membership portal');
define('DE_LINK_MESSAGE', 'Hello %s,<br/><br/>Your membership is deactivated from Melbourne Ice portal.<br/>Please contact the administrator.<br/><br/>Thank you,<br/>Melbourne Ice Crew');

define('DE_LINK_MESSAGE_COMMNET', 'Hello %s,<br/><br/>Your membership is deactivated from Melbourne Ice portal.<br/><b>Comments:</b> %s<br/><br/>Please contact the administrator.<br/><br/>Thank you,<br/>Melbourne Ice Crew');

/* Contact Us mail*/
define('CONTACT_SBUJECT', 'A new user query');
define('CONTACT_MESSAGE', 'Hi Support Team,<br/><br/>Please find out the contact details of a user.
<br/>Name: %s<br/>Email: %s<br/>Contact Number: %s<br/>Message: %s<br/>
<br/>Thank you,<br/>Melbourne Ice Crew');

/* RS Membership bokking email */
define('MEMBERSHIP_SUBJECT_RS', 'Membership Purchase Confirmation');
define('MEMBERSHIP_MSG_RS', 'Hello %s,<br><br>Your booking request has been confirmed.<br><br><b>Membership Details</b><br><table><tr style="vertical-align: top;"><td>Memberships</td><td>:</td><td>%s</td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3"><b>Payment Details</b></td></tr><tr><td>Amount Due</td><td>:</td><td>$%s</td></tr><tr><td>Total Instalments</td><td>:</td><td>%s</td></tr><tr><td>Instalments remaining</td><td>:</td><td>%s</td></tr><tr><td>Instalment Frequency</td><td>:</td><td>%s</td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3"><b>Billing Details</b></td></tr><tr><td>Payment Deduction</td><td>:</td><td>$%s on %s</td></tr><tr><td>Next Payment Due Date</td><td>:</td><td>%s</td></tr><tr><td>Final Payment Due Date</td><td>:</td><td>%s</td></tr></table><p>Thank you,<br/>Melbourne Ice Crew</p>');


define('MEMBERSHIP_MSG_RS_FULL', 'Hello %s,<br><br>Your booking request has been confirmed.<br><br><table><tr><td colspan="3"><b>Membership Details</b></td></tr><tr style="vertical-align: top"><td>Memberships</td><td>:</td><td>%s</td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3"><b>Payment Details</b></td></tr><tr><td>Total Amount</td><td>:</td><td>$%s</td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3"><b>Billing Details</b></td></tr><tr><td>Final Payment Date</td><td>:</td><td>%s</td></tr></table><p>Thank you,<br/>Melbourne Ice Crew</p>');

define('MEMBERSHIP_MSG_RS_FULL_OWNER', 'Hello %s,<br><br><b>%s</b>, booking request has been confirmed. Please find out your following details.<br><br><b>Booking Details</b><br><table><tr><td>Membership No</td><td>:</td><td>%s</td></tr><tr><td>Membership Type</td><td>:</td><td>%s</td></tr><tr><td>Bay Type</td><td>:</td><td>%s</td></tr><tr><td>Row No</td><td>:</td><td>%s</td></tr><tr><td>Seat No</td><td>:</td><td>%s</td></tr></table><br><b>Payment Details</b><br><table><tr><td>Total Amount</td><td>:</td><td>$%s</td></tr><tr><td>Payment Date</td><td>:</td><td>%s</td></tr></table><br><br/>Thank you,<br/>Melbourne Ice Crew');

/* RS Membership bokking email to owner */
define('MEMBERSHIP_SUBJECT_RS_OWNER', 'Membership Purchase Confirmation');
define('MEMBERSHIP_MSG_RS_OWNER', 'Hello %s,<br><br><b>%s</b>, booking request has been confirmed. Please find out following details.<br><br><b>Booking Details</b><br><table><tr><td>Membership No.</td><td>:</td><td>%s</td></tr><tr><td>Membership Type</td><td>:</td><td>%s</td></tr><tr><td>Bay Type</td><td>:</td><td>%s</td></tr><tr><td>Row No</td><td>:</td><td>%s</td></tr><tr><td>Seat No</td><td>:</td><td>%s</td></tr></table><br><b>Payment Details</b><br><table><tr><td>Amount Due</td><td>:</td><td>$%s</td></tr><tr><td>Total Instalments</td><td>:</td><td>%s</td></tr><tr><td>Instalments remaining</td><td>:</td><td>%s</td></tr><tr><td>Instalment Frequency</td><td>:</td><td>%s</td></tr></table><br><b>Billing Details</b><br><table><tr><td>Payment Deduction</td><td>:</td><td>$%s on %s</td></tr><tr><td>Next Payment Due Date</td><td>:</td><td>%s</td></tr><tr><td>Final Payment Due Date</td><td>:</td><td>%s</td></tr></table><br><br/>Thank you,<br/>Melbourne Ice Crew');

/* GA Membership bokking email */
define('MEMBERSHIP_SUBJECT_GA', 'Membership Purchase Confirmation');
define('MEMBERSHIP_MSG_GA', 'Hello %s,<br><br>Your booking request has been confirmed.<br><br><b>Membership Details</b><br><table><tr style="vertical-align: top;"><td>Memberships</td><td>:</td><td>%s</td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3"><b>Payment Details</b></td></tr><tr><td>Amount Due</td><td>:</td><td>$%s</td></tr><tr><td>Total Instalments</td><td>:</td><td>%s</td></tr><tr><td>Instalments remaining</td><td>:</td><td>%s</td></tr><tr><td>Instalment Frequency</td><td>:</td><td>%s</td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3"><b>Billing Details</b></td></tr><tr><td>Payment Deduction</td><td>:</td><td>$%s on %s</td></tr><tr><td>Next Payment Due Date</td><td>:</td><td>%s</td></tr><tr><td>Final Payment Due Date</td><td>:</td><td>%s</td></tr></table><p>Thank you,<br/>Melbourne Ice Crew</p>');

/* RS Membership bokking email to owner */
define('MEMBERSHIP_SUBJECT_GA_OWNER', 'Membership Purchase Confirmation');
define('MEMBERSHIP_MSG_GA_OWNER', 'Hello %s,<br><br><b>%s</b>, booking request has been confirmed. Please find out following details.<br><br><b>Booking Details</b><br><table><tr><td>Membership Type</td><td>:</td><td>%s</td></tr></table><br><b>Payment Details</b><br><table><tr><td>Amount Due</td><td>:</td><td>$%s</td></tr><tr><td>Total Instalments</td><td>:</td><td>%s</td></tr><tr><td>Instalments remaining</td><td>:</td><td>%s</td></tr><tr><td>Instalment Frequency</td><td>:</td><td>%s</td> </tr></table><br><b>Billing Details</b> <br><table><tr><td>Payment Deduction</td><td>:</td><td>$%s on %s</td></tr> <tr><td>Next Payment Due Date</td><td>:</td><td>%s</td></tr><tr><td>Final Payment Due Date</td><td>:</td><td>%s</td></tr></table><br><br/>Thank you,<br/>Melbourne Ice Crew');

/* final_mail for owner */
define('FINAL_OWNER_SUBJECT', 'Membership Purchase Confirmation');
define('FINAL_OWNER_MESSAGE', 'Hello %s,<br><br>Your booking request has been confirmed.<br><br><b>Membership Details</b><br><table><tr style="vertical-align: top;"><td>Memberships</td><td>:</td><td>%s</td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3"><b>Payment Details</b></td></tr><tr><td>Total Amount</td><td>:</td><td>$%s</td></tr><tr><td>Instalment Amount</td><td>:</td><td>$%s</td></tr><tr><td>Total Instalments</td><td>:</td><td>%s</td></tr><tr><td>Instalments remaining</td><td>:</td><td>%s</td></tr><tr><tr><td>Instalment Frequency</td><td>:</td><td>%s</td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3"><b>Billing Details</b></td></tr><tr><td>Payment Deduction</td><td>:</td><td>$%s on %s</td></tr><tr><td>Next Payment Due Date</td><td>:</td><td>%s</td></tr><tr><td>Final Payment Due Date</td><td>:</td><td>%s</td></tr></table><p>Thank you,<br/>Melbourne Ice Crew</p>');

define('FINAL_FULL_OWNER_MESSAGE', 'Hello %s,<br><br>Your booking request has been confirmed.<br><br><table><tr><td colspan="3"><b>Membership Details</b></td></tr><tr style="vertical-align: top"><td>Memberships</td><td>:</td><td>%s</td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3"><b>Payment Details</b></td></tr><tr><td>Total Amount</td><td>:</td><td>$%s</td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3"><b>Billing Details</b></td></tr><tr><td>Final Payment Date</td><td>:</td><td>%s</td></tr></table><p>Thank you,<br/>Melbourne Ice Crew</p>');

/* full payment template */
define('MEMBERSHIP_MSG_GA_FULL', 'Hello %s,<br><br>Your booking request has been confirmed.<br><br><table><tr><td colspan="3"><b>Membership Details</b></td></tr><tr style="vertical-align: top"><td>Memberships</td><td>:</td><td>%s</td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3"><b>Payment Details</b></td></tr><tr><td>Total Amount</td><td>:</td><td>$%s</td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3"><b>Billing Details</b></td></tr><tr><td>Final Payment Date</td><td>:</td><td>%s</td></tr></table><p>Thank you,<br/>Melbourne Ice Crew</p>');

define('MEMBERSHIP_MSG_GA_FULL_OWNER', 'Hello %s,<br><br><b>%s</b>, booking request has been confirmed. Please find out your following details.<br><br><b>Booking Details</b><br><table><tr><td>Membership Type</td><td>:</td><td>%s</td></tr></table><br><b>Payment Details</b><br><table><tr><td>Total Amount</td><td>:</td><td>$%s</td></tr><tr><td>Payment Date</td><td>:</td><td>%s</td></tr></table><br><br/>Thank you,<br/>Melbourne Ice Crew');

/* GA Membership bokking email */
define('MEMBERSHIP_SUBJECT_EXIST', 'Membership Purchase Confirmation');
define('MEMBERSHIP_MSG_EXIST', 'Hello %s,<br><br>Your booking request has been confirmed.<br><br><b>Membership Details</b><br><table><tr style="vertical-align: top;"><td>Memberships</td><td>:</td><td>%s</td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3"><b>Payment Details</b></td></tr><tr><td>Amount Due</td><td>:</td><td>$%s</td></tr><tr><td>Total Instalments</td><td>:</td><td>%s</td></tr><tr><td>Instalments remaining</td><td>:</td><td>%s</td></tr><tr><td>Instalment Frequency</td><td>:</td><td>%s</td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3"><b>Billing Details</b></td></tr><tr><td>Payment Deduction</td><td>:</td><td>$%s on %s</td></tr><tr><td>Next Payment Due Date</td><td>:</td><td>%s</td></tr><tr><td>Final Payment Due Date</td><td>:</td><td>%s</td></tr></table><p>Thank you,<br/>Melbourne Ice Crew</p>');

/* GA Membership bokking email */
define('MEMBERSHIP_SUBJECT_EXIST_OWNER', 'Membership Purchase Confirmation');
define('MEMBERSHIP_MSG_EXIST_OWNER', 'Hello %s,<br><br><b>%s</b>, booking request has been confirmed. Please find out following details.<br><br><b>Booking Details</b><br><table><tr><td>Membership Type</td><td>:</td><td>%s</td></tr><tr><td>Bay Type</td><td>:</td><td>%s</td></tr><tr><td>Row No</td><td>:</td><td>%s</td></tr><tr><td>Seat No</td><td>:</td><td>%s</td></tr></table><br><b>Payment Details</b><br><table><tr><td>Amount Due</td><td>:</td><td>$%s</td></tr><tr><td>Total Instalments</td><td>:</td><td>%s</td></tr><tr><td>Instalments remaining</td><td>:</td><td>%s</td></tr><tr><td>Instalment Frequency</td><td>:</td><td>%s</td></tr></table><br><b>Billing Details</b><br><table><tr><td>Payment Deduction</td><td>:</td><td>$%s on %s</td></tr><tr><td>Next Payment Due Date</td><td>:</td><td>%s</td></tr><tr><td>Final Payment Due Date</td><td>:</td><td>%s</td></tr></table><br><br/>Thank you,<br/>Melbourne Ice Crew');

define('MEMBERSHIP_MSG_EXIST_FULL', 'Hello %s,<br><br>Your booking request has been confirmed.<br><br><table><tr><td colspan="3"><b>Membership Details</b></td></tr><tr style="vertical-align: top"><td>Memberships</td><td>:</td><td>%s</td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3"><b>Payment Details</b></td></tr><tr><td>Total Amount</td><td>:</td><td>$%s</td></tr><tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3"><b>Billing Details</b></td></tr><tr><td>Final Payment Date</td><td>:</td><td>%s</td></tr></table><p>Thank you,<br/>Melbourne Ice Crew</p>');

define('MEMBERSHIP_MSG_EXIST_FULL_OWNER', 'Hello %s,<br><br><b>%s</b>, booking request has been confirmed. Please find out your following details.<br><br><b>Booking Details</b><br><table><tr><td>Membership Type</td><td>:</td><td>%s</td></tr><tr><td>Bay Type</td><td>:</td><td>%s</td></tr><tr><td>Row No</td><td>:</td><td>%s</td></tr><tr><td>Seat No</td><td>:</td><td>%s</td></tr></table><br><b>Payment Details</b><br><table><tr><td>Total Amount</td><td>:</td><td>$%s</td></tr><tr><td>Payment Date</td><td>:</td><td>%s</td></tr></table><br><br/>Thank you,<br/>Melbourne Ice Crew');

/* Social Site Mail varification */
define('SOCIAL_LOGIN_SUBJECT', 'Melbourne Ice account details');
define('SOCIAL_LOGIN_MESSAGE', 'Hi %s,<br/><br/>You are successfully linked with Melbourne Ice Community.<br/><br/>Thank you,<br/>Melbourne Ice Crew');

/*  Image Upload confirmation */
define('IMAGE_UPLOAD_SUBJECT', 'Image Upload Confirmation');
define('IMAGE_UPLOAD_MESSAGE', 'Hi Admin,<br/><br/>ID attachment has been successfully uploaded by the %s.<br/><br/>Thank you,<br/>Melbourne Ice Crew');

// Payment RS
define('TOTAL_PAYMENT', '6');
