<?
include ('admin/dbauth.php');
include ('locksmith.php');
import_request_variables('g', 'form_');
$keyd = unlock($form_r);
$vars = explode(":", $keyd);

function is_ip($str){
	return ereg("^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$", $str);
}

function giveChances($user, $chances){
	$run = runSql("select * from `chances` where `uid` = '$user'");
	if (mysql_num_rows($run)){
		$run = runSql("update `chances` set `chances` = `chances` + $chances where `uid` = '$user'");					
	}else{
		$run = runSql("insert into `chances` values(NULL, '$user', $chances)");
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
	<head profile="http://gmpg.org/xfn/11">
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		Dummy Ipsos game.<br>
		<ol>
<?
	if ($form_game == "npn"){
/*		connectDB();
		$run = mysql_query("insert into `played` values(NULL, '" . $_SERVER['REMOTE_ADDR'] . "', 0, 4, " . time() . ", 0, '');");
		$id = mysql_insert_id();
		disconnectDB();*/
		$ip = $_SERVER['REMOTE_ADDR'];
		$key = lock($ip . ":::{$_GET['c']}:{$_GET['l']}");
		echo "Game created, click <a href='index.php?r=$key'>here</a> to play";
	}else{
		//////////////////////////////////////////////////////////////
		//	Error handling
		//////////////////////////////////////////////////////////////
		$err = "";
		echo "{$vars[0]}:{$vars[1]}:{$vars[2]}:{$vars[3]}:{$vars[4]}";
		if ($_GET['r'] == "") {
			$err .= "No string<br>";
		}
		if (!(($vars[1] != "" || $vars[2] != "") || is_ip($vars[0]))) {
			$err .= "No record ID or Survey ID<br>";
		}
		if (!($vars[0] != "" && (is_numeric($vars[0]) || is_ip($vars[0])))) {
			$err .= "UserID not numeric or an IP<br>";
		}
		if (!(($vars[1] != "" && (is_numeric($vars[1])) || is_ip($vars[0])) || (($vars[2] != "" && strtolower(substr($vars[2],0,1)) == "p") || is_ip($vars[0])))){
			$err .= "RecordID or SurveyID are not good<br>";
		}
		if (!($vars[3] != "" && ($vars[3] == 1 || $vars[3] == 2))){
			$err .= "Invalid Country<br>";
		}
		if (!($vars[4] != "" && ($vars[4] == 9 || $vars[4] == 12))) {
			$err .= "Invalid Language<br>";
		}
		if ($err == ""){
			//////////////////////////////////////////////////////////////
			//	Get a random game
			//////////////////////////////////////////////////////////////
			$run = runSql("select `id` from `game` order by RAND() limit 1");
			$row = mysql_fetch_assoc($run);
			$game = $row['id'];

			//////////////////////////////////////////////////////////////
			//	Start Game
			//////////////////////////////////////////////////////////////
			echo "<li>Good String:<br>";
			echo "UserId: {$vars[0]}<br>";
			echo "RecordID: {$vars[1]}<br>";
			echo "SurveyID: {$vars[2]}<br>";
			echo "CountryID: " . ($vars[3] == 2 ? "CANADA" : "USA") . "<br>";
			echo "LanguageID: " . ($vars[4] == 9 ? "English" : "French") . "</li>";
			//////////////////////////////////////////////////////////////
			//	Recovery game (Has a record ID)
			//////////////////////////////////////////////////////////////
			if ($vars[1] !=""){
				$run = runSql("select * from `played` where uid = '{$vars[0]}' and `id` = '{$vars[1]}'");
				if (mysql_num_rows($run)){
					echo "<li>Record ID, This is a recovery or a given game</li>";
					$run = runSql("select * from `played` where `id` = {$vars[1]}");
					if (mysql_num_rows($run)){ //see if theres even a record with that ID
						$row = mysql_fetch_assoc($run);
						if ($row['state'] == 1 || $row['state'] == 4){
							echo "<li>Game started</li>";
							echo "<li>**GAME PLAYED (ID: $game)**</li>";
							$run = runSql("update `played` set `state` = " . ($row['state'] == 1 ? "3" : "5" ) . ", `datetime` = " . time() . " where `id` = {$vars[1]}");
							echo "<li><b>Finished</b></li>";
							// Give chances.
							$chances = 1000;
							giveChances($vars[0], $chances);
						}else{
							echo "<li><b>ERROR</b> This game has already been played.</li>";
						}
					}else{
						echo "<li><b>No such ID<b></li>";
					}
				}else{
					echo "<li><b>ERROR</b> Userid and record ID do not match up.</li>";
				}
			//////////////////////////////////////////////////////////////
			//	New Game (has a survey ID)
			//////////////////////////////////////////////////////////////
			}elseif ($vars[2] != "") {
				echo "<li>No Record ID, This is a new game</li>";
				$run = runSql("select `id` from `played` where uid = '{$vars[0]}' and survey = '{$vars[2]}'");
				if (!mysql_num_rows($run)){
					$run = runSql("insert into `played` values(NULL, '{$vars[0]}', $game, 1, " . time() . ", '{$vars[2]}', '', {$vars[3]}, {$vars[4]});");
					echo "<li>Game started and recorded</li>";
					$run = runSql("select `id` from `played` where `uid` = '{$vars[0]}' and `gid` = $game and `survey` = '{$vars[2]}'");
					$row = mysql_fetch_assoc($run);
					$insID = $row['id'];
					echo "<li>**GAME PLAYED (ID: $game)**</li>";
					$run = runSql("update `played` set `state` = 2, `datetime` = " . time() . " where `id` = $insID");
					// Give chances.
					$chances = 1000;
					giveChances($vars[0], $chances);
					echo "<li><b>END</b>: Game finished and recorded.</li>";
				}else{
					echo "<li><b>ERROR</b>: User has completed this survey, cheating</li>";
				}
			//////////////////////////////////////////////////////////////
			//	No purchase necessary game
			//////////////////////////////////////////////////////////////		
			}else{ 
				echo "<li>IP address</li>";
				$runtest = runSql("select `state` from `played` where `uid` = '{$vars[0]}'");
				$rowstest = mysql_fetch_assoc($runtest);
				echo "<li>No Purchase necessary game.</li>";
				if ($rowstest['state'] == 5){
					echo "<li>Given game already redeemed for this IP</li>";					
				}else{
					$run = runSql("insert into `played` values(NULL, '{$vars[0]}', $game, 4, " . time() . ", '{$vars[2]}', '', {$vars[3]}, {$vars[4]});");
					echo "<li>Game started and recorded</li>";
					echo "<li>**GAME PLAYED (ID: $game)**</li>";
					$run = runSql("update `played` set `state` = 5, `datetime` = " . time() . " where `uid` = '{$vars[0]}'");
					echo "<li><b>Finished</b></li>";
					// Give chances.
					$chances = 1000;
					giveChances($vars[0], $chances);
				}
			}
		}else{
			echo "<li><b>Error</b>: Faulty string<br>$err</li>";
		}
	}
		

?>	
</ol>
<br><a href='?game=npn&c=1&l=9'>No purchase Nessessary game ENG USA</a>
<br><a href='?game=npn&c=2&l=9'>No purchase Nessessary game ENG CANADA</a>
<br><a href='?game=npn&c=2&l=12'>No purchase Nessessary game FR CANADA</a>
	</body>
</html>
