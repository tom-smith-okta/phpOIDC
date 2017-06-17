<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// include "config.php";

// The 'checkedForOktaSession' is a boolean that tracks whether
// we've checked for an Okta session during this page load.
// Should happen only once per page load
if (!(array_key_exists("checkedForOktaSession", $_SESSION))) {
	$_SESSION["checkedForOktaSession"] = 0;
}

function authenticate($state, $requireAuthN = 1) {
	global $config;

	// Is the user authenticated?
	$authenticated = isAuthenticated();

	if ($authenticated != TRUE) {

		$_SESSION["log"][] = "user is not authenticated.";

		if ($config["checkForOktaSession"] === TRUE) {

			$_SESSION["log"][] = "config wants to check for an okta session...";
			$_SESSION["log"][] = "the state is: " . $state;

// echo "<p>user is not authenticated." ."\n";
// echo "<p>the value for session is: " . json_encode($_SESSION["checkedForOktaSession"]) . "\n";

// echo "<p>the value for session is: " . json_encode($_SESSION) . "\n";

			checkForOktaSession($state);
		}
	}
	else { 
		$_SESSION["log"][] = "the user is authenticated";
		$_SESSION["checkedForOktaSession"] = 0;
	}

	// if the page requires authentication and the user is not
	// authenticated, bounce them to the authentication screen

	if ($requireAuthN === 1 && $authenticated != TRUE) {

		$_SESSION["log"][] = "this page requires authentication, and the user is not authenticated.";

		$_SESSION["log"][] = "redirecting...";

		redirect($state);
	}
}

// builds the OAuth url
function getOauthURL($state, $prompt = "prompt") {
	global $config;

	$nonce = "someRandomValue";

	$oauthURL = $config["oauthURL"] . "authorize?response_type=id_token&redirect_uri=" . $config["redirect_uri"];
	$oauthURL .= "&scope=openid%20profile&state=" . $state . "&response_mode=form_post&nonce=" . $nonce;
	$oauthURL .= "&client_id=" . $config["client_id"];

	if ($prompt === "noprompt") { $oauthURL .= "&prompt=none"; }

	return $oauthURL;
}

function hasToken() {
	if (array_key_exists("id_token", $_SESSION)) {
		if (empty($_SESSION["id_token"])) {
			return FALSE;
		}
		else { return TRUE; }
	}
	else { return FALSE; }
}

// check to see if there is an id_token in the local session.
// if there is an id_token, check to see if it's valid.
function isAuthenticated() {
	return (hasToken() && isValid($_SESSION["id_token"]));
}

function checkForOktaSession($state) {

	$_SESSION["log"][] = "now in the checkForOktaSession function.";
	$_SESSION["log"][] = "the value of checkedForOktaSession is " . $_SESSION["checkedForOktaSession"];

	if ($_SESSION["checkedForOktaSession"] === 0) {

		// echo "<p>in the right clause";

		$_SESSION["checkedForOktaSession"] = 1;

		$_SESSION["log"][] = "we're about to redirect the user to Okta to check for a session, so we're going to set the value of checkedForOktaSession to 1.";

		$url = getOauthURL($state, "noprompt");

		// echo "<p>the url is: " . $url;

		$_SESSION["log"][] = "the OAuth url is: " . $url;

		$headerString = "Location: " . $url;

		// echo "<p>the header string is: " . $headerString;

		// exit;

		$_SESSION["log"][] = "sending the user to the Okta OAuth URL...";

		header($headerString);
	}
	else {
		$_SESSION["log"][] = "we have already checked for an Okta session.";

		// echo "we have already checked for an okta session.";
	}
}

function isValid($token) {

	global $config;

	$curl = curl_init();

	// can also make this call with header-based auth
	$url = $config["oauthURL"] . "introspect?token=" . $token . "&client_id=" . $config["client_id"] . "&client_secret=" . $config["client_secret"];

	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_HTTPHEADER => array(
	    "accept: application/json",
	    "cache-control: no-cache",
	    "content-type: application/x-www-form-urlencoded",
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		$_SESSION["output"] .= "<p>cURL Error #:" . $err;
	} else {
	  $arr = json_decode($response, TRUE);

	  if ($arr["active"] == "true") {
	  	// the id_token is valid
	  	$_SESSION["id_token"] = $token;
	  	$_SESSION["token_info"] = $response;

	  	return TRUE;
	  }
	  else {
	  	// the id_token is not valid
	  	return FALSE;
	  }
	}
	return FALSE;
}

// redirect the user to an Okta OIDC url with appropriate params
function redirect($state) {

	$url = getOauthURL($state);

	$_SESSION["log"][] = "the user is being redirected to " . $url;

	header("Location: $url");
}
