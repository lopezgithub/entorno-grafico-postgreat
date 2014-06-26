<?php
	session_start();
	
	if($_SESSION['database']=='')
		$_SESSION['database']='postgres';
		
	$DB_name=$_SESSION['database'];
	$user=$_SESSION['user'];
	$pass=$_SESSION['pass'];
	$conec = "host=localhost port=5432 dbname=$DB_name user=$user password=$pass";
	$conec = pg_connect($conec)or die ("No logro conectarme a postgresql :´(<br /><a href='logout.php'>Inicio</a>");	
	
?>