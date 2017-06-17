<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include "oktaAuthN.php";
include "utils.php";
include "config.php";

// Is the user currently authenticated?
	// Does the user have a valid local session?
		// If yes:
			// we are done.
		// If no:
			// Does the app allow IDP-init flow?
				// If yes:
					// Have we already redirected the user to Okta once?
						// If yes:
							// we are done.
						// If no:
							// redirect the user to Okta with noprompt=true

// Does this page *require* authentication?
	// If yes:
		// Is the user authenticated?
			// If yes: we are done
			// If no:
				// redirect the user to Okta (with noprompt=false)

// assign the current URL to the state var
// I am using the built-in PHP constant for filename
// but this could be a full URL
$state = basename(__FILE__);

$authenticated = isAuthenticated();

if ($authenticated) {
	echo "the user is authenticated.";
}
else {
//	echo "<p>the user is not authenticated.";

	if ($config["allowIDPinit"] === TRUE) {
		//echo "<p>we are allowing IDP init flow.";

		if ($_SESSION["checkedForOktaSession"] > 0) {
			//echo "<p>we have already checked for an Okta session.";
		}
		else {
			//echo "<p>we have *not* already checked for an Okta session.";

			$_SESSION["checkedForOktaSession"]++;

			$url = getOauthURL($state, "noprompt");

			//echo "<p>the url is: " . $url;

			$_SESSION["log"][] = "the OAuth url is: " . $url;

			$headerString = "Location: " . $url;

			//echo "<p>the header string is: " . $headerString;

			// exit;

			$_SESSION["log"][] = "sending the user to the Okta OAuth URL...";

			header($headerString);
		}
	}
}




exit;


if ($config["allowIDPinit"] === TRUE) {
	echo "we are going to check with Okta for a session first.";
	checkForOktaSession($state);
}

$authenticated = isAuthenticated();

// echo "the value of authenticated is: " . $authenticated;

// exit;

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