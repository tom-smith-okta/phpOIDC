<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include "utils.php";
include "config.php";

// revoke the Okta id_token
if (array_key_exists("id_token", $_SESSION)) {
	revokeToken($_SESSION["id_token"]);
}

// kill the local session
session_unset();

header("Location: https://tomco.okta.com/login/signout?fromURI=http://localhost:8888/oidcPHP/");

// header("Location: https://tomco.okta.com/login/signout?fromURI=https://tomco.okta.com/home/bookmark/0oa27zqgesIYQxsSb1t7/2557");


// https://tomco.okta.com/home/bookmark/0oa27zqgesIYQxsSb1t7/2557


exit;

function revokeToken($token) {

	global $config;

	$curl = curl_init();

	// can also make this call with header-based auth
	$url = $config["oauthURL"] . "revoke?token=" . $token . "&client_id=" . $config["client_id"] . "&client_secret=" . $config["client_secret"];

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
		// echo "<p>the response is: " . $response;
	}
}