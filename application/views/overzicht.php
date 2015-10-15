<?php if (!isset($filter_view)){
			$filter_view = "normal";
}?>
<div style="display: <?php echo $filter_view; ?>"id="table">
	<?php 
		if (isset($content)){		
			echo $content;
		} else {
			echo 'Er zijn momenteel geen dossiers. <p><a href="">Klik hier om een dossier toe voegen</a></p>';
		}
	?>
</div>