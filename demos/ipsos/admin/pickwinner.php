<?

include('dbauth.php');

$perpage = 20;

if ($_GET['genID']){
	$x=1;
	$run = runSql("select * from prizechances where `pid` = " . $_GET['genID'] . "  and `chances` >=1 order by RAND() limit 20");
	while($row = mysql_fetch_assoc($run)){
		$run2 = runSql("insert into `winners` values (NULL, '{$row['uid']}', {$_GET['genID']}, $x);");
		$x++;
	}
	$run = runSql("select sum(chances) as `totChances` from prizechances where `pid` = " . $_GET['genID']);
	$row = mysql_fetch_assoc($run);
	$run = runSql("insert into `prizeLOG` values (NULL, {$_GET['genID']}, {$row['totChances']});");
	$run = runSql("delete from prizechances where `pid` = " . $_GET['genID']);
	
}

echo "<a href='index.php'>&lt; Back</a><br>";
echo "<a href='pickwinner.php'>Pick winners</a><br>";
echo "<a href='?queue=1'>Check Prize Queue</a><br>";
//echo "<a href='?queue=2'>View chances alloted to each prize</a> (<b>WARNING</b> This action also gets a sum of all chances allocated to a certain prize. This takes a while. It is recommended that you only preform this action is off peak hours.)<br>";


if($_GET['getWinners']){
	echo "<b>Winners:</b><br>";
	$run = runSql("SELECT `uid` FROM `winners` where `pid` = {$_GET['getWinners']} order by placeOrder ASC");
	while($row = mysql_fetch_assoc($run)){
		echo "{$row['uid']}<br />";
	}

	

}elseif($_GET['queue'] == 1){
	$curr = Array();

	//get current prizes:
	$run = runSql("SELECT `id` FROM `prize` WHERE `expire` >= " . time() . " ORDER BY expire ASC LIMIT 4");
	while($row = mysql_fetch_assoc($run)){
		array_push($curr, $row['id']);
	}


	//get prize queue
	$run = runSql("select count(id) as count from `prize`");
	$row = mysql_fetch_assoc($run);
	$pages = floor($row['count']/$perpage);

	$run = runSql('SELECT *  FROM `prize` ORDER BY `expire` DESC limit ' . ($_GET['p']*$perpage) . "," . $perpage);
	echo "<p>Prize Queue</p>";
	echo "<p>Current prizes are shown in the green boxes. Prizes above the green boxes are up and coming and below are expired. If there are no prizes above the green boxes there is a risk that they will expire with nothing to take it's place.</p>";
	echo "<table width=100% border=1>\n";
	echo "<tr><td><b>Prize:</b></td><td><b>Expiry date</b></td></tr>";
	while($row = mysql_fetch_assoc($run)){
		if (in_array($row['id'], $curr)){
			echo "<tr style='background-color:#D4FFBF'>";
		}else{
			echo "<tr>";
		}
		echo "<td><a href='index.php?page=prize&id=" . $row['id'] . "'>{$row['name']}</a></td><td> " . date('m/d/y', $row['expire']) . "</td>";
		echo "</tr>";
	}
	echo "</table>";

}elseif($_GET['queue'] == 2){
	
	$curr = Array();

	//get current prizes:
	$run = runSql("SELECT `id` FROM `prize` WHERE `expire` >= " . time() . " ORDER BY expire ASC LIMIT 4");
	while($row = mysql_fetch_assoc($run)){
		array_push($curr, $row['id']);
	}


	//get prize queue
	$run = runSql("select count(id) as count from `prize`");
	$row = mysql_fetch_assoc($run);
	$pages = floor($row['count']/$perpage);

	$run = runSql('SELECT a. * , sum( b.chances ) as `chances` FROM `prize` AS a LEFT JOIN `prizechances` AS b ON a.id = b.pid GROUP BY a.id ORDER BY `expire` DESC limit ' . ($_GET['p']*$perpage) . "," . $perpage);
	echo "<p>Prize Queue</p>";
	echo "<p>Current prizes are shown in the green boxes. Prizes above the green boxes are up and coming and below are expired. If there are no prizes above the green boxes there is a risk that they will expire with nothing to take it's place.</p>";
	echo "<table width=100% border=1>\n";
	echo "<tr><td><b>Prize:</b></td><td><b>Expiry date</b></td><td><b>Total Chances</b></td></tr>";
	while($row = mysql_fetch_assoc($run)){
		if (in_array($row['id'], $curr)){
			echo "<tr style='background-color:#D4FFBF'>";
		}else{
			echo "<tr>";
		}
		echo "<td><a href='index.php?page=prize&id=" . $row['id'] . "'>{$row['name']}</a></td><td> " . date('m/d/y', $row['expire']) . "</td><td>{$row['chances']}</td>";
		echo "</tr>";
	}
	echo "</table>";


}else{

	$run = runSql("select count(id) as count from `prize`");
	$row = mysql_fetch_assoc($run);
	$pages = floor($row['count']/$perpage);

	$run = runSql('select * from `prize` where `expire` <= ' . time() . ' limit ' . ($_GET['p']*$perpage) . "," . $perpage);
	echo "<p>List of prizes that have expired</p>";
	echo "<table width=100% border=1>\n";
	echo "<tr><td><b>Prize:</b></td><td><b>Expiry date</b></td><td><b>Action</b></td></tr>";
	while($row = mysql_fetch_assoc($run)){
		$run2 = runSql('select * from `winners` where `pid` = ' . $row['id']);
		echo "<tr><td><a href='index.php?page=prize&id=" . $row['id'] . "'>{$row['name']}</a></td><td> " . date('d/m/y', $row['expire']) . "</td>";
		echo "<td>";
		if (!mysql_num_rows($run2)){ 
			echo "<a href='?genID={$row['id']}'>Generate winners for this prize</a>";
		}else{
			echo "<a href='?getWinners={$row['id']}'>View The Winners</a>";
		}
		echo "</td></tr>";

	}
	echo "</table>";

}




if ($pages){
	echo "Pages: ";
	for ($x=0; $x<$pages; $x++){
		if ($x == $_GET['p']){
			echo ($x+1);
		}else{
			echo "<a href='?p=$x'>" . ($x + 1) . "</a>";
		}
	}
}


?>
