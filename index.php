<?php
function __autoload($class_name) 
{
   require_once 'classes/' . $class_name . '.class.php';
}

// Start the script
require_once 'config/config.php';
require_once 'bootstrapping/bootstrapping.php';
require_once Config::get('language_folder').'/'.Config::get('language').'.php';

//if new post
if (isset($_POST['submit'])) 
{
	$post = new Post(null, 
					null,
					$_GET['id'], 
					$_POST['subject'], 
					$_POST['name'],
					$_POST['email'],
					$_POST['message'],
					$_POST['password'],
					$_FILES['file']
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


if ($_GET['p'] == 'menu')
{
	echo Writer::headerStart();
	echo Writer::menuCSS();
	echo Writer::menuJavascript();
	echo Writer::headerEnd();
	echo Writer::menu();
	echo Writer::footer();
}
elseif ($_GET['p'] == 'board')
{
	if ($_GET['id'] != 0)
	{
		echo Writer::headerStart();
		echo Writer::boardCSS();
		echo Writer::headerEnd();
		echo '<hr />';
		echo '<center>';
		echo Writer::form($_GET['id']);
		echo '</center>';
		echo '<hr />';
		echo Writer::board($_GET['id']);
		echo Writer::footer();
	}
	else 
	{
		echo "Pick a board";
	}
}
else 
{
	echo Writer::frameset();
}

?>