<? include ('dbauth.php');
include ('../locksmith.php');
if ($_FILES){
	$file = $_FILES['games']['tmp_name'];
	$fh = fopen($file, 'r');
	$data = fread($fh, filesize($file));
	fclose($fh);
	$data = split("\r\n", $data);
	$r = 0;
	for ($x=0;$x<=sizeof($data);$x++){
		$parts = split(",", $data[$x]);
		if ($parts[0]){
			//$dbh=mysql_connect ("localhost", $dbInfo['uname'], $dbInfo['pass']) or die ('<div class="adminMSG">Cannot connect to the database because: ' . mysql_error() . '</div>');
			//mysql_select_db ($dbInfo['db']);
			connectDB();
			$row = mysql_query("insert into `played` values(NULL, {$parts[0]}, 0, 1, " . time() . ", '', '{$parts[3]}', {$parts[1]}, {$parts[2]})", $dbh) or $msg  = 'counter INSERT error: '.mysql_errno().','.mysql_error() . "    " . $sql;
			$id = mysql_insert_id();
			$locked = lock("{$parts[0]}:$id::{$parts[1]}:{$parts[2]}");
			disconnectDB();

			//echo "<a target='_blank' href='http://ipsospollpredictor.com/admin/index.php?page=played&id=$id'>http://ipsospollpredictor.com/admin/index.php?page=played&id=$id</a><br>";
			echo "<a target='_blank' href='http://ipsospollpredictor2.com/index.php?r=$locked'>http://ipsospollpredictor2.com/index.php?r=$locked</a> - {$parts[3]}<br>";
			$r++;
		}
	}
	echo "$r records inserted<hr>";
	
}


?>
<html>
<head>
<title>Reports</title>
</head>
<body>

<a href='index.php'>&lt; Back</a>

Upload bulk games.<br><br>
<b>File format</b><br>
userid, country(1 = USA, 2 = CAN), Language (9 = ENG, 12 = FR)
<i>Ex.<br>
100, 2,9,email@email.com<br>
101,1,9,email@email.com<br>
102,2,12,email@email.com<br>
etc...
</i>
<form method=post enctype='multipart/form-data'>
	<input type=file name=games>
	<input type=submit value="go">
</form>
