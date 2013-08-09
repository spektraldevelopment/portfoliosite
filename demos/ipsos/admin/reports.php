<? include ('dbauth.php');?>
<html>
<head>
<title>Reports</title>
</head>
<body>
<a href='index.php'>&lt; Back</a>

<h2>Games</h2>
<hr>
<? 
$run = runSql("select * from `played`");
$total = mysql_num_rows($run);
echo "<b>Total</b>: " . $total;
echo "<br>";

$run = runSql("select * from `played` where `state` = 1");
echo "<b>Started</b>: " . mysql_num_rows($run) . " (" . ceil((mysql_num_rows($run)/$total)*100) . "%)";
echo "<br>";

$run = runSql("select * from `played` where `state` = 2");
echo "<b>Finished</b>: " . mysql_num_rows($run) . " (" . ceil((mysql_num_rows($run)/$total)*100) . "%)";
echo "<br>";

$run = runSql("select * from `played` where `state` = 3");
echo "<b>Finished with problems</b>: " . mysql_num_rows($run) . " (" . ceil((mysql_num_rows($run)/$total)*100) . "%)";
echo "<br>";

echo "<hr>";

$run = runSql("select * from `played` where `state` = 4 OR  `state` = 5");
echo "<b>Total Given Games</b>: " . mysql_num_rows($run);
echo "<br>";

$run = runSql("select * from `played` where `state` = 4");
echo "<b>Started Given Games</b>: " . mysql_num_rows($run) . " (" . ceil((mysql_num_rows($run)/$total)*100) . "%)";
echo "<br>";

$run = runSql("select * from `played` where `state` = 5");
echo "<b>Finished Given Games</b>: " . mysql_num_rows($run) . " (" . ceil((mysql_num_rows($run)/$total)*100) . "%)";
echo "<br>";



?>

</body>
