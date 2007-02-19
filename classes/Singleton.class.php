<?php
abstract class Singleton
{
   private static $instance;
   private static $data = array();
   
   private function __construct() {}

   public static function singleton() 
   {
       if (!isset(self::$instance)) {
           $c = __CLASS__;
           self::$instance = new $c;
       }

       return self::$instance;
   }
   
   public static function get($key)
   {
   	   return self::$data[$key];
   }
   
   public static function set($key, $val)
   {
    	if (empty(self::$data[$key]))
    	{
    		self::$data[$key] = $val;
    	}
   }
   
   public function __clone()
   {
       trigger_error('Clone is not allowed.', E_USER_ERROR);
   }

}
?>