<?php 		
	if (isset($_POST["check_registration"])) 
	{

		$same=false;
		$r_login=htmlspecialchars($_POST["r_login"]);
		$reg_password=htmlspecialchars($_POST["reg_password"]);
		$rep_password=htmlspecialchars($_POST["rep_password"]);
		if($reg_password != $rep_password)
		{
			echo "<script type=\"text/javascript\">alert(\"Passwords are not same\")</script>";
		}
		else
		{
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
				$result_same=$mysqli->query("SELECT `login` FROM `users`");
				while ($row=$result_same->fetch_assoc()) 
				{
					if ($row["login"]===$r_login) 
					{
						$same=true;
					}
				}
				if ($same) 
				{
					$mysqli->close();
					echo "<script type=\"text/javascript\">alert(\"This login already exist\")</script>";
				}
				else
				{
					$pop=false;
					$result_pop=$mysqli->query("SELECT * FROM `popular_passwords`");
					while($row=$result_pop->fetch_assoc())
					{
						if ($reg_password===$row["password"]) 
						{
							$pop=true;
						}
					}	
					if ($pop) 
					{
						$mysqli->close();
						echo "<script type=\"text/javascript\">alert(\"Your password is too popular.It can damage your safety.We reccomend you to change your password\")</script>";
					}
					else
					{
						function generatePassword($length)
						{
							$chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
							$numChars = strlen($chars);
							$string = '';
							for ($i = 0; $i < $length; $i++) 
							{
								$string .= substr($chars, rand(0, $numChars-1), 1);
							}
							return $string;
						}
						$token=generatePassword(256);
						$mysqli->query("INSERT INTO `pig`.`users` (`login`,`password`,`token`) VALUES ('".$r_login."','".password_hash($reg_password, PASSWORD_BCRYPT)."','".$token."')");
						$mysqli->query("INSERT INTO `stats` (`Rank`, `Name`, `Wins`, `Total games`, `Rating, %`) VALUES (NULL, '".$r_login."', '0', '0', '0')");
						$row=$mysqli->query("SELECT `id` FROM `users` WHERE `login` = '$r_login'");
						$ID=$row->fetch_assoc();
						$id=$ID["id"];
						setcookie("name$id", $r_login);
						setcookie("token$id", $token);
						$mysqli->close();
						header("Location: index.php?id=$id");
					}
				}		
			}
		}		
	}
	else
	{
		$r_login=null;
		$reg_password=null;
		$rep_password=null;
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<noscript>Please, activate JavaScript in your browser</noscript>
	<meta charset="utf-8">
	<title>Registration(regist.php)</title>
</head>
<body>
	<table border="1" cellspacing="0" align="center" width="20%" >
		<tr>
			<form name="check_registration" method="post" action="regist.php" align="center" id="form_3">
			<td align="center">	
				<p>
					<label>Registration:</label>
				</p>
				<label>login:</label></br>
				<input type="text" name="r_login" value="<?=$r_login ?>" required ></br>
				<label>password:</label></br>
				<input type="password" name="reg_password" value="<?=$reg_password ?>" required></br>
				<label>repeat password:</label></br>
				<input type="password" name="rep_password"  value="<?=$rep_password ?>" required></br>
			</td>
		</tr>
		<tr>
			<td align="center">
				<button type="submit" name="check_registration">registration</button> 
			</td>
		</form>	
		</tr>
	</table>
</body>
</html>
