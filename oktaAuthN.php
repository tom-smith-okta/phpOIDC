<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function bounceUser($state, $authenticated, $requireAuthN) {
	global $config;

	if (!(isset($requireAuthN))) { $requireAuthN = $config["requireAuthN"]; }

	if ($requireAuthN == 1 && $authenticated == FALSE) {
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
function isAuthenticated($state) {
	global $config;

	if (hasToken() && isValid($_SESSION["id_token"])) { return TRUE; }

	else {

		if ($config["allowIDPinit"] === TRUE) {

			$now = time();

			$elapsedSecs = $now - $_SESSION["checkedForOktaSession"];

			// Have we already done a behind-scenes redirect to Okta?
			if ($elapsedSecs > $config["pause"]) {
				$_SESSION["checkedForOktaSession"] = time();

				$url = getOauthURL($state, "noprompt");

				$_SESSION["log"][] = "the OAuth url is: " . $url;

				$headerString = "Location: " . $url;

				$_SESSION["log"][] = "sending the user to the Okta OAuth URL...";

				header($headerString);
			}
		}
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
