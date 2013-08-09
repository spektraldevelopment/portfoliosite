<?
	include ("dbauth.php");
	include ("../locksmith.php");

?>
<html>
<head>
<style type="text/css">
	a{
		color:#00e;
		text-decoration:none;
	}
</style>
<script language=javascript>
	function makeTime(field){
		var myDate = new Date(document.getElementById(field + 'month').value + " " + document.getElementById(field + 'day').value + ", " + document.getElementById(field + 'year').value); // Your timezone!

		var myEpoch = (myDate.getTime()/1000.0)+36000;

		document.getElementById(field).value = myEpoch;
	}

</script>
</head>
<body>
<a href='index.php'>&lt; Back</a>
This gives the total overview of the state of all the games in any selected period of time.
<h2>Start</h2>
<hr>
<select id=startday onChange="makeTime('start');">
<?
for ($x=1; $x <= 31; $x++){
	echo "<option value=$x " . (date('j', $_GET['dateStart']) == $x ? "selected" : "") . ">$x</option>\n";
}
?>
</select>

<select id=startmonth onChange="makeTime('start');">
	<option value='January' <? echo (date('F', $_GET['dateStart']) == 'January' ? "selected" : "" ); ?>>January</option>
	<option value='February' <? echo (date('F', $_GET['dateStart']) == 'February' ? "selected" : "" ); ?>>February</option>
	<option value='March <? echo (date('F', $_GET['dateStart']) == 'March' ? "selected" : "" ); ?>'>March</option>
	<option value='April' <? echo (date('F', $_GET['dateStart']) == 'April' ? "selected" : "" ); ?>>April</option>
	<option value='May' <? echo (date('F', $_GET['dateStart']) == 'May' ? "selected" : "" ); ?>>May</option>
	<option value='June' <? echo (date('F', $_GET['dateStart']) == 'June' ? "selected" : "" ); ?>>June</option>
	<option value='July' <? echo (date('F', $_GET['dateStart']) == 'July' ? "selected" : "" ); ?>>July</option>
	<option value='August' <? echo (date('F', $_GET['dateStart']) == 'August' ? "selected" : "" ); ?>>August</option>
	<option value='September' <? echo (date('F', $_GET['dateStart']) == 'September' ? "selected" : "" ); ?>>September</option>
	<option value='October' <? echo (date('F', $_GET['dateStart']) == 'October' ? "selected" : "" ); ?>>October</option>
	<option value='November' <? echo (date('F', $_GET['dateStart']) == 'November' ? "selected" : "" ); ?>>November</option>
	<option value='December' <? echo (date('F', $_GET['dateStart']) == 'December' ? "selected" : "" ); ?>>December</option>
</select>
<input type="text" id=startyear maxlength=4 size=4 onKeyUp="makeTime('start');" value="<? echo ($_GET['dateStart'] ? date('Y', $_GET['dateStart']) : date('Y', time())); ?>">


<h2>End</h2>
<hr>

<select id=endday onChange="makeTime('end');">
<?
for ($x=1; $x <= 31; $x++){
	echo "<option value=$x " . (date('j', $_GET['dateEnd']) == $x ? "selected" : "" ) . ">$x</option>\n";
}
?>
</select>

<select id=endmonth onChange="makeTime('end');">
	<option value='January' <? echo (date('F', $_GET['dateEnd']) == 'January' ? "selected" : "" ); ?>>January</option>
	<option value='February' <? echo (date('F', $_GET['dateEnd']) == 'February' ? "selected" : "" ); ?>>February</option>
	<option value='March <? echo (date('F', $_GET['dateEnd']) == 'March' ? "selected" : "" ); ?>'>March</option>
	<option value='April' <? echo (date('F', $_GET['dateEnd']) == 'April' ? "selected" : "" ); ?>>April</option>
	<option value='May' <? echo (date('F', $_GET['dateEnd']) == 'May' ? "selected" : "" ); ?>>May</option>
	<option value='June' <? echo (date('F', $_GET['dateEnd']) == 'June' ? "selected" : "" ); ?>>June</option>
	<option value='July' <? echo (date('F', $_GET['dateEnd']) == 'July' ? "selected" : "" ); ?>>July</option>
	<option value='August' <? echo (date('F', $_GET['dateEnd']) == 'August' ? "selected" : "" ); ?>>August</option>
	<option value='September' <? echo (date('F', $_GET['dateEnd']) == 'September' ? "selected" : "" ); ?>>September</option>
	<option value='October' <? echo (date('F', $_GET['dateEnd']) == 'October' ? "selected" : "" ); ?>>October</option>
	<option value='November' <? echo (date('F', $_GET['dateEnd']) == 'November' ? "selected" : "" ); ?>>November</option>
	<option value='December' <? echo (date('F', $_GET['dateEnd']) == 'December' ? "selected" : "" ); ?>>December</option>
