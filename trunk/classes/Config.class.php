<?php
/*
THIS IS NOT THE CONFIGURATION FILE!
DO NOT EDIT THIS UNLESS YOU *KNOW* WHAT YOU ARE DOING!
EDIT /config/config.php INSTEAD!
*/
class Config
{
   // Hold an instance of the class
   private static $instance;
   private static $data = array();
   
   // A private constructor; prevents direct creation of object
   private function __construct() {}

   // The singleton method
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
       self::$data[$key] = $val;
   }
   
   // Prevent users to clone the instance
   public function __clone()
   {
       trigger_error('Clone is not allowed.', E_USER_ERROR);
   }

}
?>