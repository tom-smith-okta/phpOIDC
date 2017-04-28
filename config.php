<?php

if (!($json = file_get_contents("config.json"))) {
	echo "could not open the config.json file.";
	exit;
}
else {
	if (!($obj = json_decode($json))) {
		echo "could not json decode the config.json file.";
		exit;
	}
	else {
		echo "the okta org is: " . $obj->oktaOrg;
	}
// echo "the json is: " . $json;

}


exit;





// when true, this param will force the user to authenticate before loading the page
$requireAuthN = TRUE;

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