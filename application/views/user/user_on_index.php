<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to CodeIgniter</title>

	<style type="text/css">

	::selection{ background-color: #E13300; color: white; }
	::moz-selection{ background-color: #E13300; color: white; }
	::webkit-selection{ background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 0 auto;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
		width: 960px;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: transparent;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body{
		margin: 0 15px 0 15px;
		width: 60%
	}
	
	p.footer{
		text-align: right;
		font-size: 11px;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
		clear: both;
	}
	
	#container{
		margin: 10px;
		-webkit-box-shadow: 0 0 8px #D0D0D0;
	}
	
	.label {
		display: block;
	    float: left;
	    margin-right: 10px;
	    padding-top: 5px;
	    text-align: right;
	    width: 170px;
	}
	.info {
		margin-bottom: 5px;
		margin-left: 5px;
		border: 1px solid #666666;
		border-radius: 4px;
		padding: 4px 2px;
		font-size: 15px;
	}
	</style>
	<script src="<?php echo base_url()?>media/modernizr.js"></script>
	<script src="<?php echo base_url()?>media/jquery.min.js"></script>
	<script src="<?php echo base_url()?>media/profile.js"></script>
</head>
<body>

<div id="container">
	<h1>Welcome to <span style="color:red; font-size: 22px; font-weight: bold">[!AlfA!]</span>!</h1><div style="clear:both"></div>
	<div id="body">		
		<div id="login_form"><?php echo $form ?></div>
	</div>
	<p class="footer">Languages: <a href="#">English</a></p>
</div>

</body>
</html>