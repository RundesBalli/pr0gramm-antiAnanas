<?php
/**
 * includes/config.php
 * 
 * Configuration file.
 */

/**
 * pr0gramm-apiCall path
 * Download: https://github.com/RundesBalli/pr0gramm-apiCall
 * 
 * The absolute path where the apiCall is located at.
 * 
 * @var string
 */
$apiCall = '/path/to/apiCall.php';

/**
 * Time between inbox checks in seconds.
 * 
 * @var int
 */
$timeout = 10;

/**
 * Debug mode
 * 
 * Prints out verbose messages.
 * 
 * @var bool
 */
$debug = FALSE;

/**
 * Forbidden names
 * 
 * Array with usernames not to be highlighted.
 * 
 * @var array
 */
$forbiddenNames = [
  'Gamb',
];
?>
