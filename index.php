<?php
function __autoload($class_name) 
{
   require_once 'classes/' . $class_name . '.class.php';
}

// Start the script
require_once 'config/config.php';
require_once 'bootstrapping/bootstrapping.php';

//if new post
if (isset($_POST['submit'])) 
{
	$post = new Post(1, 
					1, 
					$_POST['subject'], 
					$_POST['name'],
					$_POST['email'],
					$_POST['message'],
					$_POST['password'],
					$_POST['file']
					);	
	if ($post->savePost())
	{
		echo "<h1>Post saved!</h1>";
	}
	else 
	{
		echo "<h1>Post failed!</h1>";
	}
}
else 
{
	var_dump($_POST);
}


echo Writer::menu();
echo '<hr />';
echo Writer::form();
echo '<hr />';
echo '<hr />';
echo Writer::board(1);


?>