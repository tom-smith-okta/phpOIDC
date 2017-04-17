<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include "oktaAuthN.php";
include "utils.php";

$thisPage = "page02.php";

$authenticated = isAuthenticated($thisPage);

redirect($authenticated, $thisPage, $requireAuthN);

/**********************************************/
/******** begin page-specific content *********/
/**********************************************/

if ($authenticated) {
	$output = getUserInfo();
	$output .= "<p>the page is: " . $thisPage . "</p>";
}
else {
	$output = "<p>the user is not authenticated.</p>";
	$output .= "<p>click <a href = '" . getOauthURL($thisPage) . "'>here</a> to authenticate.</p>";
}

showPage($output);

exit;