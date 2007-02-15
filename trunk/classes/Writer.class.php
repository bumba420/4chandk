<?php
/*
This is slow! many many subquerys when only one is needed.
anyway - it's going to be cached so I'm really not 
going to spend my time on fixing it - right now.
*/
class Writer
{
	static function thread($threadId)
	{
		$thread	=	new Thread($threadId);
		$output = '';
		$i		= 1;
		
		foreach ($thread->posts() as $post)
		{
			if ($i == 1)
			{
				$output .=	'<div class="firstpost">';
				++$i;
			}
			elseif ($i == 2)
			{
				$output .=	'<div class="secondpost">';
				++$i;
			}
			else 
			{
				$output .=	'<div class="post">';
			}
			
			$output .=	'<div id="'.$post->getId().'">';
			$output .=	'<span>'.$post->getName().'</span>';
			$output .= 	$post->getMessage();
			$output .=	'</div>';
			$output .=	'</div>';
		}
		
		return $output;
	}
	
	static function board($boardId)
	{
		$board	=	new Board($boardId);
		$output = '';
		
		foreach ($board->threads() as $thread)
		{
			$output .=	self::thread($thread->getId());
			$output .= '<hr />';
		}
		
		return $output;
	}
	
	static function menu()
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
				$output .=	'<li>';
				$output	.=	'<a href="?p=board&id='.$board->getId().'" target="main">';
				$output .=	$board->getName();
				$output	.=	'</a>';
				$output .= 	'</li>';
			}
			
			$output .= '</ul>';
		}
		
		return $output;
	}
	
	static function form($destination = null)
	{
		$destination = is_null($destination) ? $_SERVER['PHP_SELF'] : $destination;
		
		$output .= '<form action="'.$destination.'" method="post" enctype="multipart/form-data" />';
		
		if (!Config::get('fored_anonymous'))
		{
			$output .= '<div><span>'.Language::get('form:name').'</span><input type="text" name="name" size="28" maxlength="75" /></div>';
			$output .= '<div><span>'.Language::get('form:email').'</span><input type="text" name="email" size="28" maxlength="75" /></div>';
		}
		
		$output .= '<div><span>'.Language::get('form:subject').'</span><input type="text" name="subject" size="35" maxlength="75" />';
		$output .= '<input type="submit" name="submit" value="'.Language::get('form:submit').'" /></div>';
		$output .= '<div><span>'.Language::get('form:message').'</span><textarea name="message" cols="48" rows="4"></textarea></div>';
		$output .= '<div><span>'.Language::get('form:file').'</span><input type="file" name="file" size="35" /></div>';
		$output .= '</div><span>'.Language::get('form:password').'</span><input type="password" name="password" size="8" /></div>';
		$output .= '</form>';
		
		return $output;
	}

	static function header()
	{
		$output		=	'<html>';
		$output		.=	'<head>';
		$output		.= 	'<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		$output		.=	'<link rel="Start" title="Home" href="/" />';
		$output		.=	'</head>';
		$output		.=	'<body>';
		
		return $output;
	}
	
	static function footer()
	{
		$output		=	'</body>';
		$output		.=	'</html>';
		
		return $output;
	}
	
	static function frameset()
	{
		$output		=	'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
		$output		.=	'<html>';
		$output		.=	'<head><title>'.Config::get('page_title').'</title>';
		$output		.=	'</head>';
		$output		.=	'<frameset cols="18%,*" frameborder="0" border="0">';
		$output		.=	'<frame src="?p=menu">';
		$output		.=	'<frame src="?p=board" name="main">';
		$output		.=	'<noframes>';
		$output		.=	'</noframes>';
		$output		.=	'</frameset>';
		$output		.=	'</html>';
		
		return $output;
	}
}
?>