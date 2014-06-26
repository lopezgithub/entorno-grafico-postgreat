<?php
	session_start();
	if(!empty($_SESSION['user']))
		header("location:modelo_E-R.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login</title>
<link href="css/login.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" language="javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript"> 
	function valida_user()
	{
		var user=$("#user").attr("value");
		var pass=$("#pass").attr("value");
        $.ajax(
		{	
			type: "POST",
			url: "funciones.php",
			data: "tran=4&user="+user+"&pass="+pass,
			success: function(msg)
			{
				if(msg!="")
					$('#result').html("Usuario o contraseña invalido");
				else
					document.location.href="login.php";	
			}
		});
	}
</script>
</head>

<body>
<div>
	Login to Posgre SQL
</div>
<br />

	<table>
    	<tr>
        	<td>
				Username 
            </td>
            <td>
            	<input type="text" name="user" id="user" required/>
    		</td>
        </tr>
        <tr>
        	<td>
            	Password 
            </td>
            <td>
            	<input type="password" name="pass" id="pass" required />
            </td>
        </tr>
    </table>
    <input type="button" value="Login" onclick="valida_user();" />	
    <div id="result"></div>
</body>
</html>