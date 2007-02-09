<?php
function __autoload($class_name) {
   require_once 'classes/' . $class_name . '.class.php';
}

// Start the script
require_once 'config/config.php';
require_once 'bootstrapping/bootstrapping.php';

?>

Hello World!

<?php

$post = new post($mysqli, '11title', '22name', '33email', '44message', '55password', '66file');

echo '<hr />'.$post->getEmail();

if ($post->savePost())
{
	echo "wee";
}
else 
{	
	echo "not so weee";
}
?>