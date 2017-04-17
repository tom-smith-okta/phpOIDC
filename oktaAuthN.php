<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include "config.php";

if (!(array_key_exists("checkedForOktaSession", $_SESSION))) {
	$_SESSION["checkedForOktaSession"] = 0;
}

function authenticate($state) {
	global $config;

	$url = getOauthURL($state);

	header("Location: " . $url);
}

function getOauthURL($state, $prompt = "prompt") {
	global $config;

	$nonce = "someRandomValue";

	$oauthURL = $config["oauthURL"] . "authorize?response_type=id_token&redirect_uri=" . $config["redirect_uri"];
	$oauthURL .= "&scope=openid%20profile&state=" . $state . "&response_mode=form_post&nonce=" . $nonce;
	$oauthURL .= "&client_id=" . $config["client_id"];

	if ($prompt === "noprompt") { $oauthURL .= "&prompt=none"; }

	return $oauthURL;
}

function isAuthenticated($thisPage = "index.php") {
			// echo "the checked for session val is: " . $_SESSION["checkedForOktaSession"];
	if (hasToken() && tokenIsValid()) {
		$_SESSION["checkedForOktaSession"] = 0;
		return TRUE;
	}
	else if ($_SESSION["checkedForOktaSession"] === 0) {
		// echo "the checked for session val is: " . $_SESSION["checkedForOktaSession"];
		$_SESSION["checkedForOktaSession"] = 1;

		// echo "the checked for session val is: " . $_SESSION["checkedForOktaSession"];


		$url = getOauthURL($thisPage, "noprompt");

		// echo "the url is: " . $url;

		// exit;

		header("Location: $url");
	}
}

function redirect($authenticated, $thisPage, $requireAuthN = TRUE) {
	if ($requireAuthN && !($authenticated)) {
		$url = getOauthURL($thisPage);
		header("Location: $url");
	}
}
// function isAuthenticated() {

// 	$output = "";

// 	if (hasToken() && tokenIsValid()) {
// 		$output = "the user has a valid token.";
// 		return $output;
// 		// return TRUE;
// 	}
// 	else {
// 		$output = "the user does not have a valid token.";
// 	}

// 	if (hasOktaSession()) {
// 		$output .= "<p>the user has an Okta session.";
// 	}
// 	else {
// 		$output .= "<p>the user does not have an Okta session.";
// 	}

// 	return $output;

	// return FALSE;
	// is there an id_token in the session?
	// is it valid?

	// $url = "https://tomco.okta.com/oauth2/v1/authorize?prompt=none&response_type=code&client_id=I2wYWlsAoRzcpOJq8ecD&redirect_uri=http://localhost:8888/oidcPHP/index.php&scope=openid%20profile&state=af0ifjsldkj&nonce=n-0S6_WzA2Mj";

	// if ($noPrompt) {
	// 	// $url = getOauthURL("index.php") . "&prompt=none";

	// 	$id_token = fopen($url);

	// 	echo "the url is: " . $url;

	// 	echo "the response is: " . json_encode($id_token);

	// 	exit;
	// }
// }

function hasOktaSession() { return FALSE; }

function hasToken() {
	if (!(array_key_exists("id_token", $_SESSION))) {
		$_SESSION["output"] .= "<p>There is no id_token in the local session.";
		return FALSE;
	}
	else {
		$_SESSION["output"] .= "<p>There is no id_token in the local session.";
		return TRUE;
	}
}

function tokenIsValid() {
	return isValid($_SESSION["id_token"]);
}

// function hasOktaSession() {
// 	$url = "https://tomco.okta.com/oauth2/v1/authorize?prompt=none&response_type=id_token&response_mode=form_post&client_id=I2wYWlsAoRzcpOJq8ecD&redirect_uri=http://localhost:8888/oidcPHP/redirect.php&scope=openid%20profile&state=index.php&nonce=n-0S6_WzA2Mj";
// 		header("Location: " . $url);
// }

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
	  	$_SESSION["output"] .= "<p>the id_token is valid.";
	  	$_SESSION["id_token"] = $token;
	  	$_SESSION["token_info"] = $response;

//	  	echo "the response is: " . $response;

//	  	exit;

	  	return TRUE;
	  }
	  else {
	  	// the id_token is not valid
	  	$_SESSION["output"] .= "<p>the id_token is not valid.";

	  	return FALSE;
	  }
	}
	return FALSE;
}