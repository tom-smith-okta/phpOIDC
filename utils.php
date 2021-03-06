<?php

function getUserInfo() {
	$token_info = json_decode($_SESSION["token_info"]);
	$userName = $token_info->preferred_username;

	$output = "<p>the user is authenticated.</p>";
	$output .= "<p>Welcome, <b>" . $userName . "</b>!</p>";
	$output .= "<p>the token info is: " . json_encode($_SESSION["token_info"]);

	return $output;
}

function set_client_id_and_secret() {
	global $config;

	foreach ($config["envs"] as $env) {

		if (file_exists($env["client_secret_path"])) {
			if ($config["client_secret"] = trim(file_get_contents($env["client_secret_path"]))) {
				$config["client_id"] = $env["client_id"];
				$config["redirect_uri"] = $env["redirect_uri"];
				$config["logout_page"] = $env["logout_page"];
				return;
			}
			else {
				echo "cannot open the client secret file at " . $env["client_secret_path"];
				exit; 
			}
		}
	}

	echo "not able to find a valid client secret.";
	exit;
}

function showPage($output) {
	global $config;

	$page = file_get_contents("html/template.html");

	$page = str_replace("%--authnstatus--%", $output, $page);
	$page = str_replace("%--oktaTenant--%", $config["oktaOrg"], $page);

	echo $page;

	exit;
}