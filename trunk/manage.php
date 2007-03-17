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

if (isset($_GET['p']) && $_GET['p'] == 'menu')
{
	echo Writer::headerStart();
	echo Writer::menuCSS();
	echo Writer::menuJavascript();
	echo Writer::headerEnd();
	echo Writer::menu(isset($_GET['showdirs']));
	echo Writer::footer();
}
elseif (isset($_GET['id']) && isset($_GET['p']) && $_GET['p'] == 'board')
{
	include_once('admin/'.$_GET['id'].'.admin.php');
}
else 
{
	echo Writer::frameset();
}

?>