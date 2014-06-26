<?php
	session_start();
	include"conexion.php";
	/*crear la tabla modelo entidad relacion, si aun no existe*/
	//echo "<br /><br /><br />-";
	if($_SESSION['database']!='postgres')
	{		
		$sql="SELECT schemaname, tablename, tableowner, tablespace, hasindexes, hasrules, hastriggers
		FROM pg_tables
		WHERE tablename='modelo_entidad_relacion' AND schemaname='databasedesigner'";
		$rows=pg_query($conec, $sql) or die ("Error al consultar las tablas");
		$n=pg_num_rows($rows);
		if($n==0)
		{
			$sql="
			BEGIN;
			CREATE SCHEMA \"databasedesigner\" AUTHORIZATION \"postgres\";
			CREATE TABLE databasedesigner.modelo_entidad_relacion (
			id_tabla SERIAL,
			tabla VARCHAR(80),
			posicion_x INTEGER,
			posicion_y INTEGER,
			CONSTRAINT pk_film PRIMARY KEY (id_tabla)		
			);
			COMMIT;";
			pg_query($sql) or die ("Error al insertar la nueva tabla: ".pg_last_error());
		}
	}
?>