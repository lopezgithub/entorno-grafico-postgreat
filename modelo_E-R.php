<?php
	session_start();
	include "validacion.php";
	include "conexion.php";
	include "base_datos.php";	
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>DBD</title>
<script type="text/javascript" language="javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" language="javascript" src="js/jquery-ui-1.8.21.custom.min.js"></script>
<script type="text/javascript" language="javascript" src="js/base.js"></script>


<plantilla id='plantilla'>
	<link href="css/<?php echo $_SESSION['tema']; ?>/base.css" rel="stylesheet" type="text/css" />
    <link href="css/<?php echo $_SESSION['tema']; ?>/jquery-ui-1.8.21.custom.css" rel="stylesheet" type="text/css" />
</plantilla>

<plantilla id='plantilla2'>
</plantilla>

</head>
<body>
<div id="barra">
    
    <?php
		//TRAEMOS LA LISTA DE LAS BASES DE DATOS Y SU CODIFICACION
		$sql="SELECT datname AS datname, pg_encoding_to_char(encoding) AS datencoding
		FROM pg_database;";
		$rows=pg_query($conec, $sql) or die ("Error al extraer las bases de datos: ".pg_last_error($conec));
		$select_database='<option value="">-Seleccione DataBase-</option>';
		while($row=pg_fetch_array($rows))
		{
			$datname=$row['datname'];
			$datencoding=$row['datencoding'];
			if($datname==$_SESSION['database'])
				$select_database.="<option value='$datname' selected>$datname($datencoding)</option>";
			else
				$select_database.="<option value='$datname'>$datname($datencoding)</option>";
				
		}
		
		//TEMAS
		$temas= array('default','blue', 'orange', 'simple');
		foreach($temas as $tema)
		{
			if($tema==$_SESSION['tema'])
				$select_temas.="<option value='$tema' selected>$tema</option>";
			else
				$select_temas.="<option value='$tema'>$tema</option>";
		}
		
		//ESQUEMAS
		$sql="SELECT schemaname/*, tablename, tableowner*/
		FROM pg_tables
		WHERE tableowner <> 'postgres'
		GROUP BY schemaname
		ORDER BY schemaname DESC";
		$rows=pg_query($conec, $sql) or die ("Error al extraer las bases de datos: ".pg_last_error($conec));
		$select_esquema="";
		while($row=pg_fetch_array($rows))
		{
			$esquema=$row['schemaname'];
			if($esquema==$_SESSION['esquema'])
				$select_esquema.="<option value='$esquema' selected>$esquema</option>";
			else
				$select_esquema.="<option value='$esquema'>$esquema</option>";	
		}
		
	?>
        <select id="database" name="database">
            <?php echo $select_database; ?>
        </select>
        
        <span id="carga_esquemas">    
        <select id="esquema" name="esquema">
            <?php echo $select_esquema; ?>
        </select>
        </span>
        
        <select id="tema" name="tema">
            <?php echo $select_temas;?>
        </select>
        <input type="button" id="actualizar" value="Actualizar" />
         
         Modelo entidad relacion Postgresql:..
         
         <a href="logout.php">
         	<span class="ui-icon ui-icon-power" style='float:right' title="logout">
            </span>
         </a>
        
