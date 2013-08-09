<?
	include('dbauth.php');
	header('Content-Type: image/png');
	$run = runSql("select `data` from `avatar` where `id` = {$_GET['id']}", "s");
	$row = mysql_fetch_assoc($run);
	echo base64_decode($row['data']);
?>
