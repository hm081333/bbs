-- phpMyAdmin SQL Dump
-- version 4.5.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-11-03 01:44:27
-- 服务器版本： 5.7.13-log
-- PHP Version: 5.6.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nyjl`
--

-- --------------------------------------------------------

--
-- 表的结构 `forum_admin`
--

CREATE TABLE `forum_admin` (
  `id` int(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `auth` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `forum_admin`
--

INSERT INTO `forum_admin` (`id`, `username`, `password`, `auth`) VALUES
(1, 'root', '$2y$10$G0yecuK.J5OEkRdKf//0oOku6dIAD5ys/Y0lnBfLNjYACsFrMZLxm', 1);

-- --------------------------------------------------------

--
-- 表的结构 `forum_class`
--

CREATE TABLE `forum_class` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `tips` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `forum_class`
--

INSERT INTO `forum_class` (`id`, `name`, `tips`) VALUES
(1, '闲聊', '随意灌水区'),
(2, 'PHP', ''),
(3, 'JavaScript', ''),
(4, 'Photoshop', ''),
(5, 'Flash', ''),
(6, 'C程序设计', ''),
(7, 'mysqli数据库', ''),
(8, '网页设计', ''),
(9, '网络营销', ''),
(10, '计算机网络基础', ''),
(11, 'Illustrator平面设计', ''),
(12, 'Linux网络操作系统', ''),
(13, 'ASP.NET', ''),
(14, 'Android应用开发', ''),
(15, '体育与健康', '体育与健康');

-- --------------------------------------------------------

--
-- 表的结构 `forum_reply`
--

CREATE TABLE `forum_reply` (
  `id` int(10) NOT NULL,
  `topic_id` int(10) NOT NULL DEFAULT '0',
  `reply_id` int(10) NOT NULL DEFAULT '0',
  `reply_name` varchar(32) CHARACTER SET gbk NOT NULL,
  `reply_email` varchar(100) CHARACTER SET gbk NOT NULL,
  `reply_detail` text CHARACTER SET gbk NOT NULL,
  `reply_pics` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `reply_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `forum_reply`
--

INSERT INTO `forum_reply` (`id`, `topic_id`, `reply_id`, `reply_name`, `reply_email`, `reply_detail`, `reply_pics`, `reply_datetime`) VALUES
(68, 61, 1, 'admin', '', 'first test OK', '', '2016-10-12 12:57:58'),
(75, 61, 6, 'admin', '', '6', '', '2016-10-12 13:35:20'),
(76, 61, 7, 'admin', '', '7', '', '2016-10-12 13:35:24'),
(77, 61, 8, 'admin', '', '8', '', '2016-10-12 13:35:27'),
(78, 61, 9, 'admin', '', '9', '', '2016-10-12 13:35:29'),
(79, 61, 10, 'admin', '', '10', '', '2016-10-12 13:35:32'),
(73, 61, 4, 'admin', '', '第四条回复', '', '2016-10-12 13:25:00'),
(74, 61, 5, 'admin', '', '第五条回复', '', '2016-10-12 13:25:07'),
(72, 61, 3, 'admin', '', '第三条回复', '', '2016-10-12 13:24:45'),
(71, 61, 2, 'admin', '', '第二条回复', '', '2016-10-12 13:24:40'),
(86, 61, 11, 'admin', '', '123123123', '', '2016-10-13 11:50:12'),
(87, 61, 12, 'admin', '', '123123123', '', '2016-10-13 11:50:14'),
(88, 61, 13, 'admin', '', '123123123', '', '2016-10-13 11:50:17'),
(89, 61, 14, 'admin', '', '123123123', '', '2016-10-13 11:50:20'),
(90, 61, 15, 'admin', '', '123123123', '', '2016-10-13 11:50:22'),
(91, 61, 16, 'admin', '', '123123123', '', '2016-10-13 11:50:25'),
(94, 89, 1, 'admin', '', '测试成功 OK！', '', '2016-10-13 22:45:32'),
(95, 89, 2, 'admin', '', '123', '', '2016-10-13 22:51:25');

-- --------------------------------------------------------

--
-- 表的结构 `forum_topic`
--

