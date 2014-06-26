function cargar_opc(id)
{
	var posicion=$("#"+id).offset();
	var x=posicion.left;
	var y=posicion.top;
	if (confirm("Deseas eliminar la restriccion de "+id+"x="+x+"  y="+y))
		alert("Ok se eliminara");
	else
		alert("Ok No se eliminara");
}

$(document).ready(function()
{
		
	$('#contenedor').find('.tabla_mr').draggable(
	{
		cursor: "crosshair", cursorAt: { }, 
		appendTo:'body'			
	});	
	
	//actualiza span squema
	$("#database").change(function(e) {
		var database=$("#database").attr("value");
		$.ajax(
		{
			type: "POST",
			url: "funciones.php",
			data: "tran=3&database="+database,
			success: function(msg)
			{
				$('#carga_esquemas').html(msg);	
			}
		});			
    });
	
	//Actualiza la pagina
	$("#actualizar").click(
		function()
		{
			var database=$("#database").attr('value');
			var esquema=$("#esquema").attr('value');
			var tema=$("#tema").attr('value');
			$.ajax(
			{
				type: "POST",
				url: "funciones.php",
				data: "tran=2&database="+database+"&esquema="+esquema+"&tema="+tema,
			});
			document.location.href="modelo_E-R.php";	
		}
	);
	
	//seleccion del tema
	$("#tema").change(function(e) 
	{
    	var tema=$("#tema").attr('value');
		
		$("#plantilla").html('<link href="css/'+tema+'/base.css" rel="stylesheet" type="text/css" /><link href="css/'+tema+'/jquery-ui-1.8.21.custom.css" rel="stylesheet" type="text/css" />'); 
		setTimeout(pinta_relacion,500);
    });
	
	//Actualiza la posicion de cada tabla
	$(".tabla_mr").mouseup(function(e) 
	{
		//var X=event.clientX; var Y=event.clientY;
		var posicion=$(this).offset();
		var x=posicion.left;
		var y=posicion.top;
		var z=$(this).find("#acceso").text();
		$(".tabla_mr").css("background-color","000000");
		
		$.ajax(
		{
			type: "POST",
			url: "funciones.php",
			data: "tran=1&x="+x+"&y="+y+"&z="+z,
			//success: function(msg)
			//{
				//$('#coordenadas').html(' ').append('<p>'+msg+'</p>');	
			//}
		});			
		pinta_relacion();	
    });	
	
	
	pinta_relacion();	
	//setTimeout("recarga_pag()",1000)
});
function recarga_pag()
{
	document.location.href="modelo_E-R.php";
}
	

	
	function pinta_relacion()
	{
		document.getElementById("myCanvas").innerHTML='';
		var array_tablas=document.getElementById("tablas_acceso").value;
		var i,x=-1,y=-1;
		var ides2=new Array();
		var ides='';
		for(i=0; i<array_tablas.length; i++)
		{
			if(array_tablas[i]==',' && i!=0)
			{
				y++;
				ides2[y]=ides;
				x=-1;
				ides='';
			}
			else if(i!=0)
			{
				x++;
				ides=ides+array_tablas[i];
			}
		}
		y++;
		ides2[y]=ides;
		
		var canvas = document.getElementById('myCanvas');
		var context = canvas.getContext('2d'); 		
		//tecnica1
		//context.clearRect(0, 0, 1000, 1000);
		
		//tecnica 2
		//context.save();
		//context.fillStyle = "#FFF";
		//context.fillRect(x, y, 1000, 1000);
		//context.restore();
		
		//tecnica 3
		canvas.width = canvas.width;
		
		//context.fillStyle();
	
		//context.clearRect(0,0,1800,1200);
		var xdiv,x1,x2,x3,y1,y2="",sentido, sentido2, flecha_primary;
		var ancho1, ancho2;
		for(i=0;i<=y;i++)
		{
			//positon=padre offset=documento
			//$('#'+ides2[i]).text();
			
			var posicion = $('#'+ides2[i]).offset();
			var primary=$('#'+ides2[i]).find('.primarykey').text();
			
			/***********agregamos clases fk pk***********/
			$('#'+ides2[i]).addClass("fk");
			 $('#'+primary).addClass("pk");
			/********************************************/
			x1=posicion.left;
			y1=posicion.top;
			var posicion = $('#'+primary).offset();
			x2=posicion.left;
			y2=posicion.top;
			
			/********elegimos de que lado  de la tabla se pinta la linea*******/
			ancho1=$('#'+ides2[i]).width();
			ancho2=$('#'+primary).width();
			
			x3=15;//linea recta que une tabla contabla
			xdiv=0;
			if(x1>x2)
			{
				x1=x1-10;
				x2=x2+ancho2-7;
				flecha_primary=+5;
				sentido=false;
				sentido2=true;
			}
			else
			{
				x2=x2-10;
				x1=x1+ancho1-7;
				xdiv=9;
				flecha_primary=-5
				x3=x3*-1;
				sentido=true;
				sentido2=false;
			}
			/******************************************************************/	
			//plantillas
			var tema=$("#tema").attr('value');
			//alert(tema);
			switch(tema)
			{
				case 'default':
					color='brown'
				break;
				case 'simple':
					color='black'
				break;
				case 'blue':
					color='blue'
				break;
				case 'orange':
					color='black'
				break;
			}
			//=======stilos===============
			context.fillStyle=color;
			context.strokeStyle=color;
			context.lineWidth='1';
			//============================
			
			context.beginPath();
			context.moveTo(x1,y1);
			context.lineTo(x1-x3,y1);
			context.lineTo(x2+x3,y2);
			context.lineTo(x2,y2);			
			context.lineTo(x2,y2);
			context.stroke();
			
			//arco PK
			var grados=270;
			var grados2=90;//borra desde el punto de inicio
			var radianes=(Math.PI/180)*grados;
			var radianes2=(Math.PI/180)*grados2;
			context.beginPath();
			context.arc(x1,y1,4,radianes2,radianes,sentido);
			context.fill();
			context.closePath();
			context.stroke();
			
			//$("#plantilla2").html('<map><area shape="circle" oncontextmenu="cargar_opc(this.id);return false" coords="30,30,64" id="circulo" href="#"/></map>');
			var texto=$("#plantilla2").html();
			xdiv=xdiv+x1;
			var div='<div id="'+primary+'_div" style="cursor:pointer; position:absolute; width:10px; height:14px; top:'+y1+'px; left:'+xdiv+'px;" onclick="cargar_opc(this.id); return false;"></div>';
			
			$("#plantilla2").html(texto+div);
			
			
			//arco FK
			var grados=270;
			var grados2=90;//borra desde el punto de inicio
			var radianes=(Math.PI/180)*grados;
			var radianes2=(Math.PI/180)*grados2;
			context.beginPath();
			context.arc(x2,y2,1.5,radianes2,radianes,sentido2);
			context.fill();
			context.closePath();
			context.stroke();
			
		}
		//setTimeout("pinta_relacion()",100) 
	}