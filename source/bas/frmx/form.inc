<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $this->title; ?></title>
	<link href="style/jqueryui/start/jquery-ui-1.9.1.custom.min.css" rel="stylesheet">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js"></script>
	
	<script type="text/javascript" src="script/frmx/lib.js"></script>
	<script type="text/javascript" src="script/frmx/form.js"></script>
	<script type="text/javascript" src="script/frmx/frame.js"></script>
	<script type="text/javascript" src="script/frmx/htmlframe.js"></script>
	<script type="text/javascript" src="script/frmx/paragraphframe.js"></script>
	
	<script type="text/javascript" src="script/frmx/markdownframe.js"></script>
	
<!-- 
	<script type="text/javascript" src="script/frmx/listframe.js"></script>
	<script type="text/javascript" src="script/frmx/cardframe.js"></script>
-->
	
	<link rel="stylesheet" type="text/css" href="style/frmx/form.css">
<!-- 	<link rel="stylesheet" type="text/css" href="style/frmx/htmlframe.css">-->
	<link rel="stylesheet" type="text/css" href="style/frmx/frame.css">
<!-- 
	<link rel="stylesheet" type="text/css" href="style/frmx/paragraphframe.css">
	<link rel="stylesheet" type="text/css" href="style/frmx/listframe.css">
	<link rel="stylesheet" type="text/css" href="style/frmx/cardframe.css">
-->
		
</head>

<body  onload="new frmx_form('default');"> <!-- onload="new xform('default');" -->
<form method="post"><?php echo getsessionstamp(); ?>
<div class="frmx_form">
	<div class="toppanel">
		<div class="titlebar">titlebar</div>
		<div class="navigatebar"><?php echo getnavigatestamp($this); ?></div>
		<div class="toolbar">
		<button type="submit" id="close" name="action" value="salir">Salir</button>
		<button type="submit" id="close" name="action" value="pdf">PDF</button>
		</div>
	</div>
	<div class="midlepanel">
		<div class="dashboard">dashboard</div>
		<div class="framecontainer">
		<?php foreach($this->frames as $frame) $frame->OnPaint(); ?>		
		</div>
	</div>
	<div class="bottompanel">
		<div class="statusbar">statusbar</div>
		<div class="buttonbar">buttonbar</div>
	</div>
</div>
</form>
</body>
</html>
