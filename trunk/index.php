<?php
function __autoload($class_name) {
   require_once 'classes/' . $class_name . 'class.php';
}

// Start the script
require_once 'config/config.php';
require_once 'bootstrapping/bootstrapping.php';
?>

Hello World!