<?php
	$conec = pg_connect("host=localhost port=5432 dbname='hg_comalcalco' user=yaroslav password=1234");	
	
	//esta consulta tra todas las bases de datos que hay, el detalle es que solo lo he probado desde el phppgadmin ya que solo funciona cuando aun no seleccionas una BD, estoy investigando como conectarme solo al servidor para poder ejecutarla ya que la conexion comun me dice que debo seleccionar una BD
	
	//$sql="
	//SELECT datname AS datname, pg_encoding_to_char(encoding) AS datencoding
	//FROM pg_database";
	//pg_query($conec,$sql) or die ("Error: ".pg_last_error($conec));
	
	
	
	

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
<script type="text/javascript" language="javascript" src="jquery/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" language="javascript" src="jquery/js/jquery-ui-1.8.21.custom.min.js"></script>
<script type="text/javascript" language="javascript">
	$(document).ready(function()
	{
		//$("#tabla").draggable();
		//$("#tabla").find('table').draggable(
		$('#contenedor').find('.tabla_mr').draggable(
		{
			appendTo:'body'
		});	
	});
</script>
<link href="jquery/css/redmond/jquery-ui-1.8.21.custom.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.tabla_mr :hover
{
	cursor:move;
}
.tabla_mr
{
	position:relative;
	box-shadow: 7px 2px 5px #666; 
	border:solid 1px #000000;
	border-radius:5px 5px 5px 5px;
	background-color:#EAEEFF;
	width:250px;
}
table
{
	font-size:12px;
	text-align:left;
}
</style>
</head>

<body>
	<?php
		$sql="SELECT schemaname, tablename, tableowner, tablespace, hasindexes, hasrules, hastriggers
		FROM pg_tables
		WHERE schemaname='public'
		";
		$rows=pg_query($conec,$sql) or die ("Error: ".pg_last_error($conec));
	
        echo "<div id='contenedor'>";
    
		while($row=pg_fetch_array($rows))
		{
			$tablename=$row['tablename'];
			?>
            <div class="tabla_mr">
            	<div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix" id="acceso">
					<?php echo $tablename ?>
            	</div>
                <?php
				/*LA consulta que comento aki abajo trae la informacion de los campos de las tablas solo que por alguna razon solo corre en el phpPgAdmin, ya que aki la ejecuto y no me trae resultados, ni errores, estoy trabajando en eso*/
					$sql2="
					/*SELECT pg_tables.tablename, pg_attribute.attname AS field, format_type(pg_attribute.atttypid, NULL) AS \"type\", 
					pg_attribute.atttypmod AS len, 
					(SELECT col_description(pg_attribute.attrelid, pg_attribute.attnum)) AS comment, 
					CASE pg_attribute.attnotnull 
					WHEN false THEN 1 ELSE 0 END AS \"notnull\", 
					pg_constraint.conname AS \"key\", pc2.conname AS ckey, 
					(SELECT pg_attrdef.adsrc FROM pg_attrdef WHERE pg_attrdef.adrelid = pg_class.oid AND pg_attrdef.adnum = pg_attribute.attnum) AS def 
					FROM pg_tables, pg_class 
					JOIN pg_attribute ON pg_class.oid = pg_attribute.attrelid 
					AND pg_attribute.attnum > 0 
					LEFT JOIN pg_constraint ON pg_constraint.contype = 'p'::\"char\" 
					AND pg_constraint.conrelid = pg_class.oid 
					AND (pg_attribute.attnum = ANY (pg_constraint.conkey)) 
					LEFT JOIN pg_constraint AS pc2 ON pc2.contype = 'f'::\"char\" 
					AND pc2.conrelid = pg_class.oid 
					AND (pg_attribute.attnum = ANY (pc2.conkey)) 
					WHERE pg_class.relname = pg_tables.tablename 
					AND pg_tables.tableowner = \"current_user\"() 
					AND pg_attribute.atttypid <> 0::oid AND tablename='$tablename' 
					ORDER BY field ASC*/
					
					SELECT column_name, udt_name, data_type, character_maximum_length
					FROM information_schema.columns 
					WHERE table_name = '$tablename';
					";
					$rows2=pg_query($conec, $sql2) or die ("Error al sacar las columnas de las tablas: ".pg_last_error($conec));
					$n=pg_num_rows($rows2);
					
					//echo $n."<br/>".$sql2;
					
					echo"
						<table border='0'>
							<tr>
								<td>
								</td>
								<td>
									Columna
								</td>
								<td>
									Tipo de dato
								</td>
							<tr>";
					while($row2=pg_fetch_assoc($rows2))
					{
						$column_name=$row2['column_name'];
						$udt_name=$row2['udt_name'];
						$data_type=$row2['data_type'];
						$character_maximum_length=$row2['character_maximum_length'];
						echo "
							<tr>
								<td>
									-
								</td>
								<td>
									$column_name
								</td>
								<td>
									$data_type($character_maximum_length)
								</td>
							</tr>";
					}
					echo"</table>";
				?>
                
            </div>
            <?php	
		}		
	?>
    </div>
</body>
</html>