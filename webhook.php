<?php

/**
 * @file
 * GitHub Webhook Tracker
 */

// Set the content type.
header('Content-type: text/plain');

// Retrieve the JSON payload.
$json = file_get_contents('php://input');
if (empty($json)) {
    exit('Empty input.');
}

// Decode it from JSON into an array.
$data = json_decode($json, true);
if (!isset($data)) {
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            $message = 'No errors';
            break;
        case JSON_ERROR_DEPTH:
            $message = 'Maximum stack depth exceeded';
            break;
        case JSON_ERROR_STATE_MISMATCH:
            $message = 'Underflow or the modes mismatch';
            break;
        case JSON_ERROR_CTRL_CHAR:
            $message = 'Unexpected control character found';
            break;
        case JSON_ERROR_SYNTAX:
            $message = 'Syntax error, malformed JSON - Make sure to use +json';
            break;
        case JSON_ERROR_UTF8:
            $message = 'Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
        default:
            $message = 'Unknown error';
            break;
    }
    exit($message);
}

// Load the payload.
$payload = file_get_contents('payload.json');
if ($payload === FALSE) {
	$payload = '{}';
}
$payload = json_decode($payload, true);
if (json_last_error() != JSON_ERROR_NONE) {
	// Default to empty if it failed to load.
	$payload = array();
}

// Add the payload, sorted by UNIX time.
$payload[time()] = $data;

// Save the file.
$data = json_encode($payload, JSON_PRETTY_PRINT);
file_put_contents('payload.json', $data);
echo 'Saved the event correctly.';
