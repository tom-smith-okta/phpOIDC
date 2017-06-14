<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include "oktaAuthN.php";
include "utils.php";
include "config.php";

// assign the current URL to the state var
// I am using the built-in PHP constant for filename
// but this could be a full URL
$state = basename(__FILE__);

// Does this page require authentication?
$requireAuthN = 0;


authenticate($state, $requireAuthN);

$authenticated = isAuthenticated();

// echo json_encode($_SESSION);

	// $output = getUserInfo();
	// $output .= "<p>the page is: " . $state . "</p>";


/**********************************************/
/******** begin page-specific content *********/
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