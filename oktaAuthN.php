<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include "config.php";

function authenticate($state) {
	global $config;

	$url = getOauthURL($state);

	header("Location: " . $url);
}

function getOauthURL($state = "someState") {
	global $config;

	$nonce = "someRandomValue";

	$oauthURL = $config["oauthURL"] . "authorize?response_type=id_token&redirect_uri=" . $config["redirect_uri"];
	$oauthURL .= "&scope=openid%20profile&state=" . $state . "&response_mode=form_post&nonce=" . $nonce;
	$oauthURL .= "&client_id=" . $config["client_id"];

	return $oauthURL;
}

function isAuthenticated() {
	// is there an id_token in the session?
	// is it valid?

	if (!(array_key_exists("id_token", $_SESSION))) { return FALSE; }
	else { return isValid($_SESSION["id_token"]); }
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
	  echo "cURL Error #:" . $err;
	} else {
	  $arr = json_decode($response, TRUE);

	  if ($arr["active"] == "true") {
	  	// the id_token is valid
	  	$_SESSION["id_token"] = $token;
	  	$_SESSION["token_info"] = $response;

//	  	echo "the response is: " . $response;

//	  	exit;

	  	return TRUE;
	  }
	  else {
	  	// the id_token is not valid
	  	return FALSE;
	  }
	}
	return FALSE;
}