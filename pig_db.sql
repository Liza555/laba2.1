

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `pig`
--

-- --------------------------------------------------------

--
-- Структура таблицы `avaliable_games`
--

DROP TABLE IF EXISTS `avaliable_games`;
CREATE TABLE IF NOT EXISTS `avaliable_games` (
  `game` int(11) NOT NULL AUTO_INCREMENT,
  `creator` varchar(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`game`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `current_games`
--

DROP TABLE IF EXISTS `current_games`;
CREATE TABLE IF NOT EXISTS `current_games` (
  `game` int(11) UNSIGNED NOT NULL,
  `player1` varchar(20) NOT NULL,
  `player2` varchar(20) NOT NULL,
  PRIMARY KEY (`game`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `current_games`
--

INSERT INTO `current_games` (`game`, `player1`, `player2`) VALUES
(10, 'Alice', 'Bob');

-- --------------------------------------------------------

--
-- Структура таблицы `popular_passwords`
--

DROP TABLE IF EXISTS `popular_passwords`;
CREATE TABLE IF NOT EXISTS `popular_passwords` (
  `password` varchar(30) NOT NULL,
  UNIQUE KEY `password` (`password`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `popular_passwords`
--

INSERT INTO `popular_passwords` (`password`) VALUES
('!qaz2wsx'),
('000000'),
('11111'),
('111111'),
('121212'),
('123'),
('123123'),
('1234'),
('12345'),
('123456'),
('1234567'),
('12345678'),
('123456789'),
('1234567890'),
('131313'),
('1q2w3e4r'),
('1qaz2wsx'),
('1qaz@wsx'),
('1qazxsw2'),
('55555'),
('654321'),
('666666'),
('696969'),
('7777777'),
('987654321'),
('abc123'),
('abcd1234'),
('affair'),
('amanda'),
('andrew'),
('anthony'),
('asdfasdf'),
('asdfg'),
('asdfgh'),
('asdfghjkl'),
('ashley'),
('ashleymadison'),
('asshole'),
('baseball'),
('batman'),
('beautiful'),
('bigdick'),
('buster'),
('charlie'),
('cheater'),
('cocacola'),
('computer'),
('corvette'),
('cowboys'),
('dallas'),
('DEFAULT'),
('dragon'),
('ferrari'),
('football'),
('freedom'),
('fuckme'),
('fuckoff'),
('fuckyou'),
('george'),
('harley'),
('hello'),
('hockey'),
('horny'),
('hosts'),
('hunter'),
('ihateyou'),
('iloveme'),
('iloveyou'),
('jackson'),
('jennifer'),
('jessica'),
('jordan'),
('jordan23'),
('kazuga'),
('killer'),
('letmein'),
('liverpool'),
('looking'),
('madison'),
('maggie'),
('master'),
('matthew'),
('mercedes'),
('michael'),
('money'),
('monkey'),
('mustang'),
('password'),
('password1'),
('pepper'),
('playboy'),
('princess'),
('pussy'),
('qazwsx'),
('qwert'),
('qwerty'),
('qwertyuiop'),
('ranger'),
('robert'),
('secret'),
('shadow'),
('soccer'),
('steelers'),
('summer'),
('sunshine'),
('superman'),
('thomas'),
('tigger'),
('trustno1'),
('whatever'),
('william'),
('yankees'),
('zaq12wsx'),
('zxcvbnm');

-- --------------------------------------------------------

--
-- Структура таблицы `stats`
--

DROP TABLE IF EXISTS `stats`;
CREATE TABLE IF NOT EXISTS `stats` (
  `Rank` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(20) NOT NULL,
  `Wins` int(11) NOT NULL,
  `Total games` int(11) NOT NULL,
  `Rating, %` int(11) NOT NULL,
  UNIQUE KEY `Rank` (`Rank`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `stats`
--

INSERT INTO `stats` (`Rank`, `Name`, `Wins`, `Total games`, `Rating, %`) VALUES
(1, 'Bob', 3, 3, 100),
(2, 'Alice', 0, 4, 0),
(3, 'Carol', 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `login` varchar(20) CHARACTER SET utf8 NOT NULL,
  `password` varchar(60) CHARACTER SET utf8 NOT NULL,
  `token` varchar(256) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `token`) VALUES
(1, 'Alice', '$2y$10$SEO/h8hTuxuZpHRWBW3AL.8NBBlhuHfbHd0iLbLZtFqIrJ62p8nAe', 'iSkHaRB7tATAkZaAFd5eyiseEEQFfY439tKryeQy3AGiZbkAZBi9dyBSDhzFdY99Yz1zH6Ter0kzhYrY5fzsT3DQtn936d9Hs052NfSnKG06D0iE5nA6kZy7bSGNR23EZH4RGYFZZRk3Nk9K1Tt7KaeRRF5Yn6NZbYY4ySkR0hHhQ9SbKHez2S0YfShkYBkBrnDEa915TaY3Gin5k0Zf7SsiFnsdbsTEyhaBNTrHFs15tYRfYzss7dS9YisyrGd4'),
(2, 'Bob', '$2y$10$PZNtNIZ4O66TV/Zce7slz.HNcE1ihfoOVbUCBUHZB3BgGjHAzOi3C', 'NS6eeAnRds53kYB2YFZfz8z5takTt80QKSar2DEZrf8QQde9fh1FFYGh6HnRhbB8Baa1A2Hkty7Z63TKBkYkG1K2DDf75F6YfZ5Y3QGar50rd8fFKRHtAsE31Y5SGst3Y4R9h0Arb4FhfED1NDk1hfrrbzHY8FKyizrTzte3ZidBbyRFFQ0S1y6aiAdiybTkHkZYDnT1t5Hb3NYHshi4t7Eii0E9K9TEZEefAGifZ84bGnR86dFFT64TbHZkTtNd'),
(3, 'Carol', '$2y$10$W7TP5viR38V15.qpeuuSu.m2nRN9YxCMyfuA1yvGcy/VnK8HxfnH6', '');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
