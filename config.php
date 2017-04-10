<?php

// Config
$config["oktaOrg"] = "https://tomco.okta.com/";
$config["client_id"] = "SV3N5CnEAMkOV4tUkIs6";
// $config["client_secret"] = "Byw_25k3FHrtpbn21LlZgsk_GMnsXIABV0ed7Fju";
$config["oauthURL"] = $config["oktaOrg"] . "/oauth2/v1/";
// $config["redirect_uri"] = "http://localhost:8888/oidcPHP/redirect.php";

$localKey = "/Users/tomsmith/keys/vishalSecret.txt";
$remoteKey = "/usr/local/keys/vishalSecret.txt";

if (file_exists($localKey)) {
	$config["client_secret"] = file_get_contents($localKey);
	$config["redirect_uri"] = "http://localhost:8888/oidcPHP/redirect.php";
}
else {
	$config["client_secret"] = file_get_contents($remoteKey);
	$config["redirect_uri"] = "http://vishal.atkdemo.com/redirect.php";
}

echo "<p>the client secret is: " . $config["client_secret"];
echo "<p>the redirect URL is: " . $config["redirect_uri"];

exit;

// $config["redirect_uri"] = getRedirect_uri();

// not for prod
// generates redirect_uri dynamically
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

function isSecure() {
	return
		(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		|| $_SERVER['SERVER_PORT'] == 443;
}
