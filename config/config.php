<?php

/*
-------- You Must Change These! -----------
*/

// Database information
Config::set('db_server', '127.0.0.1');
Config::set('db_user', '4chandk');
Config::set('db_password', '');
Config::set('db_database', 'bottiger_4chandk');

// Password salt, just something long and random.
// See http://bottiger.com/stuff/hashes if you want some random data
Config::set('salt', 'ZEEX8yhVYq80mylg8B5RLzDtnPa3tUclPf51TtsIfwDdaJd3');

/*
-------- You May Change These! -----------
*/

// Default board options
Config::set('post_pr_page', 20);
Config::set('threads_pr_board', 500);

// Script options
Config::set('alternative_url', '');

// Database settings
Config::set('database_prefix', '');
/*
-------- You Shouldn't Change These unless you REALLY KNOW what you're doing! -----------
*/

// Script constants
Config::set('document_root', $_SERVER['DOCUMENT_ROOT']);

// Name of database relations
Config::set('board_relation', Config::get('database_prefix').'boards');
Config::set('post_relation', Config::get('database_prefix').'posts');
Config::set('section_relation', Config::get('database_prefix').'sections');
?>