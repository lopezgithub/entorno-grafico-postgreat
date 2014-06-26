<?php
	session_start();
	if(!empty($_POST['database']))
	{
		$_SESSION['database']=$_POST['database'];
	}
	include "validacion.php";
	$esquema='public';
	include "conexion.php";
	/***********************funciones********************************/
	if(!empty($_POST['tran']))
	{
		$tran=$_POST['tran'];
		if($tran==1)
		{
			$x=$_POST['x'];
			$y=$_POST['y'];
			$z=trim($_POST['z']);
			
			$sql="SELECT id_tabla
			FROM modelo_entidad_relacion
			where tabla='$z'";
			$rows=pg_query($conec, $sql);
			$n=pg_num_rows($rows);
			if($n>0)
			{
				$id_tabla=pg_result($rows, 0, 'id_tabla');
				$sql="UPDATE modelo_entidad_relacion SET posicion_x=$x, posicion_y=$y
				WHERE id_tabla=$id_tabla;";
				pg_query($conec, $sql) or die ("Error al actualizar: ".pg_last_error($conec));
			}
			else
			{
				$sql="INSERT INTO modelo_entidad_relacion (tabla, posicion_x, posicion_y) VALUES('$z', $x, $y);";
				pg_query($conec, $sql) or die ("Error al insertar: ".pg_last_error($conec));
			}
		}
		
		exit();
	}
	/****************************************************************/	
	/*crear la tabla modelo entidad relacion, si aun no existe*/
	$sql="SELECT schemaname, tablename, tableowner, tablespace, hasindexes, hasrules, hastriggers
	FROM pg_tables
	WHERE tablename='modelo_entidad_relacion'";
	$rows=pg_query($conec, $sql) or die ("Error al consultar las tablas");
	$n=pg_num_rows($rows);
	if($n==0)
	{
		$sql="CREATE TABLE Modelo_Entidad_Relacion (
		id_tabla SERIAL,
		tabla VARCHAR(80),
		posicion_x INTEGER,
		posicion_y INTEGER,
		CONSTRAINT pk_film PRIMARY KEY (id_tabla)		
		);";
		pg_query($sql) or die ("Error al insertar la nueva tabla: ".pg_last_error());
	}
		$sql="SELECT schemaname, tablename, tableowner, tablespace, hasindexes, hasrules, hastriggers
		FROM pg_tables
		WHERE schemaname='$esquema'
		ORDER BY tablename DESC
		";
		$rows=pg_query($conec,$sql) or die ("Error: ".pg_last_error($conec));
    
		while($row=pg_fetch_array($rows))
		{
			$tablename=$row['tablename'];
			$sql3="SELECT id_tabla, tabla, posicion_x, posicion_y
			FROM modelo_entidad_relacion
			WHERE tabla='$tablename'";
			$rows3=pg_query($conec,$sql3) or die("Error al consultar las posiciones: ".pg_last_error($conec));
			$row3=pg_fetch_assoc($rows3);
			$posicion_x=$row3['posicion_x'];
			if($posicion_x=='')
			{
				$posicion_x=0;
			}
			$posicion_y=$row3['posicion_y'];
			if($posicion_y=='')
			{
				$posicion_y=0;
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
	WHERE pg_attrdef.adrelid = pg_class.oid AND pg_attrdef.adnum = pg_attribute.attnum) AS def 
	FROM pg_tables, pg_class 
	JOIN pg_attribute ON pg_class.oid = pg_attribute.attrelid AND pg_attribute.attnum > 0 
	LEFT JOIN pg_constraint ON pg_constraint.contype = \'p\'::"char" AND pg_constraint.conrelid = pg_class.oid AND (pg_attribute.attnum = ANY (pg_constraint.conkey)) 
	LEFT JOIN pg_constraint AS pc2 ON pc2.contype = \'f\'::"char" AND pc2.conrelid = pg_class.oid AND (pg_attribute.attnum = ANY (pc2.conkey)) 
	
	WHERE pg_class.relname = pg_tables.tablename 
	/*AND pg_tables.tableowner = "current_user"()*/ 
	AND pg_attribute.atttypid <> 0::oid AND tablename=\''.$tablename.'\' 
	';
					$rows2=pg_query($conec, $sql2) or die ("Error al sacar las columnas de las tablas: ".pg_last_error($conec));
					$n=pg_num_rows($rows2);
					
					//echo $n."<br/>".$sql2;
					
					
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
							$key='<span class="ui-icon ui-icon-key" title="Primary key"></span>';
							$id_tabla_= "id='$id_tabla'";
						}
						if($ckey!="")
						{
							$ckey='FK';
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
</body>
</html>