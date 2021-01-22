<?php

function zm_getcontactform($data) {

  $parameters = $data->get_params();

    // $firstname = sanitize_text_field($parameters['firstname']);
    // $subject = sanitize_text_field($parameters['Subject']);
    // $message = sanitize_textarea_field($parameters['Msgbody']);
    // $email = sanitize_email($parameters['Personemail']);


    // Build the message

    $message = "From: " . $parameters['data']['firstname'] . "\n";
    $message .= "CARP Membership Number: " . $parameters['data']['membershipid'] . "\n";
    $message .= "First Name: " . $parameters['data']['firstname'] . "\n";
    $message .= "Last Name: " . $parameters['data']['lastname'] . "\n";
    $message .= "Address: " . $parameters['data']['address'] . "\n";
    $message .= "City: " . $parameters['data']['city'] . "\n";
    $message .= "Province: " . $parameters['data']['province'] . "\n";
    $message .= "Postal Code: " . $parameters['data']['postalcode'] . "\n";
    $message .= "Email: " . $parameters['data']['Personemail'] . "\n";
    $message .= "Phone Number: " . $parameters['data']['phone'] . "\n";
    $message .= "Message Body: " . $parameters['data']['Msgbody'] . "\n" . "\n";
    $message .= "--" . "\n" . "This e-mail was sent from a contact form on EverythingZoomer (www.everythingzoomer.com)";


    $subject = "Zoomer Subscription Contact Form: " . "\n";
    // $recipient = get_option( 'admin_email', '' );

    $email = array(
		'to'      => 'r.saini@zoomermedia.ca',
		'subject' => $subject,
		'message' => $message,
	 );


  $success = wp_mail( $email['to'], $email['subject'], $email['message'] );
          if(isset($success)){
              return true;
              echo "successful";
          } else{
              return false;
            }
   }
