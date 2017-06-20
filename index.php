<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

/**********************************************/
/******** begin Okta auth logic ***************/
/**********************************************/
include "init.php";

// Does this page require authentication?
// If no value is provided here then pick up the default value
// from the config file
$requireAuthN = 0;

// assign the current URL to the state var
// I am using the built-in PHP constant for filename
// but this could be a full URL
$state = basename(__FILE__);

$authenticated = isAuthenticated($state);

// redirect the user to the Okta login page
// if they are not authenticated and this page
// requires authentication
bounceUser($state, $authenticated, $requireAuthN);

/**********************************************/
/******** end Okta auth logic *****************/
/**********************************************/

// Now you can use $authenticated in your app

/**********************************************/
/**** begin app/page-specific content *********/
/**********************************************/

$output = "";

if ($authenticated) {
	$output .= getUserInfo();
	$output .= "<p>the page is: " . $state . "</p>";
}
else {
	$output .= "<p>the user is <b>not</b> authenticated.</p>";
	$output .= "<p>click <a href = '" . getOauthURL($state) . "'>here</a> to authenticate.</p>";

	$output .= json_encode($_SESSION);
}

showPage($output);

exit;