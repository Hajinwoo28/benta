-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 27, 2026 at 01:45 AM
-- Server version: 5.1.36
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bentadb`
--

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE IF NOT EXISTS `carts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clientid` int(11) NOT NULL DEFAULT '0',
  `itemid` int(11) NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `price` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `clientid`, `itemid`, `quantity`, `price`) VALUES
(23, 0, 2, 1, 200),
(22, 0, 3, 1, 783);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category`) VALUES
(2, 'damit'),
(3, 'short');

-- --------------------------------------------------------

--
-- Table structure for table `productbl`
--

CREATE TABLE IF NOT EXISTS `productbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemname` text,
  `categoryid` int(11) NOT NULL DEFAULT '0',
  `description` text,
  `price` float NOT NULL DEFAULT '0',
  `quantity` int(11) NOT NULL DEFAULT '0',
  `img` text,
  `deleted` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `productbl`
--

INSERT INTO `productbl` (`id`, `itemname`, `categoryid`, `description`, `price`, `quantity`, `img`, `deleted`) VALUES
(2, 'iphone18', 2, 'iphone 18 Pro Max 1Terabite', 180000, 38, 'covers/iphone18.jpg', b'0'),
(3, 'ph care', 2, 'good for face', 783, 75, 'covers/phcare.jpg', b'0'),
(4, 'lamborghini', 0, 'malakas maka pogi', 2.5e+006, 100, 'covers/lamborghini.jpg', b'0'),
(1, 'Young Stunna Outfit', 1, 'More Rizz', 1500, 3, 'covers/youngstunnaoutfit.jpg', b'0');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clientid` int(11) NOT NULL,
  `subtotal` float NOT NULL,
  `fee` float NOT NULL,
  `total` float NOT NULL,
  `status` text,
  `orderdate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `clientid`, `subtotal`, `fee`, `total`, `status`, `orderdate`) VALUES
(6, 5, 783, 100, 883, 'cancelled', '2026-05-19 16:03:35'),
(4, 5, 7830, 100, 7930, 'completed', '2026-05-19 02:41:21'),
(7, 5, 783, 100, 883, 'completed', '2026-05-19 16:05:27'),
(8, 5, 200, 100, 300, 'completed', '2026-05-19 20:22:08'),
(9, 5, 4698, 100, 4798, 'approved', '2026-05-20 12:27:41'),
(10, 6, 200, 100, 300, 'pending', '2026-05-26 22:54:41'),
(11, 9, 0, 100, 100, 'pending', '2026-05-27 09:20:14');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_products`
--

CREATE TABLE IF NOT EXISTS `transaction_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` int(11) NOT NULL,
  `productid` int(11) DEFAULT NULL,
  `itemname` text,
  `quantity` int(11) NOT NULL,
  `price` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `transaction_products`
--

INSERT INTO `transaction_products` (`id`, `transaction_id`, `productid`, `itemname`, `quantity`, `price`) VALUES
(2, 4, 3, 'sadada', 10, 783),
(3, 6, 3, '', 1, 783),
(4, 7, 3, '', 1, 783),
(5, 8, 2, '', 1, 200),
(6, 9, 3, '', 6, 783);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `username` text NOT NULL,
  `fullname` text NOT NULL,
  `contact` text NOT NULL,
  `address` text NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `password` text NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `username`, `fullname`, `contact`, `address`, `role`, `password`) VALUES
(1, 'admin', 'admin', '09492949243', 'buliran', 'admin', 'hiadminangsarapmo'),
(2, 'user123', 'user', '09142841256', 'buliran', 'user', '12345'),
(3, 'hajinwoo28', 'Reynald Jake Malabanan', '09857794783', '268 Isla St, Buliran, San Antonio, Quezon, Philippines', 'user', '12345678'),
(4, 'Jacob21', 'Jacob Rei L.Malabanan', '09857794783', '268 Isla St, Buliran, San Antonio, Quezon, Philippines', 'user', 'koyapakess');
