<?php

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

function getUserInfo() {
	$token_info = json_decode($_SESSION["token_info"]);
	$userName = $token_info->preferred_username;

	$output = "<p>the user is authenticated.</p>";
	$output .= "<p>Welcome, <b>" . $userName . "</b>!</p>";

	return $output;

}

function isSecure() {
	return
		(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		|| $_SERVER['SERVER_PORT'] == 443;
}

function showPage($output) {

	$page = file_get_contents("html/template.html");

	$page = str_replace("%--authnstatus--%", $output, $page);

	echo $page;

	exit;

}