<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include "oktaAuthN.php";

$thisPage = "index.php";

if (isAuthenticated()) {
	echo "<p>the user is authenticated.</p>";

	$token_info = json_decode($_SESSION["token_info"]);

	$userName = $token_info->preferred_username;

	// $userName = $_SESSION["token_info"]["preferred_username"];

	echo "<p>Welcome, " . $userName;

	showContent($thisPage);
}
else {
	authenticate($thisPage);
}

exit;

function showContent($thisPage) {
	echo "<p>this is the content of the " . $thisPage . " page.</p>";
	echo file_get_contents("nav.html");
}