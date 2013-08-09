<?php
if ( isset ( $GLOBALS["HTTP_RAW_POST_DATA"] )) {
	include('dbauth.php');
	// get bytearray
	$im = $GLOBALS["HTTP_RAW_POST_DATA"];
	$id = runSql("insert into `avatar` values(NULL, '" . chunk_split(base64_encode($im)) . "');", "i");

	//echo "<a href='png.php?id=$id'>This is your image</a>";
	echo $id;
 
}  else echo 'An error occured.';
?>
