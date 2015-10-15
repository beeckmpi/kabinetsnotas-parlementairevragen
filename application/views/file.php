<?php 

  header("Content-disposition: attachment; filename=".$file['filename']);
  header("Content-type: application/octet-stream");
  echo $file->getBytes(); 

?>