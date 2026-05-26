-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 25, 2026 at 12:28 PM
-- Server version: 5.1.36
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: dbuser
--

-- --------------------------------------------------------

--
-- Table structure for table carts
--

CREATE TABLE IF NOT EXISTS carts (
  id int(11) NOT NULL AUTO_INCREMENT,
  clientid int(11) NOT NULL DEFAULT '0',
  itemid int(11) NOT NULL DEFAULT '0',
  quantity int(11) NOT NULL DEFAULT '0',
  price float NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

--
-- Dumping data for table carts
--

INSERT INTO carts (id, clientid, itemid, quantity, price) VALUES
(23, 0, 2, 1, 200),
(22, 0, 3, 1, 783);

-- --------------------------------------------------------

--
-- Table structure for table categories
--

CREATE TABLE IF NOT EXISTS categories (
  id int(11) NOT NULL AUTO_INCREMENT,
  category text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table categories
--

INSERT INTO categories (id, category) VALUES
(2, 'damit'),
(3, 'short');

-- --------------------------------------------------------

--
-- Table structure for table productbl
--

CREATE TABLE IF NOT EXISTS productbl (
  id int(11) NOT NULL AUTO_INCREMENT,
  itemname text,
  categoryid int(11) NOT NULL DEFAULT '0',
  description text,
  price float NOT NULL DEFAULT '0',
  quantity int(11) NOT NULL DEFAULT '0',
  img text,
  deleted bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table productbl
--

INSERT INTO productbl (id, itemname, categoryid, description, price, quantity, img, deleted) VALUES
(2, 'tem', 2, 'sada24', 200, 38, 'covers/134051742773875746.jpg', b'0'),
(3, 'sadada', 2, 'dada', 783, 75, 'covers/WIN_20260328_10_49_06_Pro.jpg', b'0');

-- --------------------------------------------------------

--
-- Table structure for table transactions
--

CREATE TABLE IF NOT EXISTS transactions (
  id int(11) NOT NULL AUTO_INCREMENT,
  clientid int(11) NOT NULL,
  subtotal float NOT NULL,
  fee float NOT NULL,
  total float NOT NULL,
  status text,
  orderdate datetime DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table transactions
--

INSERT INTO transactions (id, clientid, subtotal, fee, total, status, orderdate) VALUES
(6, 5, 783, 100, 883, 'cancelled', '2026-05-19 16:03:35'),
(4, 5, 7830, 100, 7930, 'completed', '2026-05-19 02:41:21'),
(7, 5, 783, 100, 883, 'completed', '2026-05-19 16:05:27'),
(8, 5, 200, 100, 300, 'completed', '2026-05-19 20:22:08'),
(9, 5, 4698, 100, 4798, 'approved', '2026-05-20 12:27:41');

-- --------------------------------------------------------

--
-- Table structure for table transaction_items
--

CREATE TABLE IF NOT EXISTS transaction_items (
  id int(11) NOT NULL AUTO_INCREMENT,
  transaction_id int(11) NOT NULL,
  productid int(11) DEFAULT NULL,
  itemname text,
  quantity int(11) NOT NULL,
  price float NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table transaction_items
--

INSERT INTO transaction_items (id, transaction_id, productid, itemname, quantity, price) VALUES
(2, 4, 3, 'sadada', 10, 783),
(3, 6, 3, '', 1, 783),
(4, 7, 3, '', 1, 783),
(5, 8, 2, '', 1, 200),
(6, 9, 3, '', 6, 783);

-- --------------------------------------------------------

--
-- Table structure for table users
--

CREATE TABLE IF NOT EXISTS users (
  user_id int(11) NOT NULL AUTO_INCREMENT,
  username text NOT NULL,
  name text NOT NULL,
  contact text NOT NULL,
  address text NOT NULL,
  role enum('admin','user') NOT NULL,
  password text NOT NULL,
  PRIMARY KEY (user_id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table users
--

INSERT INTO users (user_id, username, name, contact, address, role, password) VALUES
(1, 'admin', 'admin', '49294924', 'buliran', 'admin', 'admin12345'),
(5, 'user123', 'user', '91428412', 'buliran', 'user', '123456');
3.2.0.1