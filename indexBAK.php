<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include "oktaAuthN.php";
include "utils.php";

$thisPage = "index.php";

if (array_key_exists("checked", $_SESSION) {
	if ($_SESSION["checked"]) {}
	else { } 
}

hasOktaSession();

exit;

if (isAuthenticated(TRUE)) {
	showContent($thisPage);
}
else {
	$output = "<p>the user is not authenticated.</p>";
	$output .= "<p><i>Note: SSO from the Okta dashboard to this page is disabled for the moment so I can show this UI without immediately redirecting for authN.</i></p>";
	$output .= "<p>click <a href = '" . getOauthURL("index.php") . "'>here</a> to authenticate.</p>";
	showPage($output);
}

exit;

possible states at home page

checked and authenticated
checked and not authenticated
not checked and authenticated
not checked and not authenticated

authenticated = has valid id_token