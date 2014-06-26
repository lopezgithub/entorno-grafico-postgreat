<?php
	session_start();
	include "validacion.php";
	$esquema='public';
	include "conexion.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
<script type="text/javascript" language="javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery-ui-1.8.21.custom.min.js"></script>
<script type="text/javascript" language="javascript" src="js/base.js"></script>


<plantilla>
	<link href="css/default/base.css" rel="stylesheet" type="text/css" />
    <link href="css/default/jquery-ui-1.8.21.custom.css" rel="stylesheet" type="text/css" />
</plantilla>
</head>
<body>
<div id="barra">
    
    <?php
		$sql="SELECT datname AS datname, pg_encoding_to_char(encoding) AS datencoding
		FROM pg_database;";
		$rows=pg_query($conec, $sql) or die ("Error al extraer las bases de datos: ".pg_last_error($conec));
		$select_database='<option value="">-Seleccione DataBase-</option>';
		while($row=pg_fetch_array($rows))
		{
			$datname=$row['datname'];
			$select_database.="<option value='$datname'>$datname</option>";
		}
	?>
    <select id="database" name="database">
    	<?php echo $select_database; ?>
    </select>
        
    <select name="esquema">
    	<option value="">-Seleccione schema-</option>
    </select>
    
    <select id="tema" name="tema">
    	<option value="default">Default</option>
        <option value="simple">-simple-</option>
        <option value="blue" >-Azul-</option>
    </select>
    Modelo entidad relacion Postgresql
</div>
<input type="hidden" id="tablas_acceso" name="tablas_acceso" />
<canvas id="myCanvas" width="1800" height="1200">
</canvas>
<div id='contenedor'>
<?php
	//include "modeloER.php";
?>
		
</div>
</body>
</html>