</div>
<input type="hidden" id="tablas_acceso" name="tablas_acceso" />
<canvas id="myCanvas" width="1800" height="1200">
</canvas>
	<?php
		$_SESSION['esquema']='public';
		$sql="SELECT schemaname, tablename, tableowner, tablespace, hasindexes, hasrules, hastriggers
		FROM pg_tables
		WHERE schemaname='".$_SESSION['esquema']."'
		ORDER BY tablename DESC
		";
		$rows=pg_query($conec,$sql) or die ("Error: ".pg_last_error($conec));
	
        echo "<div id='contenedor'>";
    
		while($row=pg_fetch_array($rows))
		{
			$tablename=$row['tablename'];
			$sql3="SELECT id_tabla, tabla, posicion_x, posicion_y
			FROM databasedesigner.modelo_entidad_relacion
			WHERE tabla='$tablename'";
			$rows3=pg_query($conec,$sql3) or die("Error al consultar las posiciones: $sql3 <br/>$conec<br/>".pg_last_error($conec));
			$row3=pg_fetch_assoc($rows3);
			$posicion_x=$row3['posicion_x'];
			if($posicion_x=='')
			{
				$posicion_x=20;
			}
			$posicion_y=$row3['posicion_y'];
			if($posicion_y=='')
			{
				$posicion_y=80;
			}
					
			 echo "<div class='tabla_mr' style='left: $posicion_x"."px"."; top: $posicion_y"."px".";'>";
			?>
            	<div id="acceso">
					
                </div>
                <?php
				
				echo"
						<table border='0' id='$tablename' width='100%'>
						<tr>
						<th colspan='3'>
						<span style=\"float:left\" class=\"ui-icon ui-icon-gear\" title='Herramientas' ></span>
						<span style=\"float:right\" class=\"ui-icon ui-icon-newwin\" title='Explandir' ></span>
						<span style=\"float:right\" class=\"ui-icon ui-icon-minusthick\" title='Compactar' ></span>
						<span id='acceso'>$tablename</span>
						<th>
						</tr>";
					$sql2='
					SELECT pg_tables.tablename, pg_attribute.attname AS column_name, format_type(pg_attribute.atttypid, NULL) AS "data_type", pg_attribute.attrelid AS id_tabla,
					pg_attribute.atttypmod AS character_maximum_length, 
					
					(SELECT col_description(pg_attribute.attrelid, pg_attribute.attnum)) AS comment, 
					CASE pg_attribute.attnotnull 
						WHEN false THEN 1 
						ELSE 0 END AS "notnull", 
					pg_constraint.conname AS "key", pc2.conname AS ckey, pc2.confrelid AS id_tabla_ext,
					(SELECT pg_attrdef.adsrc 
					FROM pg_attrdef 
					WHERE pg_attrdef.adrelid = pg_class.oid 
					AND pg_attrdef.adnum = pg_attribute.attnum) AS def 
					FROM pg_tables, pg_class 
					JOIN pg_attribute ON pg_class.oid = pg_attribute.attrelid 
					AND pg_attribute.attnum > 0 
					LEFT JOIN pg_constraint ON pg_constraint.contype = \'p\'::"char" 
					AND pg_constraint.conrelid = pg_class.oid 
					AND (pg_attribute.attnum = ANY (pg_constraint.conkey)) 
					LEFT JOIN pg_constraint AS pc2 ON pc2.contype = \'f\'::"char" 
					AND pc2.conrelid = pg_class.oid 
					AND (pg_attribute.attnum = ANY (pc2.conkey)) 
					
					WHERE pg_class.relname = pg_tables.tablename 
					/*AND pg_tables.tableowner = "current_user"()*/ 
					AND pg_attribute.atttypid <> 0::oid AND tablename=\''.$tablename.'\' 
					';
					$rows2=pg_query($conec, $sql2) or die ("Error al sacar las columnas de las tablas: ".pg_last_error($conec));
					$n=pg_num_rows($rows2);
					
					//echo $n."$conec_str<br/>".$sql2;
					
					
					$aux=0;
					while($row2=pg_fetch_assoc($rows2))
					{
						$aux++;
						$column_name=$row2['column_name'];
						$udt_name=$row2['udt_name'];
						$data_type=$row2['data_type'];
						$key=$row2['key'];
						$ckey=$row2['ckey'];
						$id_tabla_="";
						$id_tabla_ext_="";
						$id_tabla=$row2['id_tabla'];
						$id_tabla_ext=$row2['id_tabla_ext'];
						if($key!="")
						{
							//<span class="ui-icon ui-icon-key" title="Primary key"></span>
							$key='<span style="color:brown">PK</span>';
							$id_tabla_= "id='$id_tabla'";
						}
						if($ckey!="")
						{
							$ckey='<span style="color:brown">FK</span>';
							$aux_complete=$tablename.$aux;
							$id_tabla_ext_= "id='$aux_complete' class='$id_tabla_ext'";
							?>
                            <script type="text/javascript">
								document.getElementById("tablas_acceso").value=document.getElementById("tablas_acceso").value+',<?php echo $aux_complete; ?>';
							</script>
							<?php
							
						}
						
						$character_maximum_length=($row2['character_maximum_length'])-4;
						switch($character_maximum_length)
						{
							case -5:
								$character_maximum_length="";
							break;
							case -3:
								$character_maximum_length=1;
							break;
						}
						echo "
							<tr $id_tabla_ $id_tabla_ext_>
								<td>
									<div class='primarykey'>$id_tabla_ext</div>
									$key $ckey
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