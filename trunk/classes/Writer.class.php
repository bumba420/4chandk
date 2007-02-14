<?php
/*
This is slow! many many subquerys when only one is needed.
anyway - it's going to be cached so I'm really not 
going to spend my time on fixing it.
*/
class Writer
{
	static function thread($threadId)
	{
		$thread	=	new Thread($threadId);
		$output = '';
		
		foreach ($thread->posts() as $post)
		{
			$output .=	'<div id="'.$post->getId().'">';
			$output .=	'<span>'.$post->getName().'</span>';
			$output .= 	$post->getMessage();
			$output .=	'</div>';
		}
		
		return $output;
	}
	
	static function board($boardId)
	{
		$board	=	new Board($boardId);
		$output = '';
		
		$output .= 'topics:';
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
				$output .= '<li>';
				$output .=	$board->getName();
				$output .= '</li>';
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
			$output .= '<div><input type="text" name="name" size="28" maxlength="75" /></div>';
			$output .= '<div><input type="text" name="email" size="28" maxlength="75" /></div>';
		}
		
		$output .= '<div><input type="text" name="subject" size="35" maxlength="75" />';
		$output .= '<input type="submit" name="submit" value="Submit" /></div>';
		$output .= '<div><textarea name="message" cols="48" rows="4"></textarea></div>';
		$output .= '<div><input type="file" name="file" size="35" /></div>';
		$output .= '</div><input type="password" name="password" size="8" /></div>';
		$output .= '</form>';
		
		return $output;
	}
	/*
	<form id="postform" action="http://img.trevorchan.org/board.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="board" value="c" /><p>
                <table class="postform"><tbody><tr>
                    <td class="postblock">Name</td><td><input type="text" name="name" size="28" maxlength="75" /></td>
                    </tr><tr>
                <td class="postblock">E-mail</td><td><input type="text" name="email" size="28" maxlength="75" /></td>
                </tr>
                <tr>
                <td class="postblock">Subject</td><td><input type="text" name="subject" size="35" maxlength="75" />&nbsp;<input type="submit" value="Submit" /></td>
                </tr>
                <tr>
                <td class="postblock">Message</td><td><textarea name="message" cols="48" rows="4"></textarea></td>
                </tr><tr>
                        <td class="postblock">File<a href="#" onclick="togglePassword();" style="text-decoration: none;">&nbsp;</a></td><td><input type="file" name="imagefile" size="35" /></td>
                        </tr><tr>
                    <td class="postblock">Password</td><td><input type="password" name="postpassword" size="8" />&nbsp;(for post and file deletion)</td>
                    </tr>
                    <tr id="passwordbox"><td></td><td></td></tr><tr>
                    <td colspan="2"><div class="rules"><ul><li>Supported file types are: GIF, JPG, PNG</li><li>Maximum file size allowed is 1000 KB.</li><li>Images greater than 200x200 pixels will be thumbnailed.</li><li>Currently 167 unique user posts.</li></ul></div></td>
                    </tr></tbody></table></form>
	*/
}
?>