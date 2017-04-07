<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Config
$config["oktaOrg"] = "https://tomco.okta.com/";
$config["client_id"] = "SV3N5CnEAMkOV4tUkIs6";
$config["client_secret"] = "Byw_25k3FHrtpbn21LlZgsk_GMnsXIABV0ed7Fju";
$config["redirect_uri"] = getRedirect_uri(); // not for prod. The redirect_uri values need to be added to the OIDC app in Okta
$config["oauthURL"] = $config["oktaOrg"] . "/oauth2/v1/";

$token = "";

// first, look for an id_token in the post
if (array_key_exists("id_token", $_POST)) {
	$token = $_POST["id_token"];

	echo "the token is: $token";
}
else {
	// Look for an id_token in the session
	// if no token, then get one.
	// if there is a token, then validate it.
	if (!(array_key_exists("id_token", $_SESSION))) { getToken(); }
	else { $token = $_SESSION["id_token"]; }
}

if (!(isValid($token))) {
	echo "<p>the token is empty, invalid, or expired.</p>";
	exit;
}
else {
	showPage();
}

function getToken() {
	global $config;

	$nonce = "someRandomValue";
	$state = "someState";

	$oauthURL = $config["oauthURL"] . "authorize?response_type=id_token&redirect_uri=" . $config["redirect_uri"];
	$oauthURL .= "&scope=openid%20profile&state=" . $state . "&response_mode=form_post&nonce=" . $nonce;
	$oauthURL .= "&client_id=" . $config["client_id"];

	header("Location: " . $oauthURL);

	// echo "the oauthURL is: " . $oauthURL;

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
	  echo "<p>" . $response;

	  $arr = json_decode($response, TRUE);

	  if ($arr["active"] == "true") {
	  	echo "<p>the token is valid.";
	  	$_SESSION["id_token"] = $token;
	  	return true;
	  }
	  else {
	  	echo "<p>response not parsed correctly.";
	  }
	}
	return false;
}

function showPage() {
	echo "<p>this is some protected content.</p>";
}

function isSecure() {
	return
		(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		|| $_SERVER['SERVER_PORT'] == 443;
}

function getRedirect_uri() {

	// http or https
	if (isSecure()) { $protocol = "https"; }
	else { $protocol = "http"; }

	$redirectURI = $protocol . "://" . $_SERVER["SERVER_NAME"];

	// add the port to the hostname if appropriate
	if (array_key_exists("SERVER_PORT", $_SERVER)) {
		if ($_SERVER["SERVER_PORT"] == "80") {}
		else { $redirectURI .= ":" . $_SERVER["SERVER_PORT"]; }
	}

	return $redirectURI . $_SERVER["PHP_SELF"];
}