<?php
class Parser
{
	static function boardMessage($text)
	{
		return nl2br(htmlentities($text));
	}
}
?>