</select>
<input type="text" id=endyear maxlength=4 size=4 onKeyUp="makeTime('end');" value="<? echo ($_GET['dateEnd'] ? date('Y', $_GET['dateEnd']) : date('Y', time())); ?>">

<br>
<form method=get id=getRecords>
<input type="hidden" id=end name=dateEnd value="">
<input type="hidden" id=start name=dateStart value="">
<br><br>
User ID (optional): <input type="text" name=uid><br>
<input type=radio CHECKED name=getinfo value="records">Generate records<br>
<input type=radio name=getinfo value="urls">Generate urls for unfinished games<br>
<input type=button name="records" value="Get Records" onClick="makeTime('start'); makeTime('end'); document.getElementById('getRecords').submit();">
</form>
<h2>Games</h2>
<hr>
<?

if ($_GET['getinfo'] == "records"){
	$run = runSql("select * from `played` where " . ($_GET['uid'] ? "uid = '" . $_GET['uid'] . "' AND" : "" ) . " `datetime` <= {$_GET['dateEnd']} and `datetime` >= {$_GET['dateStart']}");
	echo "<b>Total</b>: " . mysql_num_rows($run);
	echo "<br>";
	
	$run = runSql("select * from `played` where " . ($_GET['uid'] ? "uid = '" . $_GET['uid'] . "' AND" : "" ) . " `state` = 1 AND `datetime` <= {$_GET['dateEnd']} and `datetime` >= {$_GET['dateStart']}");
	echo "<b>Started</b>: " . mysql_num_rows($run);
	echo "<br>";
	
	$run = runSql("select * from `played` where " . ($_GET['uid'] ? "uid = '" . $_GET['uid'] . "' AND" : "" ) . " `state` = 2 AND `datetime` <= {$_GET['dateEnd']} and `datetime` >= {$_GET['dateStart']}");
	echo "<b>Finished</b>: " . mysql_num_rows($run);
	echo "<br>";
	
	$run = runSql("select * from `played` where " . ($_GET['uid'] ? "uid = '" . $_GET['uid'] . "' AND" : "" ) . " `state` = 3 AND `datetime` <= {$_GET['dateEnd']} and `datetime` >= {$_GET['dateStart']}");
	echo "<b>Finished with problems</b>: " . mysql_num_rows($run);
	echo "<br>";
	
	$run = runSql("select * from `played` where " . ($_GET['uid'] ? "uid = '" . $_GET['uid'] . "' AND" : "" ) . " `state` = 4 AND `datetime` <= {$_GET['dateEnd']} and `datetime` >= {$_GET['dateStart']}");
	echo "<b>No Purchase Necessary Games</b>: " . mysql_num_rows($run);
	echo "<br>";
	
	$run = runSql("select * from `played` where " . ($_GET['uid'] ? "uid = '" . $_GET['uid'] . "' AND" : "" ) . " `state` = 5 AND `datetime` <= {$_GET['dateEnd']} and `datetime` >= {$_GET['dateStart']}");
	echo "<b>Finished No Purchase Necessary Games</b>: " . mysql_num_rows($run);
	echo "<br>";
	
	echo "<table width=100% border=1>\n";
	echo "<tr>\n";
	echo "<td><b><a href='?dateEnd={$_GET['dateEnd']}&dateStart={$_GET['dateStart']}&order=uid&dir=" . ($_GET['dir'] == "ASC" ? "DESC" : "ASC") . "&getinfo=records&uid={$_GET['uid']}'>User ";
		if($_GET['order'] == "uid"){
			echo ($_GET['dir'] == "ASC" ? "&uarr;" : "&darr;");
		}
	echo "</a></b></td>\n";

	
	echo "<td><b><a href='?dateEnd={$_GET['dateEnd']}&dateStart={$_GET['dateStart']}&order=gid&dir=" . ($_GET['dir'] == "ASC" ? "DESC" : "ASC") . "&getinfo=records&uid={$_GET['uid']}'>Game ID ";
	if($_GET['order'] == "gid"){
		echo ($_GET['dir'] == "ASC" ? "&uarr;" : "&darr;");
	}
	echo "</a></b></td>\n";
	
	
	echo "<td><b><a href='?dateEnd={$_GET['dateEnd']}&dateStart={$_GET['dateStart']}&order=datetime&dir=" . ($_GET['dir'] == "ASC" ? "DESC" : "ASC") . "&getinfo=records&uid={$_GET['uid']}'>Time ";
	if($_GET['order'] == "datetime" || $_GET['order'] == ""){
		echo ($_GET['dir'] == "ASC" ? "&uarr;" : "&darr;");
	}
	echo "</a></b></td>\n";
	
	
	echo "<td><b><a href='?dateEnd={$_GET['dateEnd']}&dateStart={$_GET['dateStart']}&order=state&dir=" . ($_GET['dir'] == "ASC" ? "DESC" : "ASC") . "&getinfo=records&uid={$_GET['uid']}'>State ";
	if($_GET['order'] == "state"){
		echo ($_GET['dir'] == "ASC" ? "&uarr;" : "&darr;");
	}
	echo "</a></b></td>\n";
	
	
	echo "<td><b><a href='?dateEnd={$_GET['dateEnd']}&dateStart={$_GET['dateStart']}&order=survey&dir=" . ($_GET['dir'] == "ASC" ? "DESC" : "ASC") . "&getinfo=records&uid={$_GET['uid']}'>Survey ID ";
	if($_GET['order'] == "survey"){
		echo ($_GET['dir'] == "ASC" ? "&uarr;" : "&darr;");
	}
	echo "</a></b></td>\n";
	
	
	
	echo "</tr>\n";
	$run = runSql("select * from `played` where " . ($_GET['uid'] ? "uid = '" . $_GET['uid'] . "' AND" : "" ) . " `datetime` <= {$_GET['dateEnd']} and `datetime` >= {$_GET['dateStart']} order by " . ($_GET['order'] ? "`{$_GET['order']}`" : "`datetime`") . ($_GET['dir'] ? "{$_GET['dir']}" : "DESC"));
	while($row = mysql_fetch_assoc($run)){
		echo "<tr>\n";
		echo "<td>{$row['uid']}</td>\n";
		echo "<td>{$row['gid']}</td>\n";
		echo "<td>" . date('m/d/y - G:i:s', $row['datetime']) . "</td>\n";
		echo "<td><a href='index.php?page=played&id={$row['id']}'>";
			switch($row['state']){
			case(1):
				echo "Started";
				break;
			case(2):
				echo "Finished";
				break;
			case(3):
				echo "Finished with problems";
				break;
			case(4):
				echo "No Purchase Necessary Game";
				break;
			case(5):
				echo "No Purchase Necessary Game Finished";
				break;

			}
		echo "</a></td>\n";
		echo "<td>" . ($row['survey'] ? $row['survey'] : "n/a") . "</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";

}else if ($_GET['getinfo'] == "urls"){

	$run = runSql("select * from `played` where `state` = 1 and `datetime` <= {$_GET['dateEnd']} and `datetime` >= {$_GET['dateStart']} order by " . ($_GET['order'] ? "`{$_GET['order']}`" : "`datetime`") . ($_GET['dir'] ? "{$_GET['dir']}" : "DESC"));
	echo mysql_num_rows($run) . " records found.";
	echo "<table border=1>";
	echo "<tr><td>UserID</td><td>URL</td><td>Email(Bulk entries only)</td></tr>";
	echo "</tr>\n";
	while($row = mysql_fetch_assoc($run)){
		echo "<tr>\n";
		echo "<td>{$row['uid']}</td>\n";
		echo "<td>http://www.ipsospollpredictor.com/?r=" . lock($row['uid'] . ":" . $row['id'] . "::" . $row['country'] . ":". $row['lang']) . "</td>\n";
		echo "<td>" . ($row['email'] ? $row['email'] : "&nbsp;") . "</td></tr>\n";
	}
	echo "</table>\n";

}else if ($_GET['getinfo'] == "games"){

	$run = runSql("select * from `game` where `question` LIKE '%{$_GET['gamestr']}%'");
	echo mysql_num_rows($run) . " records found.";
	echo "<table border=1>";
	echo "<tr><td>Question</td><td>Link</td></tr>";
	echo "</tr>\n";
	while($row = mysql_fetch_assoc($run)){
		echo "<tr>\n";
		echo "<td>{$row['question']}</td>\n";
		echo "<td><a href='http://ipsospollpredictor.com/admin/index.php?page=game&id=" . $row['id'] . "'>Link</a></td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";
}


?>
</body>
</html>
