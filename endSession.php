<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include "init.php";

// revoke the Okta id_token
if (array_key_exists("id_token", $_SESSION)) {
	revokeToken($_SESSION["id_token"]);
}

echo "<p>the id_token in the session is: " . $_SESSION["id_token"];


// kill the local session
session_unset();

echo "<p>the id_token in the session is: " . $_SESSION["id_token"];

$redirectString = "Location: " . $config["logout_page"];

header($redirectString);

exit;

function revokeToken($token) {
	global $config;

	$curl = curl_init();

	// can also make this call with header-based auth
	$url = $config["oauthURL"] . "revoke?token=" . $token . "&client_id=" . $config["client_id"] . "&client_secret=" . $config["client_secret"];

	echo "<p>the url is: " . $url;

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

	echo "<p>the resonse is: " . $response;

	curl_close($curl);

	if ($err) {
		echo "cURL Error #:" . $err;
		echo "<p>the response is: " . json_encode($response);
	} else {
		// echo "<p>the response is: " . json_encode($response);
	}
}