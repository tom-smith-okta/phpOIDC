<?php

if (!($json = file_get_contents("config.json"))) {
	echo "could not open the config.json file.";
	exit;
}
else {
	if (!($config = json_decode($json, TRUE))) {
		echo "could not json decode the config.json file.";
		exit;
	}
}

$config["oauthURL"] = $config["oktaOrg"] . $config["oauthBasePath"];

set_client_id_and_secret();