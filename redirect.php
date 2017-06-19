<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$_SESSION["log"][] = "in the redirect script.";

$_SESSION["log"][] = "the post from Okta is: " . json_encode($_POST);

// echo "in the redirect script. ";

// exit;

// Look for an id_token in the POST from Okta
// save it to the local session and redirect the user to the
// url indicated in the "state" param
if (array_key_exists("id_token", $_POST)) {

	$_SESSION["log"][] = "found an id_token in the POST.";

	$_SESSION["id_token"] = $_POST["id_token"];

}

if (array_key_exists("state", $_POST)) {
	header("Location: " . $_POST["state"]);
}

else {
	header("Location: index.php");
}