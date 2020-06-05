<?php

if (!isset($_GET["id"])) 
{
	header("Location: login_form.php?error=301");
	exit();
}
	$mysqli= new mysqli("localhost","root","","pig");// открываем БД "pig" на хосте "localhost" с логином "root" и пустым паролем
	$mysqli->query("SET NAMES 'utf8'");// устанавливаем кодировку.
	if ($mysqli->connect_errno)// 4-8: проверка на ошибку при открытии БД. Если произошла ошибка, то выходим
	{
    	printf("Соединение не удалось: %s\n", $mysqli->connect_error);
    	exit();
	}
	else
	{	
		$good=false;// 11-28: Проверка на авторизованный вход. Если не авторизован, то переход наформу логина
		$result_correct=$mysqli->query("SELECT * FROM `users`");
		while ($row=$result_correct->fetch_assoc()) 
		{
			$id=$_GET["id"];
			if (htmlspecialchars($_COOKIE["name$id"])===$row["login"] && htmlspecialchars($_COOKIE["token$id"])===$row["token"]) 
			{
				$login=$row["login"];
				$good=true;
			}
		}
																			//Unsigned user:
		if (!$good) 
		{
			$mysqli->close();
			header("Location: login_form.php?error=301");
			exit();
		}

		function printGames($player,$gameId,$buttonName,$cr,$enemy)// функция динамической отрисовки HTML-элементов
		{
			print '<tr>';// вывод новой строки в таблице
				print '<td>';// вывод ячейки в строке
					print $player;// выод логина
					print '<form method="post" action="">';
						print( '<input type="hidden" name="field" value="'.$gameId.'">');// 36-38: скрытые поля, передающие в форму значения id игры, имя 		создателя игры и его противника
						print( '<input type="hidden" name="creator" value="'.$cr.'">');
						print( '<input type="hidden" name="enemy" value="'.$enemy.'">');
						print ('<button type="submit" name="'.$buttonName.'" value="'.$player.'">'.$buttonName.'</button>');// вывод кнопки 
					print '</form>';
				print '</td>';
			print '</tr>';
		}
		$GLOBALS["Agames"]=$mysqli->query("SELECT* FROM `avaliable_games` ");// выбор каждого стобца в строке таблицы `avaliable_games`
		$GLOBALS["CurGames"]=$mysqli->query("SELECT* FROM `current_games` ");// выбор каждого стобца в строке таблицы `current_games`

																			//Avaliable Games:
		function AvaliableGames($login)// функция вывода на экран игр, созданных другими игроками, в которые можно зайти
		{
			while ($row=$GLOBALS["Agames"]->fetch_assoc())// проверяем каждую запись в таблице и раскладываем каждую  в ассоциативный массив
			{
				$gameId= $row["game"];// id игры в проверяемой записи
				$creator=$row["creator"];// создатель игры в  проверяемой записи
				if ($creator!==$login)//53-57:  если не мы создали игру, то выводим её в таблице доступных для входа игр 
				{
					$buttonName='play';
					printGames($creator,$gameId,$buttonName,$creator,'');
				}
			}		
		}
																					//My Games:

		function MyGames($login)// функция вывода на экран игр, в которые мы сейчас играем
		{
			while ($rowCur=$GLOBALS["CurGames"]->fetch_assoc()) // проверяем каждую запись в таблице и раскладываем каждую  в ассоциативный массив
			{
				$gameId= $rowCur["game"];// id игры в проверяемой записи
				$p1=$rowCur["player1"];// создатель игры в  проверяемой записи
				$p2=$rowCur["player2"];// противник создателя в  проверяемой записи
				$buttonName='go';// имя кнопки 
				if ($login==$p1) // 71-78: Вывод на экран логина нашего противника и кнопки входа в игру
				{
					printGames($p2,$gameId,$buttonName,$p1,$p2);
				}
				else if ($login==$p2) 
				{
					printGames($p1,$gameId,$buttonName,$p1,$p2);
				}
			}			
		}
																					//Play:

		function Play($db,$game,$creator,$enemy,$login,$id)// функция входа в достуную игру
		{
			$db->query("INSERT INTO `current_games` (`game`, `player1`, `player2`) VALUES ('".$game."','".$creator."', '".$enemy."')");// помещаем в таблицу 	`current_games` игру с "id", взятым из `avaliable_games` 
			$db->query("DELETE FROM avaliable_games WHERE game=$game");// удаляем запись с взятым "id" из таблицы  `avaliable_games`
			session_start();// начинаем работу с сессиями
			$_SESSION["enemy_$game"]=$enemy;// записвыем в сессию противника создателя в данной игре
			$db->close();// закрываем БД
			header("Location: game.php?creator=$creator&game=$game&enemy=$enemy&login=$login&id=$id");// переходим на страницу игры с указанными 				GET-параметрами
		}
																			//Exit from account:
		if (isset($_POST["exit"]))// 93-101: если выходим из аккаунта, то:
		{
			$mysqli->query("UPDATE `users` SET `token` = '' WHERE `users`.`login` = '$login'");// удаляем токен из нашей зписи в таблице пользователей
			setcookie("name$id","",time()-1);// удаляем cookie логина
			setcookie("token$id","",time()-1);// удаляем cookie токена
			session_destroy();// удаляем сессию 
			$mysqli->close();// закрываем БД
			header("Location: login_form.php");// переход на форму логина
		}
		

																			//Create game (MM):
		if (isset($_POST["create_game"])) // создание игры
		{
			$newGame=$mysqli->query("INSERT INTO `avaliable_games` (`creator`) VALUES ('".$login."')");// создаём новую запись в таблице поиска игр
			$MyRank=$mysqli->query("SELECT `Rank` FROM `stats` WHERE `Name`='$login'");// поиск нашего ранга в таблице  статистики игроков
			$rowMyRank=$MyRank->fetch_assoc();// ранг в ассоциативный массив
			$stats=$mysqli->query("SELECT COUNT(*) FROM `stats`");// количесво записей в таблице статистики
			$count=$stats->fetch_row();// количество в массив
			for($eps=1;$eps < $count[0];$eps++)// 112-137: Match Making
			{
				while ($rowAG=$GLOBALS["Agames"]->fetch_assoc())// проверяем каждую запись в таблице `avaliable_games`
				{
					if ($rowAG["creator"]!==$login)// если мы не являемся создателем проверяемой игры
					{
						$creator=$rowAG["creator"];// создатель игры
						$Rank=$mysqli->query("SELECT `Rank` FROM `stats`WHERE `Name`='$creator'");// поиск ранга создателя
						$rowCreator= $Rank->fetch_assoc();// ранг создателя в ассоциативный массив
						if (abs($rowMyRank["Rank"]-$rowCreator["Rank"])<=$eps)// 121-125: если модуль разницы нашего ранга и ранга создателя  меньше текущей	 погрешности, то MM с ним
						{
							$gameId=$rowAG["game"];
							Play($mysqli,$gameId,$creator,$login,$login,$id);
						}
					}
				}
				/*echo $eps;
				echo "<br>";*/
			}
			// 132-139:  если не нашлось противника, то создаём свою новую игру
			$res=$mysqli->query("SELECT MAX(`game`) FROM `avaliable_games`");// 132-137: поиск "id" нашей новой игры
			$test=$res->fetch_assoc();
			foreach ($test as $key => $value) 
			{
   				$gameId=$value;
    		}
			$mysqli->close();// закрываем БД
			header("Location: game.php?creator=$login&game=$gameId&enemy=$enemy&login=$login&id=$id");// переходим на страницу игры с указанными 				GET-параметрами
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>General page (index.php)</title>
</head>
<body>
	<table border="1" cellspacing="0" align="center" width="80%" height="500" >
		<tr >
			<td  colspan="2">
				
				<form name="exit" method="post" action="" align="center">
				<button type="submit" name="exit" >Exit</button>
				<p>
					<h1><?php echo "Hello, @".$login; ?></h1>	
				</form>
				</p>
				<form name="stats" align="center" method="post" action="stats.php">
					<input type="hidden" name="id" value="<?=$id;?>">
					<button type="submit" name="stats" value="<?=$login;?>">Stats</button>
				</form>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<h2 align="center">My games:</h2>
				<hr>
				<table align="center" id="MG">
					<?php
						MyGames($login);// вывод на экран игр, в которые играем
						if (isset($_POST["go"])) 
						{
							$creator=$_POST["creator"];
							$game=$_POST["field"];
							$enemy=$_POST["enemy"];
							header("Location: game.php?creator=$creator&game=$game&enemy=$enemy&login=$login&id=$id");// переход в игру по нажатию "go"
						}
					?>
				</table>
			</td>
			<td width="50%">
				<h2 align="center">Avaliable games:</h2>
				<hr>
				<table align="center" id="AG">
					<?php
						AvaliableGames($login);// вывод на экран доступных для входа игр
						if (isset($_POST["play"])) 
						{
							$game=$_POST["field"];
							$creator=$_POST["play"];
							Play($mysqli,$game,$creator,$login,$login,$id);// переход в игру по нажатию "play"
						}	
					?>
				</table>
				<hr>
				<form name="create_game" align="center" method="post" action="">
					<button type="submit" name="create_game">Create game</button>
				</form>
			</td>
		</tr>
	</table>
</body>
</html>
