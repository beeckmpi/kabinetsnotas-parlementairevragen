<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	if ( ! function_exists('dompdf'))
	{	
		function pdf_create($html, $filename='', $stream=TRUE, $size = 'A4', $orientation = 'portrait') 
		{
		    require_once("dompdf/dompdf_config.inc.php");
		    
		    $dompdf = new DOMPDF();
		    $dompdf->load_html($html);
			$dompdf->set_paper($size, $orientation);
		    $dompdf->render();
		    if ($stream) {
		        $dompdf->stream($filename.".pdf");
		    } else {
		        return $dompdf->output();
		    }
		}
	}
 