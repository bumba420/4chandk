<?php

/*
-------- You Must Change These! -----------
*/

// Database information
Config::set('db_server', '127.0.0.1');
Config::set('db_user', 'bottiger_4chandk');
Config::set('db_password', '');
Config::set('db_database', 'bottiger_4chandk');

// Password salt, just something long and random.
// See http://bottiger.com/stuff/hashes if you want some random data
Config::set('salt', 'ZEEX8yhVYq80mylg8B5RLzDtnPa3tUclPf51TtsIfwDdaJd3');

/*
-------- You May Change These! -----------
*/

// Page title
Config::set('page_title', '4chandk');
Config::set('page_url',	'');

// Default board options
Config::set('post_pr_page', 20);
Config::set('threads_pr_board', 500);
Config::set('fored_anonymous', false);

Config::set('image_max_width', 200);
Config::set('image_max_height', 200);

// language options
Config::set('language', 'english');

// Tripcode options, just write the hash you would like to use.
// i.e. crypt, md5, sha1, sha512, whatever.
// Just make sure your version of PHP supports it
Config::set('tripcode_hash', 'md5');

// Database settings
Config::set('database_prefix', '');
/*
-------- You Shouldn't Change These unless you REALLY KNOW what you're doing! -----------
*/

// Script constants
Config::set('root_folder', $_SERVER['DOCUMENT_ROOT'].'/4chan');
Config::set('tmp_folder', Config::get('root_folder').'/tmp');
Config::set('cache_folder', Config::get('tmp_folder').'/cache');
Config::set('image_folder', Config::get('tmp_folder').'/img');
Config::set('thumbnail_folder', Config::get('tmp_folder').'/thumbnails');
Config::set('language_folder', Config::get('root_folder').'/languages');

Config::set('thumbnail_url', 'tmp/thumbnails');
Config::set('image_url', 'tmp/img');

// Name of database relations
Config::set('board_relation', Config::get('database_prefix').'boards');
Config::set('post_relation', Config::get('database_prefix').'posts');
Config::set('section_relation', Config::get('database_prefix').'sections');
?>