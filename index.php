<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include "oktaAuthN.php";
include "utils.php";

$thisPage = "index.php";

if (isAuthenticated()) {
	showContent($thisPage);
}
else {
	$url = getOauthURL("index.php");
	$output = "<p>the user is not authenticated.</p>";
	$output .= "<p>click <a href = '$url'>here</a> to authenticate.</p>";
	showPage($output);
}

exit;