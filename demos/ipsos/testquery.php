<?php
$time1 = microtime(true);
session_start();
include "admin/dbauth.php";

$queries = array("SELECT SQL_NO_CACHE * FROM `prize` WHERE `expire` >= 1235578700 ORDER BY expire ASC LIMIT 4",
			"select SQL_NO_CACHE * from `game` where `lang` = 9 and `country` = 001 order by RAND() limit 1",
			"select SQL_NO_CACHE * from `game` where `lang` = 9 and `country` = 002 order by RAND() limit 1",
			"select SQL_NO_CACHE `chances` from `chances` where `uid` = 1030805726 limit 1",
			"select SQL_NO_CACHE `state` from `played` where `id` = 1308475 limit 1");

$sql = rand(0,4);
runSql($queries[$sql]);
$time2 = microtime(true);

$time = $time2-$time1;
echo "<br />".$time ." Seconds from $time1 & $time2 <br />".$queries[$sql];
$_SESSION['sqlresult'][$sql]['total_execution_time']+=$time;
$_SESSION['sqlresult'][$sql]['total_executions']++;
$_SESSION['sqlresult'][$sql]['average_time']=$_SESSION['sqlresult'][$sql]['total_execution_time']/$_SESSION['sqlresult'][$sql]['total_executions'];
print_r($_SESSION['sqlresult']);
//unset($_SESSION['sqlresult']);
?>

