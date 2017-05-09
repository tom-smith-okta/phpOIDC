<?php

// start a session, if one does not already exist
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include "oktaAuthN.php";
include "utils.php";
include "config.php";

// assign the current URL to the state var
// I am using the built-in PHP constant for filename
// but this could be a full URL
$state = basename(__FILE__);

// Does this page require authentication?
$requireAuthN = 1;

authenticate($state, $requireAuthN);

/**********************************************/
/******** begin page-specific content *********/
/**********************************************/

$output = getUserInfo();
$output .= "<p>the page is: " . $state . "</p>";

showPage($output);

exit;