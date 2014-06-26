<?php
	session_start();
	
	if(!empty($_SESSION['user']) && !empty($_SESSION['pass']))
	{
		if(empty($_SESSION['database']))//esta vacia la session datbase
		{
			$_SESSION['database']='postgres';
		}
		if(empty($_SESSION['tema']))
		{
			$_SESSION['tema']='default';
		}
		if(empty($_SESSION['esquema']))
		{
			$_SESSION['esquema']='public';
		}
	}
	else
	{
		header("location:login.php");
	}
?>