#release notes for 4chandk-24022007

# Release notes for _4chandk-24022007_. Also known as "BigBang" #

## Before you read ##
**The script may contain errors, these notes may contain errors, everything else may contain errors. However, I release this due to the fact that it works (for me), I won't have time to work on it the next week, and would like the opportunity to get some feedback.**
**Please, if you have _anything_ to say, don't hesitate to write me an e-mail (bottiger @ gmail.com), or submit a bug: http://code.google.com/p/4chandk/issues/list.***

## How is this for ##
This release is for testing, playing and debugging only! The board itself should works more or less, but since the admin-panel is more or less non-existing, no caching is done, and the script is very unstable[1](1.md) I can't recommend that you use it in real life.

[1](1.md) Not unstable as it will break your server, but unstable as in: "It will change a lot, never versions will not be backwards compatible, and I will not make a nifty upgrade script for you."

## What works? ##
See the roadmap: http://bottiger.com/Imageboard_Roadmap
Basically it says: "The boards works more or less, but may be slow, and you don't really have an admin-panel to control your boards".

## Requirements ##
The short version: A standard up-to-date webhost.
The long version: A webserver with PHP5, MySQL5 and GBlib

I know, not all of you have this, some hosts are still running PHP/MySQL 4, but that's just to bad :( I don't plan on ever making this compatible with PHP/MySQL 4.

## Installation ##
Since there isn't a nifty installer you need to manually load _database.sql_ into your database, and then edit _config/config.php_. Only the first couple of lines are required.

oh yeah, and don't forget to chmod _tmp/_ (and it's subfolders) to 777, or just make sure it's writable to the webserver if you know better.

## Known bugs ##
I still don't know why, but on some servers the script just dies when uploading a large image, even with meromy\_limit turned off:
If you find any other bugs (and you will) don't hesitate to report them: http://code.google.com/p/4chandk/issues/list