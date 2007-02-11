-- phpMyAdmin SQL Dump
-- version 2.6.4-pl4
-- http://www.phpmyadmin.net
-- 
-- Host: mysql19.servage.net
-- Generation Time: Feb 09, 2007 at 01:02 AM
-- Server version: 5.0.15
-- PHP Version: 4.4.4
-- 
-- Database: `bottiger_4chandk`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `boards`
-- 

CREATE TABLE `boards` (
  `id` int(11) NOT NULL auto_increment,
  `section_id` int(11) NOT NULL,
  `dir` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `banner` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `posts`
-- 

CREATE TABLE `posts` (
  `id` int(11) NOT NULL auto_increment,
  `thread_id` int(11) default NULL,
  `board_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `filename` varchar(255) NOT NULL,
  `posted_at` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `sections`
-- 

CREATE TABLE `sections` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;