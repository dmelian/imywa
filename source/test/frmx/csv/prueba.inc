<html>
<body>
<form method="post">
<?php echo getsessionstamp();?>

accion <select name="action">
	<option value="csv1">Crear el csv parrafo</option>
	<option value="csv2">Crear el csv lista</option>
	<option value="close">Salir</option>
</select>
<input type="submit">

</form>
</body>
</html> 
