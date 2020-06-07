<?php

//Устанавливаем доступы к базе данных:
$host = 'localhost';
$user = 'root';
$password = '';
$db_name = 'pig_bd';
$link = mysqli_connect($host, $user, $password, $db_name);
mysqli_query($link, "SET NAMES utf8");
  
