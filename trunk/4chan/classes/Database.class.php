<?php
class Database extends mysqli 
{
   // Hold an instance of the class
   private static $instance;
   
   // A private constructor; prevents direct creation of object
   private function __construct()
   {
		parent::__construct(
					Config::get('db_server'), 
					Config::get('db_user'), 
					Config::get('db_password'), 
					Config::get('db_database')
					);
		parent::query("SET NAMES 'utf8'");
   }
   
   private function __destruct()
   {
   		parent::close();
   }

   // The singleton method
   public static function singleton() 
   {
       if (!isset(self::$instance)) {
           $c = __CLASS__;
           self::$instance = new $c;
       }

       return self::$instance;
   }
   
   // Prevent users to clone the instance
   public function __clone()
   {
       trigger_error('Clone is not allowed.', E_USER_ERROR);
   }

}
?>