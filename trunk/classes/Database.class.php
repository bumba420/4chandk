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
		//parent::query("SET NAMES 'utf8'");
		
		if (mysqli_connect_errno())
		{
			die(sprintf("Can't connect to database. Error: %s", mysqli_connect_error()));
		}
   }
   
   public function standartQuery($sql)
   {
   		
   		if (!($result = parent::query($sql)))
   		{
   			die(printf("Can't perform query on database. Error: %s", $this->error));
   		}

   		return $result;
   }
   
   public function getObjectData()
   {
   		return null;
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