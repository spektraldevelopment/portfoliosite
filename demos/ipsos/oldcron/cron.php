<?

/*********************************************

Cron job script that takes all finished records
that are over 24 hours old (finished, finished 
with problems, NPN Finished) copies them to 
archive and removes them from the played table

 *********************************************/
include ('admin/dbauth.php');

$dayAgo = (time() - (60*60*24));
echo $daysAgo;
$run = runSql("select * from `played` where `state` in (2, 3, 5) and `datetime` <= " . $dayAgo);

while($row = mysql_fetch_assoc($run)){
	runSql("insert into `archive` values({$row['id']}, '{$row['uid']}', {$row['gid']}, {$row['state']}, {$row['datetime']}, '{$row['survey']}', '{$row['email']}', {$row['country']}, {$row['lang']});");
	echo $row['id'] . "<br />";
}

//$run = runSql("delete from `played` where `state` in (2, 3, 5) and `datetime` <= " . $dayAgo);

?>
