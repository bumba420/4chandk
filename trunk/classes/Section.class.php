<?php
class Section
{
	private  $id			=	'';
	private  $name			=	'';
	private  $order			=	0;
	
	private  $boards		=   array();
	
	private  $data			=	false;
	
	function __construct($id)
	{
		$this->id = $id;
	}
	
	public function boards()
	{
		
		/* works */
		$query = "SELECT id 
				  FROM ".Config::get('board_relation')."
				  WHERE section_id = ".$this->id;
	
		if ($stmt = Database::singleton()->prepare($query)) {

		   /* execute statement */
		   $stmt->execute();
	
		   /* bind result variables */
		   $stmt->bind_result($id);

		   $boards	=	array();
		   /* fetch values */
		   while ($stmt->fetch()) {
		       $boards[] = new Board($id);
		       //end($boards)->getName();
		   }

			//$boards[0]->getName();

			/* close statement */
		   $stmt->close();
		}
		$this->boards = $boards;
		//if ($this->id == 2)
		//	die("id is 2 ".var_dump($this->boards));
		return $this->boards;
	}
	
	public function setData($data)
	{
		if (!$this->data) 
		{
			foreach ($data as $key => $value)
			{
				$this->$key	= $value;
			}
			
			$this->data	=	true;
			return true;
		}
		
		return false;
	}
	
	
	private function getData()
	{
		/*
		16:09:28 < melange> jeg har følgende funktion: http://pastebin.ca/353697 . Men
                    jeg er ikke særlig glad for den. Det basale problem er at
                    den ikke er dynamisk nok, og kræver meget manuelt arbejde
                    hvis jeg nu bare tilføjer en ekstra parameter i min
                    database-relation. er der ikke en smartere måde bare at
                    mappe "name" i min relation til $this->name i mit objekt?
		16:20:06 < ffeezz> du ka' lade være med at bind'e og bare hente ud i et assoc
                   og iterere over array_keys og sætte $this->$$key = $value; ?
                   (jeg synes den der bind-syntax er dybt nasty i sin
                   forurening af scopet, men det vænner man sig nok til)
        */

		$query	=	"SELECT * 
					FROM ".Config::get('section_relation')."
					WHERE id = ".$this->id;
		
		if ($result = Database::singleton()->query($query)) 
		{
			
			while ($row = $result->fetch_assoc()) 
			{
				foreach ($row as $key => $value)
				{
					$this->$key	= $value;
				}
			}
							  
			$this->data		= true;
		}
	}
	
	function getId()
	{
		if (!$this->data) {	$this->getData(); }
		return $this->id;
	}
	
	function getName()
	{
		if (!$this->data) {	$this->getData(); }
		return $this->name;
	}
	
	function getOrder()
	{
		if (!$this->data) {	$this->getData(); }
		return $this->order;
	}
}
?>