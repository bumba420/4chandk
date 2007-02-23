<?php
/*
THIS FILE IS A MESS.
I STILL HAVN'T DESIDED HOW IT SHOULD *LOOK*
*/

function __autoload($class_name) 
{
   require_once 'classes/' . $class_name . '.class.php';
}

// Start the script
require_once 'config/config.php';
require_once Config::get('language_folder').'/'.Config::get('language').'.php';

Config::initialize();
Authorization::isBanned();

//if new post
if (isset($_POST['submit'])) 
{
	if (isset($_GET['thread_id']))
	{
		$post = new Post(null, 
					$_GET['thread_id'],
					$_GET['id'], 
					$_POST['subject'], 
					$_POST['name'],
					User::calcTripecode($_POST['name']),
					$_POST['email'],
					$_POST['message'],
					$_POST['password'],
					time(),
					$_FILES['file']
					);
	}
	else 
	{
		$post = new Post(null, 
					null,
					$_GET['id'], 
					$_POST['subject'], 
					$_POST['name'],
					User::calcTripecode($_POST['name']),
					$_POST['email'],
					$_POST['message'],
					$_POST['password'],
					time(),
					$_FILES['file']
					);
	}	
	if ($post->savePost())
	{
		header("Location: ".URL::current());
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
	echo Writer::menu(isset($_GET['showdirs']));
	echo Writer::footer();
}
elseif ($_GET['p'] == 'board' && is_null($_GET['thread_id']))
{
	if ($_GET['id'] != 0)
	{
		$board = new Board($_GET['id']);
		
		echo Writer::headerStart();
		echo Writer::boardCSS();
		echo Writer::boardJavascript();
		echo Writer::headerEnd();
		echo Writer::boardTop($board);
		echo '<center>';
		echo Writer::form($board);
		echo '</center>';
		echo '<hr />';
		echo Writer::board($board, intval($_GET['page']));
		echo Writer::boardBottom($board);
		echo Writer::footer();
	}
	else 
	{
		echo "Pick a board";
	}
}
elseif (isset($_GET['thread_id']))
{
	$board = new Board($_GET['id']);
	
	echo Writer::headerStart();
	echo Writer::boardCSS();
	echo Writer::headerEnd();
	echo Writer::boardTop($board);
	echo '<center>';
	echo Writer::form($board);
	echo '</center>';
	echo '<hr />';
	echo Writer::thread($board, $_GET['thread_id']);
	echo Writer::footer();
}
else 
{
	echo Writer::frameset();
}

?>