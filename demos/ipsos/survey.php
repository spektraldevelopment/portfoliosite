<?
include ('admin/dbauth.php');

$run = runSql("select * from `played` where `id` > 198752");
while ($row = mysql_fetch_assoc($run)){
	$run2 = runSql("select `id` from `survey` where `uid` = '{$row['uid']}' and `sid` = '{$row['survey']}'");
	if (mysql_num_rows($run2)){ // increment plays for this user/survey
		$run3 = runSql("update `survey` set plays = plays + 1 where `uid` = '{$row['uid']}' and `sid` = '{$row['survey']}';");
	}else{ // instert a new record. User has never played this survey
		$run3 = runSql("insert into `survey` values(NULL, '{$row['uid']}', '{$row['survey']}', 1);");
	}
	echo $row['id'] . "<br />";
}

?>
