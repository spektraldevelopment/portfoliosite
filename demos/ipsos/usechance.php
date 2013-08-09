<? include ('admin/dbauth.php');

if($_GET['u'] && $_GET['c'] && $_GET['p']){
	$run = runSql("select `chances` from `chances` where `uid` = '{$_GET['u']}'");
	$row = mysql_fetch_assoc($run);
	$chances = $row['chances'];
	if ($_GET['c'] <= $chances){
		runSql("update `chances` set `chances` = `chances` - {$_GET['c']} where `uid` = '{$_GET['u']}'");
		$run = runSql("select * from `prizechances` where `uid` = '{$_GET['u']}' and  `pid` = {$_GET['p']}");
		if (mysql_num_rows($run)){
			runSql("update `prizechances` set `chances` = `chances` + {$_GET['c']} where `uid` = '{$_GET['u']}' and  `pid` = {$_GET['p']}");
		}else{
			runSql("insert into `prizechances` values(NULL, '{$_GET['u']}', {$_GET['p']}, {$_GET['c']})");
		}
		echo "Successful";
	}else{
		echo "Not enough chances";
	}
}else{
	echo "Missing parameter(s)";
}

?>
