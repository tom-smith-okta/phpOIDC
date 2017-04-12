<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include "oktaAuthN.php";
include "utils.php";

$thisPage = "page01.php";

if (isAuthenticated()) {
	showContent($thisPage);
}
else {
	authenticate($thisPage);
}

exit;