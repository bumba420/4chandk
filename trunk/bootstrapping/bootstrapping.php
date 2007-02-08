<?php

// Define everything in $config as constants
foreach ($config as $key => $value)
{
	define(strtoupper($key), $value);
}

// Destroy $config
unset($config);

// Connect to the database:
$mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

?>