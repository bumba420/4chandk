<?php
class Writer
{
	static function write_thread($threadId)
	{
		return;
	}
	
	static function write_board($boardId)
	{
		return;
	}
	
	static function write_menu()
	{
		$query		=	"SELECT * FROM ".Config::get('section_relation');
		$sections	= array();
		$output = '';
		
		if ($result = Database::singleton()->query($query)) {
			
			while ($row = $result->fetch_assoc()) 
			{
				$section	=	array();
				foreach ($row as $key => $value)
				{
					$section[$key]	= $value;
				}
				$sections[]	=	new Section($row['id']);
				end($sections)->setData($section);
			}
		}
		
		foreach ($sections as $section)
		{
			$output .= '<span>';
			$output	.= $section->getName();
			$output .= '</span>';
			
			$output .= '<ul>';

			foreach ($section->boards() as $board)
			{
				$output .= '<li>';
				$output .=	$board->getName();
				$output .= '</li>';
			}
			
			$output .= '</ul>';
		}
		
		return $output;
	}
}
?>