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

// Is the user authenticated?
$authenticated = isAuthenticated();

if ($authenticated != TRUE) {

	$_SESSION["log"][] = "user is not authenticated.";

	if ($config["checkForOktaSession"] === TRUE) {

		$_SESSION["log"][] = "config wants to check for an okta session...";
		$_SESSION["log"][] = "the state is: " . $state;

		checkForOktaSession($state);
	}
}
else { $_SESSION["log"][] = "the user is authenticated"; }

// if the page requires authentication and the user is not
// authenticated, bounce them to the authentication screen

if ($requireAuthN === 1 && $authenticated != TRUE) {

	$_SESSION["log"][] = "this page requires authentication, and the user is not authenticated.";

	$_SESSION["log"][] = "redirecting...";

	redirect($state);
}

/**********************************************/
/******** begin page-specific content *********/
/**********************************************/

if ($authenticated) {
	$output = getUserInfo();
	$output .= "<p>the page is: " . $state . "</p>";
}
else {
	$output = "<p>the user is <b>not</b> authenticated.</p>";
	$output .= "<p>click <a href = '" . getOauthURL($state) . "'>here</a> to authenticate.</p>";
}

showPage($output);

exit;