<?php
class database
{
   // Hold an instance of the class
   private static $instance;
   
   // A private constructor; prevents direct creation of object
   private function __construct() 
   {
       echo 'I am constructed';
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