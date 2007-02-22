<?php
class Manage extends Writer 
{
	private function getDefaultValues()
	{
		$values		=	array();
		
		$values[0]	=	Config::get('max_filesize_in_bytes');
		$values[1]	=	Config::get('threads_pr_page');
		$values[2]	=	Config::get('threads_pr_board');
		$values[3]	=	Config::get('fored_anonymous');
		$values[4]	=	Config::get('comment_length');
		$values[5]	=	Config::get('thread_length');
		
		return $values;
	}
	
	private function getBoardValues(Board $board)
	{
		$values		=	array();
		
		$values[0]		=	$board->getFilesizeInKB()*1024;
		$values[1]		=	$board->getThreadsPrPage();
		$values[2]		=	$board->getThreadsPrBoard();
		$values[3]		=	$board->getForcedAnonymous();
		$values[4]		=	$board->getCommentLength();
		$values[5]		= 	$board->getThreadLength();
		
		$values[6]		=	$board->getName();
		$values[7]		=	$board->getDirectory();
		$values[8]		=	$board->getDescription();
		$values[9]		=	$board->getBanner();
		
		$values['id']	=	$board->getId();
		
		return $values;
	}
	
	static function BoardDefaultForm(Board $board = null)
	{
		if (is_null($board))
		{
			return self::BoardForm(self::getDefaultValues());
		}
		else 
		{
			return self::BoardForm(self::getBoardValues($board));
		}
	}
	
	static function BoardForm($values)
	{
		$destination = $_SERVER['PHP_SELF'];
		
		$output .= '<form action="'.URL::current().'" method="post" enctype="multipart/form-data" />';
		$output	.=	'<table><tbody><tr>';
		
		if (isset($values['id'])) 
		{
			$output	.=	'<input type="hidden" value="'.$values['id'].'" name="id" />';
		}
		
		$output	.=	'<td class="postblock">'.Language::get('manage:board_name').'</td>';
		$output	.=	'<td><input type="text" name="name" value="'.$values[6].'" size="35" /></td>';
		
		$output	.=	'</tr><tr>';
		$output	.=	'<td class="postblock">'.Language::get('manage:board_directory').'</td>';
		$output	.=	'<td><input type="text" name="dir" value="'.$values[7].'" size="35" /></td>';
		
		$output	.=	'</tr><tr>';
		$output	.=	'<td class="postblock">'.Language::get('manage:board_description').'</td>';
		$output	.=	'<td><input type="text" name="description" value="'.$values[8].'" size="35" /></td>';
		
		$output	.=	'</tr><tr>';
		$output	.=	'<td class="postblock">'.Language::get('manage:board_filesize').'</td>';
		$output	.=	'<td><input type="text" name="filesize" value="'.$values[0].'" size="35" /></td>';
		
		$output	.=	'</tr><tr>';
		$output	.=	'<td class="postblock">'.Language::get('manage:board_banner').'</td>';
		$output	.=	'<td><input type="text" name="banner" value="'.$values[9].'" size="35" /></td>';
		
		$output	.=	'</tr><tr>';
		$output	.=	'<td class="postblock">'.Language::get('manage:board_threads_page').'</td>';
		$output	.=	'<td><input type="text" name="threads_page" value="'.$values[1].'" size="35" /></td>';
		
		$output	.=	'</tr><tr>';
		$output	.=	'<td class="postblock">'.Language::get('manage:board_threads_board').'</td>';
		$output	.=	'<td><input type="text" name="threads_board" value="'.$values[2].'" size="35" /></td>';
		
		$output	.=	'</tr><tr>';
		$output	.=	'<td class="postblock">'.Language::get('manage:board_forced_anonymous').'</td>';
		$output	.=	'<td><input type="text" name="forced_anonymous" value="'.$values[3].'" size="35" /></td>';
		
		$output	.=	'</tr><tr>';
		$output	.=	'<td class="postblock">'.Language::get('manage:board_comment_length ').'</td>';
		$output	.=	'<td><input type="text" name="comment_length" value="'.$values[4].'" size="35" /></td>';
		
		$output	.=	'</tr><tr>';
		$output	.=	'<td class="postblock">'.Language::get('manage:board_thread_length').'</td>';
		$output	.=	'<td><input type="text" name="thread_length" value="'.$values[5].'" size="35" /></td>';
		
		$output	.=	'</tr><tr>';
		
		$output	.=	'<td class="postblock">'.Language::get('manage:submit').'</td>';
		$output	.=	'<td><input type="submit" name="submit" value="'.Language::get('manage:submit').'" /></td>';
		$output	.=	'</tr><tr>';
		
		$output	.=	'<td colspan="2"><div class="rules">';
		$output	.=	'</div>';
		
		$output	.=	'</td></tr></tbody></table>';
		$output	.=	'</form>';
		
		return $output;
	}
	
	static function listBoards()
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
		
		$output	.=	'<div style="margin: auto; width: 800px;">';
		
		$output	.=	'<div class="postblock"">';
		$output	.=	'<a href="?">';
		$output	.=	'New Board!';
		$output	.=	'</a>';
		$output	.=	'</div>';
		$output	.=	'<div>';
		$output	.=	'&nbsp;';
		$output	.=	'</div>';
		
		foreach ($sections as $section)
		{
			$output	.=	'<div class="postblock"">';
			$output	.=	$section->getName();
			$output	.=	'</div>';
			
			foreach ($section->boards() as $board)
			{
				$output	.=	'<div>';
				$output	.=	'<a href="?board='.$board->getId().'">';
				$output	.=	$board->getName();
				$output	.=	'</a>';
				$output	.=	'</div>';
			}
		}
		
		$output	.=	'</div>';
		
		return $output;
	}
}
?>