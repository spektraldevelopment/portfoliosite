<?

/*********************************************

Cron job script that takes all finished records
that are over 24 hours old (finished, finished 
with problems, NPN Finished) copies them to 
archive and removes them from the played table

 *********************************************/
include ('dbauth.php');

/**********************************************/

$_filename = date('m-d-Y', time());

$myFile = "records/" . $_filename . ".txt";
$fh = fopen($myFile, 'w') or die("can't open file");

// TEST $now = time() + (60*60*24); // Cron will run at 3am.
$now = time(); // Cron will run at 3am.

$cutoff = $now - (60*60*24*21); // 21 days ago, The cutoff point for started games.
//$start = 0; // 0:00.
$start = $now - (60*60*27); // 0:00.
$end = $now - (60*60*3); // 23:59.
//fwrite($fh,  "$end");

fwrite($fh,  "Ipsos Poll Predictor Report\r\nReport from " . date('l F jS, Y (G:i)', $start) . " to " . date('l F jS, Y (G:i)', $end) . "\r\n\r\n");


$run = runSql("select count(id) as count from `played` where `datetime` <= $end AND `datetime` >= $start");
$row = mysql_fetch_assoc($run);
fwrite($fh,  "Games Total {$row['count']}\r\n");

/* Started */
$run = runSql("select count(id) as count from `played` where `state` in (1, 4) and `datetime` <= $end AND `datetime` >= $start");
$row = mysql_fetch_assoc($run);
fwrite($fh,  "Games Started {$row['count']}\r\n");

$run = runSql("select count(id) as count from `played` where `state` = 1 and `datetime` <= $end AND `datetime` >= $start");
$row = mysql_fetch_assoc($run);
fwrite($fh,  "Normal Games Started {$row['count']}\r\n");

$run = runSql("select count(id) as count from `played` where `state` = 4 and `datetime` <= $end AND `datetime` >= $start");
$row = mysql_fetch_assoc($run);
fwrite($fh,  "NPN Games Started {$row['count']}\r\n");


/* FINISHED */
$run = runSql("select count(id) as count from `played` where `state` in (2, 3, 5) and `datetime` <= $end AND `datetime` >= $start");
$row = mysql_fetch_assoc($run);
fwrite($fh,  "Games Finished {$row['count']}\r\n");

$run = runSql("select count(id) as count from `played` where `state` = 2 and `datetime` <= $end AND `datetime` >= $start");
$row = mysql_fetch_assoc($run);
fwrite($fh,  "Games Finished (No Problems) {$row['count']}\r\n");

$run = runSql("select count(id) as count from `played` where `state` = 3 and `datetime` <= $end AND `datetime` >= $start");
$row = mysql_fetch_assoc($run);
fwrite($fh,  "Games Finished (Given Games) {$row['count']}\r\n");

$run = runSql("select count(id) as count from `played` where `state` = 5 and `datetime` <= $end AND `datetime` >= $start");
$row = mysql_fetch_assoc($run);
fwrite($fh,  "NPN Games Finished {$row['count']}\r\n");


/* NPN */
$run = runSql("select count(id) as count from `played` where `state` in (5, 4) and `datetime` <= $end AND `datetime` >= $start");
$row = mysql_fetch_assoc($run);
fwrite($fh,  "Total NPN Games {$row['count']}\r\n");

/* UNFINISHED GAMES */
fwrite($fh,  "Ipsos Poll Predictor Unfinished Games\r\nReport from " . date('l F jS, Y (G:i)', $start) . " to " . date('l F jS, Y (G:i)', $end) . "\r\n\r\n");
fwrite($fh,  "The following users have started a game and not ended it during the above timespan\r\n");
$run = runSql("select uid, count(uid) as `count` from `played` where `state` in (1, 4) AND `datetime` <= $end AND `datetime` >= $start group by `uid`");
while($row = mysql_fetch_assoc($run)){
	fwrite($fh,  "{$row['uid']}" . ($row['count'] == 1 ? "" : " ({$row['count']})") . "\r\n");
}


/* EXPIRED GAMES */
fwrite($fh,  "Ipsos Poll Predictor Expired Games\r\nReport from " . date('l F jS, Y (G:i)', $start) . " to " . date('l F jS, Y (G:i)', $end) . "\r\n\r\n");
fwrite($fh,  "This is the number of games that have expired and will be removed.\r\n");
$run = runSql("select count(id) as count from `played` where `state` in (1, 4) and `datetime` <= $cutoff");
$row = mysql_fetch_assoc($run);
fwrite($fh,  "{$row['count']}\r\n");
/* DELETE EXPIRED GAMES */
$run = runSql("delete from `played` where `state` in (1, 4) and `datetime` <= $cutoff");


fwrite($fh,  "Ipsos Poll Predictor Archive\r\nReport from " . date('l F jS, Y (G:i)', $start) . " to " . date('l F jS, Y (G:i)', $end) . "\r\n\r\n");
fwrite($fh,  "This is the CSV file of games that have been finished.\r\n");
/* ARCHIVE FINISHED */

$run = runSql("select * from `played` where `state` in (2, 3, 5) and `datetime` <= $end AND `datetime` >= $start");
fwrite($fh,  "id, uid, gid, state, datetime, survey, email, country, lang\r\n\r\n");
while($row = mysql_fetch_assoc($run)){
	fwrite($fh,  "{$row['id']}, {$row['uid']}, {$row['gid']}, {$row['state']}, {$row['datetime']}, {$row['survey']}, {$row['email']}, {$row['country']}, {$row['lang']}\r\n\r\n");
}


/***********************************/

$run = runSql("select * from `played` where `state` in (2, 3, 5) AND `datetime` <= $end AND `datetime` >= $start");
while($row = mysql_fetch_assoc($run)){
	runSql("insert into `archive` values({$row['id']}, '{$row['uid']}', {$row['gid']}, {$row['state']}, {$row['datetime']}, '{$row['survey']}', '{$row['email']}', {$row['country']}, {$row['lang']});");
}


/***********************************/


/* DELETE ARCHIVED GAMES */
$run = runSql("delete from `played` where `state` in (2, 3, 5) and  `datetime` <= $end AND `datetime` >= $start");









/*
$dayAgo = (time() - (60*60*24));
fwrite($fh,  $daysAgo;
$run = runSql("select * from `played` where `state` in (2, 3, 5) and `datetime` <= " . $dayAgo);

while($row = mysql_fetch_assoc($run)){
	runSql("insert into `archive` values({$row['id']}, '{$row['uid']}', {$row['gid']}, {$row['state']}, {$row['datetime']}, '{$row['survey']}', '{$row['email']}', {$row['country']}, {$row['lang']});");
	fwrite($fh,  $row['id'] . "<br />");
}
*/
//$run = runSql("delete from `played` where `state` in (2, 3, 5) and `datetime` <= " . $dayAgo);

echo "View cron job output here: http://www.ipsospollpredictor2.com/records/$_filename.txt";

?>
