<?php 
	$login=$_POST["stats"];// получаем наш логин
	$id=$_POST["id"];// получаем наш id
	function printMe($login,$wins,$total,$rating)// 4-20: функция вывода на экран строки с нашей статистикой (Значения выделяются жирным шрифтом)
	{
		print '<tr>';
			print '<td>';
				print '<b>'.$login.'</b>';	
			print '</td>';
			print '<td>';
				print '<b>'.$wins.'</b>';	
			print '</td>';
			print '<td>';
				print '<b>'.$total.'</b>';	
			print '</td>';
			print '<td>';
				print '<b>'.$rating.'</b>';	
			print '</td>';
		print '</tr>';
	}

	function printGames($login)// функция вывода на экран строк таблицы с данными статистики
	{
		$mysqli= new mysqli("localhost","root","","pig");// открываем БД
		$mysqli->query("SET NAMES 'utf8'");// устанавливаем кодировку
		if ($mysqli->connect_errno)// 26-30: проверка на ошибку при открытии БД. Если произошла ошибка, то выходим
		{
	    	printf("Соединение не удалось: %s\n", $mysqli->connect_error);
	    	exit();
		}
		else
		{	
			$values=$mysqli->query("SELECT * FROM `stats`");// выбираем все значения из таблицы статистики
			while ($row=$values->fetch_assoc())// заносим их в ассоциативный массив
			{
				if ($row["Name"]===$login)// если текущая строка есть наша статистика то printMe()
					printMe($row["Name"],$row["Wins"],$row["Total games"],$row["Rating, %"]);
				else
				{
					print '<tr>';// выводим новую строку таблицы
					print '<td>';// вывод ячейки с логином
						echo $row["Name"];	
					print '</td>';
					print '<td>';// вывод ячейки с количеством побед
						echo $row["Wins"];	
					print '</td>';
					print '<td>';// вывод ячейки с общим количеством игр
						echo $row["Total games"];	
					print '</td>';
					print '<td>';// вывод ячейки с рейтингом
						echo $row["Rating, %"];	
					print '</td>';
				print '</tr>';
				}
			}	
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Stats</title>
</head>
<body>
	<form align="center" method="get" action="index.php">
		<button name="id" type="submit" value="<?=$id ?>">Home</button>
	</form>
	<table border="1" cellspacing="0" align="center">
		<tr>
			<td>Login:</td>
			<td>Wins</td>
			<td>Total games</td>
			<td>Rating, %</td>
		</tr>
		<?php  
		// со второй строки таблицы начинаем выводить значения 
		printGames($login);
		?>
	</table>
</body>
</html>