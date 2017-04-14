<?php

// Config
$config["oktaOrg"] = "https://tomco.okta.com/";
$config["oauthURL"] = $config["oktaOrg"] . "/oauth2/v1/";

$localKey = "/Users/tomsmith/keys/oidcSecret.txt";
$remoteKey = "/usr/local/keys/vishalSecret.txt";

if (file_exists($localKey)) {
	$config["client_id"] = "I2wYWlsAoRzcpOJq8ecD";
	$config["client_secret"] = trim(file_get_contents($localKey));
	$config["redirect_uri"] = "http://localhost:8888/oidcPHP/redirect.php";
}
else {
	$config["client_id"] = "SV3N5CnEAMkOV4tUkIs6";
	$config["client_secret"] = trim(file_get_contents($remoteKey));
	$config["redirect_uri"] = "http://oidc.atkodemo.com/redirect.php";
}

// $config["redirect_uri"] = getRedirect_uri();