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

$_SESSION["log"][] = "In the idpInit script...";


if (isAuthenticated()) {
	echo "the user is authenticated";
	echo "<p>the session is: " . json_encode($_SESSION);
}
else {

	$_SESSION["log"][] = "The user is not authenticated...";
	$_SESSION["log"][] = "So we are going to bounce them to Okta to check for a session...";

	//echo "the user is NOT authenticated";

	$url = getOauthURL($state, "noprompt");

	$_SESSION["log"][] = "the OAuth URL is: " . $url;

	//echo "<p>the url is: " . $url;

	$headerString = "Location: " . $url;

	//echo "<p>the header string is: " . $headerString;

	header($headerString);

}

exit;