<html>
<body>
<form method="post">
<?php echo getsessionstamp();?>

accion <select name="action">
	<option value="pdf1">Crear el pdf</option>
	<option value="pdf2">probar el pdf_card</option>
	<option value="close">Salir</option>
</select>
<input type="submit">

</form>
</body>
</html>