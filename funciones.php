<?php
	session_start();
	if(!empty($_POST['tran']))
	{
		$tran=$_POST['tran'];
	
		if($tran==1)
		{
			include "conexion.php";
			$x=$_POST['x'];
			$y=$_POST['y'];
			$z=trim($_POST['z']);
			
			$sql="SELECT id_tabla
			FROM databasedesigner.modelo_entidad_relacion
			where tabla='$z'";
			$rows=pg_query($conec, $sql);
			$n=pg_num_rows($rows);
			if($n>0)
			{
				$id_tabla=pg_result($rows, 0, 'id_tabla');
				$sql="UPDATE databasedesigner.modelo_entidad_relacion SET posicion_x=$x, posicion_y=$y
				WHERE id_tabla=$id_tabla;";
				pg_query($conec, $sql) or die ("Error al actualizar: ".pg_last_error($conec));
			}
			else
			{
				$sql="INSERT INTO databasedesigner.modelo_entidad_relacion (tabla, posicion_x, posicion_y) VALUES('$z', $x, $y);";
				pg_query($conec, $sql) or die ("Error al insertar: ".pg_last_error($conec));
			}
		}
		
		if($tran==2)
		{
			$_SESSION['database']=$_POST['database'];
			$_SESSION['tema']=$_POST['tema'];
			$_SESSION['esquema']=$_POST['esquema'];
		}
		
		if($tran==3)
		{
			include "conexion.php";
			/*$database='sistel';//$_POST['database'];
			$user='yaroslav';//$_SESSION['user'];
			$pass='1234';//$_SESSION['pass'];*/
			//$conec_str="host=localhost port=5432 dbname=$database user=yaroslav password=1234";
			//$conec_temp = pg_connect($conec_str);
			
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
            <select id="esquema" name="esquema">
            <?php echo $select_esquema; ?>
        	</select>
			<?php
		}
		if($tran==4)
		{
			//login
			if(!empty($_POST['user']) && !empty($_POST['pass']))
			{
				$DB_name='postgres';
				$user=$_POST['user'];
				$pass=$_POST['pass'];
				$conec_str="host=localhost port=5432 dbname=$DB_name user=$user password=$pass";
				pg_connect($conec_str) or die("Error");
				//or die
				$_SESSION['user']=$_POST['user'];	
				$_SESSION['pass']=$_POST['pass'];	
			}
		}
	}//fin del if $tran
?>