<?

//Oh god this code is so messy....

include ('admin/dbauth.php');


include ('locksmith.php');
importRequest('g', 'form_');
$keyd = unlock($form_r);
$vars = explode(":", $keyd);

function is_ip($str){
	return ereg("^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$", $str);
}


	if ($form_game == "npn"){
		//////////////////////////////////////////////////////////////
		//	No purchase necessary
		//////////////////////////////////////////////////////////////

		$ip = $_SERVER['REMOTE_ADDR'];
		$key = lock($ip . ":::{$_GET['c']}:{$_GET['l']}");
		echo "Game created, click <a href='index.php?r=$key'>here</a> to play";
		
	}elseif ($_GET['r']){
		//////////////////////////////////////////////////////////////
		//	Error handling
		//////////////////////////////////////////////////////////////
		$err = "";
		//echo "{$vars[0]}:{$vars[1]}:{$vars[2]}:{$vars[3]}:{$vars[4]}";
		if ($_GET['r'] == "") {
			$err = "No string<br>";
		}
		if (!(($vars[1] != "" || $vars[2] != "") || is_ip($vars[0]))) {
			$err = "No record ID or Survey ID<br>";
		}
		if (!($vars[0] != "" && (is_numeric($vars[0]) || is_ip($vars[0])))) {
			$err = "UserID not numeric or an IP<br>";
		}
		if (!(($vars[1] != "" && (is_numeric($vars[1])) || is_ip($vars[0])) || (($vars[2] != "" && strtolower(substr($vars[2],0,1)) == "p") || is_ip($vars[0])))){
			$err = "RecordID or SurveyID are not good<br>";
		}
		if (!($vars[3] != "" && ($vars[3] == 1 || $vars[3] == 2))){
			$err = "Invalid Country<br>";
		}
		if (!($vars[4] != "" && ($vars[4] == 9 || $vars[4] == 12))) {
			$err = "Invalid Language<br>";
		}
		if ($err == ""){
			//////////////////////////////////////////////////////////////
			//	Check if game has already been played
			//////////////////////////////////////////////////////////////
			$state = 0;
			if ($vars[1] != ""){
				$run = runSql("select `state` from `played` where id = {$vars[1]} limit 1");
				$row = mysql_fetch_assoc($run);
				$state = $row['state'];
			}
			if ($state == 0 || $state == 1 || $state == 4){
	
			//////////////////////////////////////////////////////////////
			//	check for number of plays per survey
			//////////////////////////////////////////////////////////////

			//$run = runSql("select `id` from `played` where uid = '{$vars[0]}' and survey = '{$vars[2]}'");
			$run = runSql("select `plays` from `survey` where `uid` = '{$vars[0]}' and `sid` = '{$vars[2]}' limit 1");
			$row = mysql_fetch_assoc($run);
			//if ((is_ip($vars[0]) && mysql_num_rows($run) == 0) || (!is_ip($vars[0]) && mysql_num_rows($run) < 3)){
			if ((is_ip($vars[0]) && $row['plays'] <= 17) || (!is_ip($vars[0]) && $row['plays'] <= 3)){
				$rec = 1;
				if ($vars[1] != ""){
					$rec = 0;
					$run = runSql("select `id` from `played` where uid = '{$vars[0]}' and `id` = '{$vars[1]}' limit 1");
					$rec = mysql_num_rows($run);
				}
				if ($rec){

				//////////////////////////////////////////////////////////////
				//	If you've made it this far, you get to play
				//////////////////////////////////////////////////////////////

				$query = array();
			
				$run = runSql("select `chances` from `chances` where `uid` = '{$vars[0]}' limit 1");
				$row = mysql_fetch_assoc($run);

				$TEMP_user = $vars[0];
				$TEMP_chances = ($row['chances'] ? $row['chances'] : 0);

				//echo "userid={$vars[0]}&chances=" . ($row['chances'] ? $row['chances'] : "0" ) . "";

				$query['userid'] = "{$vars[0]}";
				$query['country'] = "{$vars[3]}";
				$query['NPN'] = is_ip($vars[0]);
				$query['chances'] = ($row['chances'] ? "{$row['chances']}" : "0" );

				//////////////////////////////////////////////////////////////
				//	Get a random game
				//////////////////////////////////////////////////////////////
				$run = runSql("select * from `game` where `lang` = {$vars[4]} and `country` = {$vars[3]} order by RAND() limit 1");
				$row = mysql_fetch_assoc($run);
				$game = $row['id'];

				//echo "question={$row['question']}&answer={$row['answer']}&"; // Question, answer
				$query['question'] = $row['question'];

				$randDemo = rand(0,2);
				switch($randDemo){
				case(0):
					$preqDemo = "s ";
					$preqDemoFr = "de Canadiens (hommes et femmes) ont répondu";
					$demoField = "total";
					break;
				case(1):
					$preqDemo = " men ";
					$preqDemoFr = "d'hommes canadiens ont répondu";
					$demoField = "male";
					break;
				case(2):
					$preqDemo = " women ";
					$preqDemoFr = "de femmes canadiennes ont répondu";
					$demoField = "female";
					break;
				}
				
				if ($vars[4] == 9){ //English
					$query['prequestion'] = "What percentage of " . ($vars[3] == 1 ? "American" : "Canadian") . $preqDemo . "answered \"YES\" to the following question:";
				}else{
					$query['prequestion'] = "Quel pourcentage " . $preqDemoFr . " \"OUI\" pour la question suivante:";
				}

				$query['answer'] = $row[$demoField]; // Question, answer

				$run = runSql("SELECT * FROM `prize` WHERE `expire` >= " . time() . " ORDER BY expire ASC LIMIT 4");
				$p = 1;
				$ptags = array("<p>", "</p>");
				while ($row = mysql_fetch_assoc($run)){
					//echo "pid$p={$row['id']}&pname$p={$row['name']}&pdesc$p=" . str_replace($ptags,"",$row['desc']) . "&pexp$p=" . date('n/j/y', $row['expire']) . "&pimg$p=http://www.ipsospollpredictor.com{$row['img']}&";
					$query["pid$p"] = $row['id'];
					if ($vars[4] == 12){
						$name = $row['name_fr'];
						$desc = $row['desc_fr'];
					}else{
						$name = $row['name'];
						$desc = $row['desc'];
					}
					$query["pname$p"] = $name; 
					$query["pdesc$p"] = str_replace($ptags,"",$desc);
					$query["pexp$p"] = date('n/j/y', $row['expire']);
					$query["pimg$p"] = "http://ipsospollpredictor2.com{$row['img']}";

					$run2 = runSql("SELECT `chances` FROM `prizechances` WHERE pid = {$row['id']} and `uid` = '{$vars[0]}' limit 1");
					$row2 = mysql_fetch_assoc($run2);
					//echo "pchan$p=" . ($row2['chances'] ? $row2['chances'] : "0") . "&";
					$query["pchan$p"] = ($row2['chances'] ? $row2['chances'] : "0");
					$p++;
				}
				//echo "language=" . ($vars[4] == 9 ? "English" : "French") . "&"; // language
				$query["language"] = ($vars[4] == 9 ? "English" : "French"); // language


				if (!$vars[1]){
					//This part just refused to work with mysql_insert_id() so I did it the long way. I'm too busy to care.
					$time = time();
					if (is_ip($vars[0])){ //Insert new record as a NPN game
						$run = runSql("insert into `played` values(NULL, '{$vars[0]}', $game, 4, " . $time . ", '{$vars[2]}', '', {$vars[3]}, {$vars[4]});");
					}else{ //insert new normal record
						$run = runSql("insert into `played` values(NULL, '{$vars[0]}', $game, 1, " . $time . ", '{$vars[2]}', '', {$vars[3]}, {$vars[4]});");
					}

					$run = runSql("select `id` from `survey` where `uid` = '{$vars[0]}' and `sid` = '{$vars[2]}' limit 1");
					if (mysql_num_rows($run)){ // increment plays for this user/survey
						$run = runSql("update `survey` set plays = plays + 1 where `uid` = '{$vars[0]}' and `sid` = '{$vars[2]}' limit 1;");
					}else{ // instert a new record. User has never played this survey
						$run = runSql("insert into `survey` values(NULL, '{$vars[0]}', '{$vars[2]}', 1);");
					}

					$run = runSql("select `id` from `played` where `uid` = '{$vars[0]}' AND `datetime` = $time limit 1");
					$row = mysql_fetch_assoc($run);
					$id = $row['id'];
					//echo  "playid=$id&";"
					$query["playid"] = $id;

					$TEMP_id = $id;
					if (is_ip($vars[0])){
						//echo "state=4&";
						$query["state"] = 4;
						$TEMP_state = 4;
					}else{
						//echo "state=1";
						$query["state"] = 1;
						$TEMP_state = 1;
					}
				}else{
					$run = runSql("update `played` set `gid` = $game, `datetime` = " . time() . " where `id` = {$vars[1]} limit 1");
					//echo "playid=" . $vars[1] . "&";
					$query["playid"] = $vars[1];
					$TEMP_id = $vars[1];
					//echo "state=2";
					$query["state"] = 2;
					$TEMP_state = 2;
				}
				//echo "<a href='http://ipsospollpredictor.com/index_flash.php?gameend=1&user=$TEMP_user&chances=$TEMP_chances&p1=3&p1chances=9&p2=2&p2chances=9&p3=1&p3chances=9&p4=4&p4chances=9&id=$TEMP_id&state=$TEMP_state'>finish game</a>";
				}else{
					$query["err"] = "No record";
				}
			}else{
				$query["err"] = "Over 3 playes/survey";
			}
		}else{
			$query["err"] = "This game has finished";
		}
		}else{
			$query["err"] = $err;
		}
		if ($err){
			runSql("insert into `errors` values(NULL, '$form_r', '$keyd', '$err', " . time() . ", '" . $_SERVER['REMOTE_ADDR'] . "')");
		}
		$query["ready"] = 1;
		echo http_build_query($query);

	
}else if($_GET['gameend'] == 1){ // END GAME

	$run = runSql("select `state` from `played` where `id` = $form_id limit 1");
	$row = mysql_fetch_assoc($run);

	if ($row['state'] == 1 || $row['state'] == 4){
		$run = runSql("select * from `chances` where `uid` = '$form_user' limit 1");
		if (mysql_num_rows($run)){
			$run = runSql("update `chances` set `chances` = $form_chances where `uid` = '$form_user' limit 1");					
		}else{
			$run = runSql("insert into `chances` values(NULL, '$form_user', $form_chances)");
		}
	
		// Prize the first
		if ($form_p1chances){
		$run = runSql("select * from `prizechances` where `uid` = '$form_user' AND `pid` = $form_p1 limit 1");
		if (mysql_num_rows($run)){
			$run = runSql("update `prizechances` set `chances` = `chances` + $form_p1chances where `uid` = '$form_user' and `pid` = $form_p1 limit 1");					
		}else{
			$run = runSql("insert into `prizechances` values(NULL, '$form_user', $form_p1, $form_p1chances)");
		}
		}
	
		// Prize the second
		if ($form_p2chances){
		$run = runSql("select * from `prizechances` where `uid` = '$form_user' AND `pid` = $form_p2 limit 1");
		if (mysql_num_rows($run)){
			$run = runSql("update `prizechances` set `chances` = `chances` + $form_p2chances where `uid` = '$form_user' and `pid` = $form_p2 limit 1");					
		}else{
			$run = runSql("insert into `prizechances` values(NULL, '$form_user', $form_p2, $form_p2chances)");
		}
		}
	
		// Prize the third
		if ($form_p3chances){
		$run = runSql("select * from `prizechances` where `uid` = '$form_user' AND `pid` = $form_p3 limit 1");
		if (mysql_num_rows($run)){
			$run = runSql("update `prizechances` set `chances` = `chances` + $form_p3chances where `uid` = '$form_user' and `pid` = $form_p3 limit 1");					
		}else{
			$run = runSql("insert into `prizechances` values(NULL, '$form_user', $form_p3, $form_p3chances)");
		}
		}
	
		// Prize the fourth
		if ($form_p4chances){
		$run = runSql("select * from `prizechances` where `uid` = '$form_user' AND `pid` = $form_p4 limit 1");
		if (mysql_num_rows($run)){
			$run = runSql("update `prizechances` set `chances` = `chances` + $form_p4chances where `uid` = '$form_user' and `pid` = $form_p4 limit 1");					
		}else{
			$run = runSql("insert into `prizechances` values(NULL, '$form_user', $form_p4, $form_p4chances)");
		}
		}	
		
		//Set to finished
		if ($row['state'] == 1 && $form_state == 1){ 
			$run = runSql("update `played` set `state` =  2 where `id` = $form_id limit 1");
			
		// Set to finished with problems
		}else if ($row['state'] == 1 && $form_state == 2){ 
			$run = runSql("update `played` set `state` =  3 where `id` = $form_id limit 1");
		
		// Set to finished NPN game
		}elseif ($row['state'] == 4){ 
			$run = runSql("update `played` set `state` =  5 where `id` = $form_id limit 1");
			$run = runSql("insert into `altEntries` values(NULL, '$form_user', '$form_email', '$form_fname', '$form_lname')");
		}
	}
	//echo '<script> window.top.location="http://ipsospollpredictor.com/"; </script>';
	exit;
}else{

		echo "Blanks";
	}
?>	
