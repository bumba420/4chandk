<?php
// The form
Language::set('form:name', 			'Name');
Language::set('form:email', 		'E-mail');
Language::set('form:subject',		'Subject');
Language::set('form:message',		'Comment');
Language::set('form:file',			'File');
Language::set('form:password',		'Password');
Language::set('form:submit',		'Submit');
Language::set('form:nofile',		'No File');
Language::set('form:delete',		'for post and file deletion');

// The post
Language::set('post:reply',			'Reply');
Language::set('post:file',			'File');
Language::set('post:thumbnail',		'Thumbnail displayed, click image for full size.');
Language::set('post:omitted',		'%d posts and %d image omitted. Click Reply to view.');

// This is a bit tricky. The following 3 lines are all part of 1 single sentence.
// "Comment too long. Click here to view the full text."
Language::set('post:too_long_1',		'Comment too long. Click');
Language::set('post:too_long_2',		'here');
Language::set('post:too_long_3',		'to view the full text.');

// The top
Language::set('top:home',			'Home');
Language::set('top:manage',			'Manage');
Language::set('top:return',			'Return');
Language::set('top:mode',			'Posting mode: Reply');

// The Bottom
Language::set('bottom:previous',	'Previous');
Language::set('bottom:next',		'Next');
Language::set('bottom:delete_post',	'Delete Post');
Language::set('bottom:file_only',	'File Only');
Language::set('bottom:password',	Language::get('form:password'));
Language::set('bottom:delete',		'Delete');
Language::set('bottom:report',		'Report');
?>