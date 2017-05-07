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

$authenticated = isAuthenticated($state);

redirect($authenticated, $state);

/**********************************************/
/******** begin page-specific content *********/
/**********************************************/

if ($authenticated) {
	$output = getUserInfo();
	$output .= "<p>the page is: " . $state . "</p>";
}
else {
	$output = "<p>the user is not authenticated.</p>";
	$output .= "<p>click <a href = '" . getOauthURL($state) . "'>here</a> to authenticate.</p>";
}

showPage($output);

exit;