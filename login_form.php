<?php 
	$entered_login=null;
	$enterd_password=null;
	if (isset($_POST["enter"])) 
	{
		$good=false;
		$entered_login=htmlspecialchars($_POST["login"]);
		$enterd_password=htmlspecialchars($_POST["password"]);
		function generateToken($length)
			{
				$chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ0123456789';
				$numChars = strlen($chars);
				$string = '';
				for ($i = 0; $i < $length; $i++) 
					{
						$string .= substr($chars, rand(0, $numChars-1), 1);
					}
				return $string;
			}
		$mysqli= new mysqli("localhost","root","","pig");
		$mysqli->query("SET NAMES 'utf8'");
		if ($mysqli->connect_errno) 
		{
    		printf("Соединение не удалось: %s\n", $mysqli->connect_error);
    		$mysqli->close();
    		exit();
		}
		else
		{
			$result_same=$mysqli->query("SELECT `id`,`login`,`password` FROM `users`");
			while ($row=$result_same->fetch_assoc()) 
			{
				if ($row["login"]===$entered_login && password_verify($enterd_password, $row["password"])) 
				{
					$id=$row["id"];
					
					$good=true;
				}
			}
			if ($good) 
			{
				$token=generateToken(256);
				$mysqli->query("UPDATE `users` SET `token` = '$token' WHERE `users`.`login` = '$entered_login'");
				$mysqli->close();
				setcookie("name$id", $entered_login);
				setcookie("token$id", $token);
				header("Location: index.php?id=$id");
			}
			else
			{
				echo "<script type=\"text/javascript\">alert(\"Incorrect login or password\")</script>";
			}
		}
		
	}
	elseif (isset($_POST["registration"])) 
	{
		
		
		header("Location: regist.php");
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Start page(login_form.php)</title>
	<noscript>Please, activate JavaScript in your browser</noscript>
</head>
<body>
	<table border="1" cellspacing="0" align="center" width="20%" >
		<form name="check_login"  method="post" align="center"  action="login_form.php" id="form_1">
		<tr>
			<td colspan="2" align="center">	
				<p>
					<label>Welcome!</label>
				</p>
				<label>Login: </label></br>
				<input type="text" name="login" placeholder="login" value="<?= $entered_login  ?>" required /></br>
				<label>Password:</label></br>
				<input type="password" name="password" placeholder="password..." value="<?=$enterd_password  ?>" required></br>
			</td>
		</tr>
		<tr>
			<td align="center">
			<button type="submit" name="enter">Log in</button>	
		</form>	
			</td>
			<td>
				<form name="registration" method="post" action="login_form.php" align="center">
					<input type="submit" name="registration" value="registration">
				</form>
			</td>
		</tr>	
	</table>
</body>
</html>
