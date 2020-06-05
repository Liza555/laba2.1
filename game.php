<?php 
	if(session_id() == '')// 2-5: если не существует текущей сессии,то создаём её
	{
    session_start();
	}
echo "<br>";
	$p1=$_GET["creator"];// создатель текущей игры
	$GLOBALS["game"]=$_GET["game"];// id текущей игры
	$Mylogin=$_GET["login"];// наш логин
	$GLOBALS["id"]=$_GET["id"];// наш id
	$GLOBALS["stake"]=0;// количество очков на кону
	$random=1;// выпавшее количество очков
	//print_r($_SESSION);
	$game=$GLOBALS["game"];
	if (isset($_SESSION["winner$game"])) // 15-24: удаляем все используемые сессии и перерходим на главную страницу, если игра закончилась 
	{
		echo $_SESSION["winner$game"]." is win!";
		$_SESSION["score1_$game"]="";
		$_SESSION["score2_$game"]="";
		$_SESSION["stake_$game"]="";
		$_SESSION["move_$game"]="";
		$_SESSION["winner$game"]="";
		header("Location: index.php?id=$id");
	}
																		//Start parametrs:
	if (!isset($_SESSION["score1_$game"]) || !isset($_SESSION["score2_$game"]) || !isset($_SESSION["stake_$game"]) || !isset($_SESSION["move_$game"])) 
	{
		$game=$GLOBALS["game"];	
		$_SESSION["score1_$game"]=0;// начальное количество очков у игрока 1
		$_SESSION["score2_$game"]=0;// начальное количество очков у игрока 2
		$_SESSION["stake_$game"]=0;// начальное количество очков на кону
		$_SESSION["move_$game"]=$_GET["creator"];// первый ход делает создатель
	}
	if (isset($_SESSION["enemy_$game"]))
	{
		$p2=$_SESSION["enemy_$game"];// если в игру зашёл пртивник, то записываем его логин
	}
	else
	{
		$p2="Waiting for enemy...";// иначе вывод "Waiting for enemy..."
	}
																		//MENU:
	if (isset($_POST["menu"]))//43-46: при нажатии кнопки "menu" переходим на нашу главную страницу с сохранением игры
	{
		header("Location: index.php?id=$id");
	}
																		//SURREND:
	if (isset($_POST["surrend"]))// 48-60: при нажатии на кнопку "surrend" смотрим, кто её нажал и этого игрока записываем в проигравшие, а противника 			выигравшие 
	{
		if ($_POST["surrend"]==$p1) 
		{
			GameOver($p2,$p1);
			$_SESSION["winner$game"]=$p2;
		}
		else
		{
			GameOver($p1,$p2);
			$_SESSION["winner$game"]=$p1;
		}
	}
	
																		//THROW:
	if (isset($_POST["throw"]))// "бросок игральной кости"
	{
		if ($_SESSION["move_$game"]===$_POST["throw"] && $_GET["enemy"]!=null)// бросить может только тот, кто сейчас ходит
		{
			$random=rand(1,6);// генерируем случайное число [1;6]
			if ($random==1)// 68-78: если выпало 1, то меняем ходящего и обнуляем очки на кону 
			{
				if ($_SESSION["move_$game"]===$p1) 
				{
					$_SESSION["move_$game"]=$p2;
				}
				else 
				{
					$_SESSION["move_$game"]=$p1;
				}
				$_SESSION["stake_$game"]=0;
			}
			else// 80-84: если выпало чило, отличное от 1, то складываем его с текущи количеством очков на кону
			{
				$GLOBALS["stake"]=$_SESSION["stake_$game"]+$random;
				$_SESSION["stake_$game"]=$GLOBALS["stake"];
			}
		}
	}
																		//PASS:
	if (isset($_POST["pass"]))//при нажатии "pass" смотрим, кто её нажал
	{
		if ($_POST["pass"]==$_SESSION["move_$game"])// 90-101: если нажал ходящий, то складываем очки на кону с его очками и меняем ходящего
		{
			if ($_SESSION["move_$game"]===$p1) 
			{
				$_SESSION["score1_$game"]=$_SESSION["score1_$game"]+$_SESSION["stake_$game"];
				$_SESSION["move_$game"]=$p2;
			}
			else
			{
				$_SESSION["score2_$game"]=$_SESSION["score2_$game"]+$_SESSION["stake_$game"];
				$_SESSION["move_$game"]=$p1;
			}
			$_SESSION["stake_$game"]=0;// обнуляем количество очков на кону
			if ($_SESSION["score1_$game"]>=2)// 102-110: если очки игрока >=100, то он выиграл
			{
				GameOver($p1,$p2);
			}
			else if ($_SESSION["score2_$game"]>=2) 
			{
				GameOver($p2,$p1);
			}
		}
	}
																		//GAME OVER:
	function GameOver($winner,$loser)// функция завершения игры
	{
		print ('<h2 align="left">'.$winner.'  is win!'.'</h2>');// выводим на экран логин победителя
		$mysqli= new mysqli("localhost","root","","pig");// открываем БД
		$mysqli->query("SET NAMES 'utf8'");// устанавливаем кодировку
		if ($mysqli->connect_errno)//// 119-122: проверка на ошибку при открытии БД. Если произошла ошибка, то выходим
		{
	    	printf("Соединение не удалось: %s\n", $mysqli->connect_error);
		}
		else
		{
			$id=$GLOBALS["id"];															
			$game=$GLOBALS["game"];	
																// FOR WINNER:														
			$statsWinner=$mysqli->query("SELECT * FROM `stats` WHERE `Name`='$winner'");// ищем запись победителя в таблице статистики
			$row=$statsWinner->fetch_assoc();// в ассоциативный массив
			$wins= (integer) $row['Wins'];
			$wins+=1;// увеличиваем количество выигранных игр у победителя
			$TotalGames= (integer) $row['Total games'];
			$TotalGames+=1;//увеличиваем общее количество игр у победителя
			$rating=round($wins/$TotalGames*100);// пересчитываем рейтинг победителя
			$mysqli->query("UPDATE `stats` SET `Wins`='$wins', `Total games`='$TotalGames', `Rating, %`='$rating' WHERE `Name`='$winner'");// заносим 			обновлённые данные в таблицу статистики
																// FOR LOSER:
			$statsLoser=$mysqli->query("SELECT * FROM `stats` WHERE `Name`='$loser'");// ищем запись проигравшего в таблице статистики
			$row=$statsLoser->fetch_assoc();// в ассоциативный массив
			$wins= (integer) $row['Wins'];
			$TotalGames= (integer) $row['Total games'];
			$TotalGames+=1;//увеличиваем общее количество игр у проигравшего
			$rating=round($wins/$TotalGames*100);// пересчитываем рейтинг проигравшего
			$mysqli->query("UPDATE `stats` SET `Total games`='$TotalGames', `Rating, %`='$rating' WHERE `Name`='$loser'");// заносим обновлённые данные в 		таблицу статистики
			$mysqli->query("DELETE FROM current_games WHERE `game`='$game'");// удаляем игру из текущих
																//SORTING:
			$i=1;
			$sort=$mysqli->query("SELECT * FROM `stats` ORDER BY `Rating, %` DESC");
			while ($row=$sort->fetch_assoc())// в ассоциативный массив заносим все записи таблицы статистики, отсортированные по убыванию рейтинга 
			{
				$name=$row["Name"];
				$wins=$row["Wins"];
				$total=$row["Total games"];
				$rating=$row["Rating, %"];
				echo $name;
				echo "<br>";
				echo $i;
				echo "<br>";
				echo $rating;
				echo "<br>";
				/*echo $name;
				echo "<br>";
				echo $name;
				echo "<br>";*/
				$mysqli->query("UPDATE `stats` SET `Name`='$name' WHERE `Rank`='$i'");// 154-157: обновляем все данные в таблице статистики
				$namme=$mysqli->query("SELECT * FROM `stats` WHERE `Rank`='$i'");
				print_r($nameres=$namme->fetch_assoc());
				echo "<br>";
				$mysqli->query("UPDATE `stats` SET `Wins`='$wins' WHERE `Rank`='$i'");
				$mysqli->query("UPDATE `stats` SET `Total games`='$total' WHERE `Rank`='$i'");
				$mysqli->query("UPDATE `stats` SET `Rating, %`=$rating WHERE `Rank`='$i'");
				$i++;
			}
		}
		$_SESSION["score1_$game"]="";// 161-164: очищаем использованные сессии
		$_SESSION["score2_$game"]="";
		$_SESSION["stake_$game"]="";
		$_SESSION["move_$game"]="";
		$_SESSION["winner$game"]=$winner;// записываем в сессию победителя
		$mysqli->close();// закрываем БД
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?="Game: ".$p1." vs ".$p2;  ?></title>
</head>
<body>
	<form align="center" name="menu" method="post" action="">
		<button type="submit" name="menu">Main menu</button>
		<button type="submit" name="surrend" value="<?=$Mylogin ?>">Surrend</button>
	</form>
	<br>
	<br>
	<br>
	<br>
	<br>
	<div align="center">
		<h1><?=$p2.'&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp'.$_SESSION["score2_$game"];  ?></h1>
	</div>
	<br>
	<br>
	<br>
	<div align="center">
		<h2>Random: <?=$random;  ?></h2>
		<h2>Points at stake: <?=$GLOBALS["stake"];  ?></h2>
		<h2>Now moving: <?=$_SESSION["move_$game"]; ?></h2>
	</div>
	<br>
	<br>
	<br>
	<div align="center">
		<h1><?=$p1.'&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp'.$_SESSION["score1_$game"]; ?></h1>	
	</div>
	<br>
	<br>
	<br>
	<form align="center" name="actions" method="post" action="">
		<button type="submit" name="throw" value="<?=$Mylogin ?>">Throw</button>
		<button type="submit" name="pass" value="<?=$Mylogin ?>">Pass</button>
	</form>
	<script type="text/javascript">
		
	</script>
</body>
</html>






				
				