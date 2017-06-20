<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Look for an id_token in the POST from Okta
// save it to the local session and redirect the user to the
// url indicated in the "state" param
if (array_key_exists("id_token", $_POST)) {
	$_SESSION["id_token"] = $_POST["id_token"];
}

if (array_key_exists("state", $_POST)) {
	header("Location: " . $_POST["state"]);
}
else {
	header("Location: index.php");
}