<?php

if (session_status() === PHP_SESSION_NONE) { session_start(); }

include "oktaAuthN.php";
include "utils.php";

if (empty($_SESSION["checkedForOktaSession"])) { $_SESSION["checkedForOktaSession"] = 0; }

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

set_client_id_and_secret();

$config["oauthURL"] = $config["oktaOrg"] . $config["oauthBasePath"];

$config["logoutURL"] = $config["oktaOrg"] . "login/signout?fromURI=" . $config["logout_page"];