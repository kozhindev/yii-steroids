-- phpMyAdmin SQL Dump
-- version 4.2.8
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Янв 19 2018 г., 09:08
-- Версия сервера: 5.6.20
-- Версия PHP: 7.1.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- База данных: `yii-steroids`
--

-- --------------------------------------------------------

--
-- Структура таблицы `test_article`
--

CREATE TABLE IF NOT EXISTS `test_article` (
`id` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `test_article_photos`
--

CREATE TABLE IF NOT EXISTS `test_article_photos` (
  `photoId` int(11) NOT NULL,
  `articleId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `test_attachments`
--

CREATE TABLE IF NOT EXISTS `test_attachments` (
`id` int(11) NOT NULL,
  `articleId` int(11) NOT NULL,
  `fileName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `test_category`
--

CREATE TABLE IF NOT EXISTS `test_category` (
`id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `test_photos`
--

CREATE TABLE IF NOT EXISTS `test_photos` (
`id` int(11) NOT NULL,
  `fileName` varchar(255) NOT NULL,
  `barId` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `test_photo_bar`
--

CREATE TABLE IF NOT EXISTS `test_photo_bar` (
`id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `test_photo_foo`
--

CREATE TABLE IF NOT EXISTS `test_photo_foo` (
`id` int(11) NOT NULL,
  `photoId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `test_article`
--
ALTER TABLE `test_article`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `test_attachments`
--
ALTER TABLE `test_attachments`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `test_category`
--
ALTER TABLE `test_category`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `test_photos`
--
ALTER TABLE `test_photos`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `test_photo_bar`
--
ALTER TABLE `test_photo_bar`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `test_photo_foo`
--
ALTER TABLE `test_photo_foo`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `test_article`
--
ALTER TABLE `test_article`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `test_attachments`
--
ALTER TABLE `test_attachments`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `test_category`
--
ALTER TABLE `test_category`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `test_photos`
--
ALTER TABLE `test_photos`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `test_photo_bar`
--
ALTER TABLE `test_photo_bar`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблицы `test_photo_foo`
--
ALTER TABLE `test_photo_foo`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;