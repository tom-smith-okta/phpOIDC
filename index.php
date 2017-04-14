<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include "oktaAuthN.php";
include "utils.php";

$thisPage = "index.php";

if the user has an id_token && id_token is valid: show protected content
	end

if the user has a valid Okta session: show protected content
	end

else
	if redirect == true: redirect to oidc endpoint with prompt.
	else: show login link.





// hasOktaSession();

// exit;

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