CREATE TABLE `forum_topic` (
  `id` int(10) NOT NULL,
  `class_id` int(10) NOT NULL,
  `topic` varchar(255) CHARACTER SET gbk NOT NULL,
  `detail` text CHARACTER SET gbk NOT NULL,
  `pics` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(32) CHARACTER SET gbk NOT NULL,
  `email` varchar(100) CHARACTER SET gbk NOT NULL,
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `view` int(10) NOT NULL DEFAULT '0',
  `reply` int(10) NOT NULL DEFAULT '0',
  `sticky` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `forum_topic`
--

INSERT INTO `forum_topic` (`id`, `class_id`, `topic`, `detail`, `pics`, `name`, `email`, `datetime`, `view`, `reply`, `sticky`) VALUES
(67, 1, '新用户尝试发帖', '新用户尝试发帖内容', '', '123', '123@123.123', '2016-10-12 22:09:57', 14, 0, 0),
(68, 1, '新用户尝试发帖', '新用户尝试发帖图片内容', 'pics/1476281421.png', '123', '123@123.123', '2016-10-12 22:10:21', 23, 0, 0),
(89, 1, '手机尝试发图贴', '第一次尝试', 'pics/1476369877.jpeg', '123', '123@123.123', '2016-10-13 22:44:37', 28, 2, 0),
(76, 1, '上传图片预览', '尝试上传图片本地预览', 'pics/1476345220.png', 'admin', '', '2016-10-13 15:53:40', 2, 0, 0),
(61, 1, 'first test', 'first test', '', 'admin', '', '2016-10-12 12:27:22', 96, 16, 0),
(97, 1, 'qwe', 'qwe', 'pics/1477319059.jpg', 'admin', '', '2016-10-24 22:24:19', 1, 0, 0),
(99, 2, '管理员第二次尝试发帖', '管理员第二次尝试发帖', 'pics/1477319322.jpg', '管理员', '', '2016-10-24 22:28:42', 3, 0, 1),
(95, 2, '123', '123', '', 'admin', '', '2016-10-24 19:04:22', 11, 0, 0),
(98, 1, '管理员第一次尝试发帖', '管理员第一次尝试发帖', 'pics/1477319193.jpg', '管理员', '', '2016-10-24 22:26:33', 31, 0, 1),
(101, 2, '修改用户权限验证方式', '修改用户权限验证方式', 'pics/1477540278.jpg', 'hm081333', '522751485@qq.com', '2016-10-27 11:51:18', 15, 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `forum_user`
--

CREATE TABLE `forum_user` (
  `id` int(10) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `realname` varchar(50) NOT NULL,
  `auth` int(1) NOT NULL DEFAULT '0',
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

--
-- 转存表中的数据 `forum_user`
--

INSERT INTO `forum_user` (`id`, `username`, `password`, `email`, `realname`, `auth`, `regdate`) VALUES
(3, 'root', '$2y$10$2jH.x35xlp4c7MJskYnVeO8VuTpvREUjfwPAdKp.78h2GY.S9hMXu', 'root@root.root', 'test', 0, '2016-10-27 12:08:58'),
(1, '管理员', '管理员', '522751485@qq.com', 'LYi-Ho', 0, '2016-10-24 18:00:00'),
(4, 'hm081333', '$2y$10$ww7CvAzywm63TrgxAc5LjO5LbCj5Qk/NrEId5QdWkEerXHcZVidhq', '522751485@qq.com', 'LYi-Ho', 1, '2016-10-27 11:32:20'),
(33, '123123', '$2y$10$ZoUxjzUlQnfVWxFts3A2HOpTJknT2ahSZHPabqfOCqhF.0tc9LmSC', '123@123.123', '123', 0, '2016-10-31 22:03:47'),
(34, '123', '$2y$10$iZsEASmPA5ikW1hJa/l47.FED279nCjB0TFMEj4bE44EdX.4InKW.', '', '', 0, '2016-10-31 22:12:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `forum_admin`
--
ALTER TABLE `forum_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forum_class`
--
ALTER TABLE `forum_class`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forum_reply`
--
ALTER TABLE `forum_reply`
  ADD PRIMARY KEY (`id`),
  ADD KEY `a_id` (`reply_id`);
ALTER TABLE `forum_reply` ADD FULLTEXT KEY `reply_pics` (`reply_pics`);

--
-- Indexes for table `forum_topic`
--
ALTER TABLE `forum_topic`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forum_user`
--
ALTER TABLE `forum_user`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `forum_admin`
--
ALTER TABLE `forum_admin`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `forum_class`
--
ALTER TABLE `forum_class`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- 使用表AUTO_INCREMENT `forum_reply`
--
ALTER TABLE `forum_reply`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;
--
-- 使用表AUTO_INCREMENT `forum_topic`
--
ALTER TABLE `forum_topic`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;
--
-- 使用表AUTO_INCREMENT `forum_user`
--
ALTER TABLE `forum_user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
