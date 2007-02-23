<?php
function __autoload($class_name) 
{
   require_once 'classes/' . $class_name . '.class.php';
}

// Start the script
require_once 'config/config.php';
require_once Config::get('language_folder').'/'.Config::get('language').'.php';

Config::initialize();
session_start();

if (isset($_POST['password']))
{
	$_SESSION['su_password'] = $_POST['password'];
}

if (Config::get('su_password') != $_SESSION['su_password']) 
{
	echo "Please log in:";
	echo '<form method="post">';
	echo '<input type="password" name="password" />';
	echo ' ';
	echo '<input type="submit" value="login" />';
	echo '</form>';
	die();
}

if (isset($_GET['delete_section']))
{
	$query	=	"DELETE FROM ".Config::get('section_relation')." WHERE id = ".$_GET['delete_section'];
	
	if ($result = Database::singleton()->query($query)) 
	{
		;
	}
	else 
	{
		die("Error:".Database::singleton()->error.'<hr />'.$query);
	}
}

if (isset($_GET['delete_board']))
{
	$query	=	"DELETE FROM ".Config::get('section_relation')." WHERE id = ".$_GET['delete_board'];
	
	if ($result = Database::singleton()->query($query)) 
	{
		;
	}
	else 
	{
		die("Error:".Database::singleton()->error.'<hr />'.$query);
	}
}

if (isset($_POST['section_submit']))
{
	$query	=	"INSERT INTO ".Config::get('section_relation')."
				(
				name 
				)
				VALUES
				(
				'".$_POST['section_id']."'
				)";
	
	if ($result = Database::singleton()->query($query)) 
	{
		;
	}
	else 
	{
		die("Error:".Database::singleton()->error.'<hr />'.$query);
	}
}

if (isset($_POST['submit']))
{
	// update
	if (isset($_POST['id']))
	{
		$query = "UPDATE ".Config::get('board_relation')."
					SET section_id = ".$_POST['section_id'].",
						name = '".$_POST['name']."',
						dir = '".$_POST['dir']."',
						description  = '".$_POST['description']."',
						filesize = ".$_POST['filesize'].",
						banner = '".$_POST['banner']."',
						threads_page = ".$_POST['threads_page'].",
						threads_board = ".$_POST['threads_board'].",
						forced_anonymous = ".$_POST['forced_anonymous'].",
						comment_length = ".$_POST['comment_length'].",
						thread_length = ".$_POST['thread_length']."
					WHERE id = ".$_POST['id'];
	}
	// insert
	else 
	{
		$query	=	"INSERT INTO ".Config::get('board_relation')."
					(
					section_id, 
					name, 
					dir, 
					description, 
					filesize, 
					banner, 
					threads_page, 
					threads_board, 
					forced_anonymous,
					comment_length,
					thread_length
					)
					VALUES
					(
					".$_POST['section_id'].",
					'".$_POST['name']."',
					'".$_POST['dir']."',
					'".$_POST['description']."',
					".$_POST['filesize'].",
					'".$_POST['banner']."',
					".$_POST['threads_page'].",
					".$_POST['threads_board'].",
					".$_POST['forced_anonymous'].",
					".$_POST['comment_length'].",
					".$_POST['thread_length']."
					)";
	}
	if ($result = Database::singleton()->query($query)) 
	{
		;
	}
	else 
	{
		die("Error:".Database::singleton()->error.'<hr />'.$query);
	}
}

$board	=	null;
if (intval($_GET['board']))
{
	$board = new Board(intval($_GET['board']));
}

echo Writer::headerStart();
echo Writer::boardCSS();
echo Writer::boardJavascript();
echo Writer::headerEnd();

echo '<center>';
echo Manage::SectionForm();
echo '</center>';
echo '<hr />';

echo '<center>';
echo Manage::BoardDefaultForm($board);
echo '</center>';
echo '<hr />';
echo Manage::listBoards();

echo Writer::footer();



?>