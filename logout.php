<?php
	session_start();
	session_unset($_SESSION['database']);
	session_unset($_SESSION['user']);
	session_unset($_SESSION['pass']);
	session_unset($_SESSION['tema']);
	header("location:login.php");
?>