-- phpMyAdmin SQL Dump
-- version 2.6.4-pl4
-- http://www.phpmyadmin.net
-- 
-- Host: mysql19.servage.net
-- Generation Time: Feb 22, 2007 at 11:33 PM
-- Server version: 5.0.15
-- PHP Version: 4.4.4
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `bans`
-- 

CREATE TABLE `bans` (
  `id` int(11) NOT NULL auto_increment,
  `ip` varchar(255) collate utf8_bin NOT NULL,
  `expire` int(11) NOT NULL,
  `board_id` varchar(255) collate utf8_bin default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `boards`
-- 

CREATE TABLE `boards` (
  `id` int(11) NOT NULL auto_increment,
  `section_id` int(11) NOT NULL,
  `name` varchar(255) collate utf8_bin NOT NULL,
  `dir` varchar(255) collate utf8_bin NOT NULL,
  `description` text collate utf8_bin NOT NULL,
  `filesize` int(11) NOT NULL,
  `banner` varchar(255) collate utf8_bin NOT NULL,
  `threads_page` int(11) NOT NULL,
  `threads_board` int(11) NOT NULL,
  `forced_anonymous` tinyint(1) NOT NULL,
  `comment_length` int(11) NOT NULL,
  `thread_length` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `section_id` (`section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `posts`
-- 

CREATE TABLE `posts` (
  `id` int(11) unsigned NOT NULL,
  `thread_id` int(11) default NULL,
  `board_id` int(11) NOT NULL,
  `title` varchar(255) collate utf8_bin NOT NULL,
  `name` varchar(255) collate utf8_bin NOT NULL,
  `tripecode` varchar(255) collate utf8_bin NOT NULL,
  `email` varchar(255) collate utf8_bin NOT NULL,
  `password` varchar(255) collate utf8_bin NOT NULL,
  `message` text collate utf8_bin NOT NULL,
  `filename` varchar(255) collate utf8_bin NOT NULL,
  `posted_at` int(11) NOT NULL,
  `last_update` int(11) default NULL,
  `ip` varchar(255) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`,`board_id`),
  KEY `board_id` (`board_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

-- 
-- Table structure for table `sections`
-- 

CREATE TABLE `sections` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) collate utf8_bin NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- 
-- Constraints for dumped tables
-- 

-- 
-- Constraints for table `boards`
-- 
ALTER TABLE `boards`
  ADD CONSTRAINT `boards_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `posts`
-- 
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
