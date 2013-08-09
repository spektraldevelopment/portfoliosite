<?
/********************************************************************************************************************************************************************************************************
******************************************************************************    steamEngine v 0.99   **************************************************************************************************
***************                                                                                                                                                                         *****************
***************  steamEngine is built and maintaned by Thomas Girard and is offered under the GPL (General Public Licence) and you are free to distribute and modify it in any way.     *****************
***************  You are NOT, however, allowed to charge money for any copies of this software, modified or otherwise and you must keep this statement at the top of index.php.         *****************
***************  Enjoy and support free software http://www.gnu.org/licenses/gpl-faq.html                                                                                               *****************
***************                                                                                                                                                                         *****************
*********************************************************************************************************************************************************************************************************
********************************************************************************************************************************************************************************************************/

$arrNumType = array('number', 'num_select', 'num_module', 'timeNow', 'float');
$arrAlphaType = array('long_module', 'text', 'textarea', 'alpha_select', 'alpha_module', 'file', 'div', 'password', 'ro');

session_start();

////////////////////////////////////////////////////////////////////////
//DB connecter file
////////////////////////////////////////////////////////////////////////

include('dbauth.php');


////////////////////////////////////////////////////////////////////////
//pulls in GET args
////////////////////////////////////////////////////////////////////////

importRequest('g', 'form_');


////////////////////////////////////////////////////////////////////////
//Get site controls
////////////////////////////////////////////////////////////////////////

$run = runSql('SELECT * FROM `_site`');
while($row = mysql_fetch_assoc($run)){
	$_SITE[$row['param_name']] = $row['param_value'];
}


////////////////////////////////////////////////////////////////////////
// Log users out.
////////////////////////////////////////////////////////////////////////

if ($form_act == 'logout'){
	$_SESSION['uid' . $_SERVER['SERVER_ADDR']] = "";
}


////////////////////////////////////////////////////////////////////////
//Login
////////////////////////////////////////////////////////////////////////

if ($_POST['_uname']){
	$run = runSql("SELECT * FROM `{$_SITE['users_table']}` WHERE `username` = '" . sanitizeInput($_POST['_uname']) . "'");
	if (mysql_num_rows($run)){
		$row = mysql_fetch_assoc($run);
		if (md5($_POST['_pass']) == $row['password']){
			$_SESSION['uid' . $_SERVER['SERVER_ADDR']] = $row['id']; 
			runSql("update `{$_SITE['users_table']}` set `ip` = '{$_SERVER['REMOTE_ADDR']}', `lastlogin` = " . (time() + $_SITE['server_offset']) . " where `id` = {$row['id']};");
		}else{
			echo "Invalid Login";
			runSql("insert into `_errlog` values(NULL, 'Invalid password', 'User: " . sanitizeInput($_POST['_uname']) . "<br />\r\nPassword: " . sanitizeInput($_POST['_pass']) . "<br />\r\nIP: " . sanitizeInput($_SERVER['REMOTE_ADDR']) . "<br />\r\nTime: " . date('F d y, H:i', time() + $_SITE['server_offset']) . "\r\n');");
		}
	}else{
		echo "Invalid Login";
			runSql("insert into `_errlog` values(NULL, 'Invalid username', 'User: " . sanitizeInput($_POST['_uname']) . "<br />\r\nPassword: " . sanitizeInput($_POST['_pass']) . "<br />\r\nIP: " . sanitizeInput($_SERVER['REMOTE_ADDR']) . "<br />\r\nTime: " . date('F d y, H:i', time() + $_SITE['server_offset']) . "\r\n');");
	}
	
}


////////////////////////////////////////////////////////////////////////
// Binary Permission checks
////////////////////////////////////////////////////////////////////////

function permCheck($l,$check){ //check value
	global $_SITE;
	$myBin = decbin($l);
	$checkBin = decbin($check);

	$length = strlen($myBin) - strlen($checkBin);

	for ($x = 0; $x < $length; $x++){
		$checkBin = "0" . $checkBin;
	}

	$checkPos = strpos($checkBin, "1");

	if ($_SITE['debug_mode']){
		echo "<span style='color:#f00;'>Check $checkBin<br />Pass  $myBin";
		echo "</span><br />";
	}

	if (substr($myBin,$checkPos,1) == 1){
		return 1;
	}else{
		return 0;
	}
}


function adminperm($u,$check){ //check User
	global $_SITE;
	$run = runSql("select b.value from `{$_SITE['users_table']}` as a left join _permission as b on a.permission = b.id where a.id = $u");
        $row = mysql_fetch_assoc($run);
	$myperm = $row['value'];

	$myBin = decbin($myperm);
	$checkBin = decbin($check);

	$length = strlen($checkBin) - strlen($myBin);

	for ($x = 0; $x < $length; $x++){
		$myBin = "0" . $myBin;
	}

	$myPos = strpos($myBin, "1");

	if ($_SITE['debug_mode']){
		echo "<span style='color:#f00;'>Permission<br />Check $checkBin<br />Pass  $myBin";
		echo "</span><br />";
	}

	if (substr($checkBin,$myPos,1) == 1){
		return 1;
	}else{
		return 0;
	}

}

////////////////////////////////////////////////////////////////////////
// Resize images
////////////////////////////////////////////////////////////////////////


function resize($img, $newWidth, $newHeight){
	$max_width=$newWidth;
	//Check if GD extension is loaded
	if (!extension_loaded('gd') && !extension_loaded('gd2')){
		echo "GD extension not loaded";
        	return false;
	}else{
		//Get Image size info
		list($oWidth, $oHeight, $image_type) = getimagesize($img);
	   	switch ($image_type){
			case 1: $im = imagecreatefromgif($img); break;
			case 2: $im = imagecreatefromjpeg($img);  break;
			case 3: $im = imagecreatefrompng($img); break;	
			default:  trigger_error('Unsupported filetype!', E_USER_WARNING);  break;
		}
   
		/*** calculate the aspect ratio ***/
		$as = (float) $oHeight / $oWidth;

		/*** calulate the thumbnail width based on the height ***/
		$newHeight = round($newWidth * $as);
   

		while($newHeight>$max_width){
		        $newWidth-=10;
		        $newHeight = round($newWidth * $as);
		}
   
		$newImg = imagecreatetruecolor($newWidth, $newHeight);
   
		/* Check if this image is PNG or GIF, then set if Transparent*/ 
		if(($image_type == 1) OR ($image_type==3)){
		        imagealphablending($newImg, false);
		        imagesavealpha($newImg,true);
		        $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
		        imagefilledrectangle($newImg, 0, 0, $newWidth, $newHeight, $transparent);
		}
		imagecopyresampled($newImg, $im, 0, 0, 0, 0, $newWidth, $newHeight, $oWidth, $oHeight);
   
		//Generate te file, and rename it to $newfilename
		switch ($image_type){
		        case 1: imagegif($newImg,$newfilename); break;
		        case 2: imagejpeg($newImg,$newfilename);  break;
		        case 3: imagepng($newImg,$newfilename); break;
			default:  trigger_error('Failed resize image!', E_USER_WARNING);  break;
		}
 
		return $newfilename;
	}
}


////////////////////////////////////////////////////////////////////////
//FTP
////////////////////////////////////////////////////////////////////////

function ftpit($t, $dir, $act = NULL, $path = NULL){
	// set up basic connection
	global $_SITE;
	global $msg;
	if (isset($_FILES) || $act){
		$ftp_server = $_SITE['ftp_server'];
		$conn_id = ftp_connect($ftp_server)  or die("Could not connect");
		$ftp_user_name = $_SITE['ftp_user'];
		$ftp_user_pass = $_SITE['ftp_password'];
		$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
		if ($_SITE['debug_mode']){
			if ((!$conn_id) || (!$login_result)) {
			        echo "<span style='color:#f00;'>FTP connection has failed!";
			        echo "Attempted to connect to $ftp_server for user $ftp_user_name</span><br />";
			        exit;
			} else {
			        echo "<span style='color:#f00;'>Connected to $ftp_server, for user $ftp_user_name</span><br />";
			}
		}

		if ($_FILES[$t]["name"] != "" && !$act){ //Upload a file.
/*
			// This is the temporary file created by PHP
			$uploadedfile = $_FILES['uploadfile']['tmp_name'];

			// Create an Image from it so we can do the resize
			$src = imagecreatefromjpeg($uploadedfile);

			// Capture the original size of the uploaded image
			list($width,$height)=getimagesize($uploadedfile);

			// For our purposes, I have resized the image to be
			// 600 pixels wide, and maintain the original aspect
			// ratio. This prevents the image from being "stretched"
			// or "squashed". If you prefer some max width other than
			// 600, simply change the $newwidth variable
			$newwidth=600;
			$newheight=($height/$width)*$newwidth;
			$tmp=imagecreatetruecolor($newwidth,$newheight);

			// this line actually does the image resizing, copying from the original
			// image into the $tmp image
			imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);

			// now write the resized image to disk. I have assumed that you want the
			// resized, uploaded image file to reside in the ./images subdirectory.
			$filename = "images/". $_FILES['uploadfile']['name'];
			imagejpeg($tmp,$filename,100);

			imagedestroy($src);
			imagedestroy($tmp); // NOTE: PHP will clean up the temp file it created when the request
			// has completed.
 */


			$source_file = $_FILES[$t]["tmp_name"];
			$destination_file = $_SITE['ftp_base_href'] . $_SITE['base_href'] . $dir . $_FILES[$t]["name"];

			if($_SITE['debug_mode']){
				echo "<span style='color:#f00;'>img: $t ->" . $destination_file . "</span><br />";
			}

			$upload = ftp_put($conn_id, $destination_file, $source_file, FTP_BINARY);

			if($_SITE['debug_mode']){
				if (!$upload) {
					echo "<span style='color:#f00;'>FTP upload has failed!!</span><br />";
				} else {
					echo "<span style='color:#f00;'>Uploaded $source_file to $ftp_server as $destination_file</span><br />";
				}
			}

			// close the FTP stream
			ftp_close($conn_id);

			array_push($msg, "File uploaded!<br /><i>". $dir . $_FILES[$t]["name"] . "</i>");
			if ($h || $w){
				resize($_SITE['base_href'] . $dir . $_FILES[$t]["name"], $w, $h);
			}
			return $dir . $_FILES[$t]["name"];
		}elseif ($act = "del"){ //Delete a file
			if (ftp_delete($conn_id, $_SITE['ftp_base_href'] . $_SITE['base_href'] . $path) && $_SITE['debug_mode']){
				echo "<span style='color:#f00;'>" . $_SITE['ftp_base_href'] . $_SITE['base_href'] . $path . "deleted";
			}
			array_push($msg, "File Deleted");
			return "";
		}else{
			return "";
		}
	}
}





////////////////////////////////////////////////////////////////////////
//Submit a form
////////////////////////////////////////////////////////////////////////

if($_POST['Xsubmit']){
	if ($_POST['Xinstall'] != ""){ //Installing a Module
		$file = '_mod/' . $_POST['Xinstall'] . "/install.sql";
		if (file_exists($file)) {
			$fh = fopen($file, 'r');
			$sql = fread($fh,8192);
			runSql($sql);
			array_push($msg, "Module successfully installed.");
		}else{
			array_push($msgBad, "Module not found.");
		}
	}else{	// Submit
		global $_SITE;
		$base_href = $_SITE['base_href'];
		$ftp_server = $_SITE['ftp_server'];
		$ftp_user_name = $_SITE['ftp_uname'];
		$ftp_user_pass = $_SITE['ftp_pass'];
		foreach($_FILES as $k => $v){
			if ($v['name'] != "" && $_POST['XDel' . $k] == ''){
				$_POST[$k] = ftpit($k, $_POST['XDIR' . $k], $_POST['XWIDTH' . $k], $_POST['XHEIGHT' . $k]);
			}elseif ($v['name'] == "" && $_POST['XDel' . $k] == 'delete'){
				echo "DELETING";
				$_POST[$k] = ftpit($k, $_POST['XDIR' . $k], 'del', $_POST['XPath' . $k]);
			}
		}
		if ($_POST['Xtab'] != "_assets"){
			if ($_POST['Xid'] && $_POST['Xtab'] != '_pages' && $_POST['Xtab'] != '_fields' && $_POST['Xtab'] != '_module' && $_POST['Xtab'] != '_assets'){
				// if this is an update to a normal page, make a backup.
				$run = runSql("select * from {$_POST['Xtab']} where `id` = {$_POST['Xid']}");
				$_SESSION['bkp'] = mysql_fetch_assoc($run);
				$_SESSION['bkp']['XXXundo_desc'] = "Update to `{$_POST['Xtab']}` " . date('m/j/y \a\t H:i', time() + $_SITE['server_offset']);
				$_SESSION['bkp']['Xtab'] = $_POST['Xtab'];
				$_SESSION['bkp']['Xid'] = $_POST['Xid'];
			}

			//Normal Submit
			if (($_POST['Xtab'] == '_fields' && $_POST['Npage'] != '') || $_POST['Xtab'] != '_fields'){
			$f = "";	//clear field
			$v = ""; 	//clear values
			$table = $_POST['Xtab'];
			// Gather up variables and write them into an SQL statement
			foreach ($_POST as $key => $value){
				if (substr($key, 0,1) != 'X' && substr($key, 0,1) != 'F'){ //Skip over form fields we dont want to insert
					if (!(substr($key, 0,1) == 'P' && $value == "")){ // Do not update empty password fields
						$keyFixed = substr($key, 1, strlen($key) - 1);
						$f .= "||| `$keyFixed`";
						if (substr($key, 0,1) == 'N'){
							$v .= "||| " . (str_replace("'", "&#146;", $value) == "" ? 0 : str_replace($garb, $rep, $value));
						}elseif (substr($key, 0,1) == 'T'){ //TinyMCE fields
							$v .= "||| '" . str_replace("'", "&#146;", $value) . "'";
						}elseif (substr($key, 0,1) == 'A'){
							$v .= "||| '" . str_replace("'", "&#146;", $value) . "'";
						}elseif (substr($key, 0,1) == 'P'){
							$v .= "||| '" . md5($value) . "'";
						}elseif (substr($key, 0,1) == 'D'){ //Date format
							$v .= "||| " . strtotime($value);
						}
					}
				}
			}
			if ($_POST['Xid']){
				if ($_POST['Xundo-restore']){
					// Restore a deleted record
					$f = str_replace('|||', ',', $f);
					$v = str_replace('|||', ',', $v);
					$sql = "insert into `$table` (id $f) values({$_POST['Xid']} $v)";
					$action = "added";					
				}else{
					// Update a record
					$sql = "update `$table` set `id` = {$_POST['Xid']}";
					$fArr = explode("||| ", $f);
					$vArr = explode("|||", $v);
				
					foreach($fArr as $k => $v){
						if ($v != ""){
							$sql .= ", $v = {$vArr[$k]}";
						}
					}
					$sql .= " where `id` = {$_POST['Xid']};";
					$action = "updated";
				}
			}else{
				// Insert new record
				$f = str_replace('|||', ',', $f);
				$v = str_replace('|||', ',', $v);
				$sql = "insert into `$table` (id $f) values(NULL $v)";
				$action = "added";
			}
			//Run SQL
			runSql($sql);
			global $msg;
			array_push($msg, "'$table' entry $action successfully.");
			
			
			////////////////////////////////////////////////////////////////////////
			// Make RSS feeds
			////////////////////////////////////////////////////////////////////////

			// Check if this page has RSS set.
			$runRSS = runSql("select `id`, `rss`, `rssTitle`, `rssDesc`, `rssLink` from `_pages` where `table` = '$table'");
			$rowRSS = mysql_fetch_assoc($runRSS);

			if ($rowRSS['rss']){

				$rssGarb = array("<", ">", "&#146;" ,"&rsquo;");
				$rssRep = array("&lt;", "&gt;", "", "");

				$thisRSS = $rowRSS;

				$fh = fopen("rss/$table.rss", "w");

				//Get RSS fields
				$runRSS = runSql("select `id`, `name`, `type`, `rss` from `_fields` where `page` = {$thisRSS['id']} and `rss` <> 0");
				while($rowRSS = mysql_fetch_assoc($runRSS)){
					if ($rss[$rowRSS['rss']] == ""){
						$rss[$rowRSS['rss']] = ($rowRSS['type'] == 'file' ? "FILE{$rowRSS['name']}" : $rowRSS['name']);
					}else{
						$rss[$rowRSS['rss']] .= "," . ($rowRSS['type'] == 'file' ? "FILE{$rowRSS['name']}" : $rowRSS['name']);
					}
				}

				$runRSS = runSql("select * from `$table` order by `id` desc");

				fwrite($fh, '<?xml version="1.0" encoding="UTF-8"?><rss xmlns:atom="http://www.w3.org/2005/Atom" xmlns:openSearch="http://a9.com/-/spec/opensearchrss/1.0/" version="2.0">');
				fwrite($fh, '<channel>');
				fwrite($fh, "<title>{$thisRSS['rssTitle']}</title>");
				fwrite($fh, "<description>{$thisRSS['rssDesc']}</description>");
				fwrite($fh, "<link>{$thisRSS['rssLink']}</link>");
				
				while($rowRSS = mysql_fetch_assoc($runRSS)){
					fwrite($fh, '<item>');
					fwrite($fh, "<title>" . ($rowRSS[$rss[1]] ? $rowRSS[$rss[1]] : 'Untitled' ) . "</title>");
					fwrite($fh, "<description>");
					if (strpos($rss[2], ",")){
						$descArray = explode(",", $rss[2]);
						foreach($descArray as $v){
							if (strpos($v, "FILE") !== false){
								$v = substr($v, 4, strlen($v));
								fwrite($fh, str_replace($rssGarb, $rssRep, "<img src='{$_SITE['base_url']}{$rowRSS[$v]}'>")); 
							}else{
								fwrite($fh, str_replace($rssGarb, $rssRep, $rowRSS[$v])); 
							}
						}
					}else{
						fwrite($fh, str_replace($rssGarb, $rssRep, $rowRSS[$rss[2]])); 
					}
					fwrite($fh, "</description>");
					fwrite($fh, "<link>{$thisRSS['rssLink']}/?id={$rowRSS['id']}</link>");
					fwrite($fh, '</item>');
				}
				fwrite($fh, '</channel>');
				fwrite($fh, '</rss>');

			}
			////////////////////////////////////////////////////////////////////////
			// Admin pages need to do some serious SQL work to modify the DB
			////////////////////////////////////////////////////////////////////////
	
			if ($_POST['Xadmin'] == 'page' && $_POST['Xid'] == "" && $_POST['Atable'] != ""){ 

				// Create new table
				runSQL('CREATE TABLE `' . str_replace($garb, $rep, $_POST['Atable']) . '` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,PRIMARY KEY ( `id` )) ENGINE = InnoDB');

				// Add message to the user.
				array_push($msg, "Successfully Added page '" . str_replace($garb, $rep, $_POST['Atable']) . "'");

			}elseif ($_POST['Xadmin'] == 'field'){ 

				// Add new field
				if ($_POST['Npage'] != ''){
					$type = "";

					// Switch on the field type for the data type
					switch($_POST['Atype']){
						case 'alpha_select':
						case 'password':
						case 'file':
						case 'alpha_module':
						case 'text':
							$type = "VARCHAR( 255 )";
							break;
						case 'float':
							$type = "FLOAT";
							break;
						case 'timeNow':
						case 'num_select':
						case 'num_module':
						case 'number':
							$type = "INT( 11 )";
							break;
						case 'textarea':
						case 'long_module':
							$type = "mediumtext";
							break;
						case 'div':
							$type ="";
							break;
					}
					if ($type != ""){
						// Dividers do not get put into the DB
						$run = runSql("SELECT `table` FROM `_pages` WHERE `id` = {$_POST['Npage']}");
						$row = mysql_fetch_assoc($run);
						if ($_POST['Xid']){
							runSQL("ALTER TABLE `{$row['table']}` CHANGE `{$_POST['Xname']}` `{$_POST['Aname']}` $type NOT NULL  ");
							array_push($msg, "Successfully modified field '{$_POST['Aname']}'");
						}else{		
							runSQL("ALTER TABLE `{$row['table']}` ADD `{$_POST['Aname']}` $type NOT NULL ;");
							array_push($msg, "Successfully added field '{$_POST['Aname']}'");
						}
					}
				}else{
					array_push($msgBad, "You must choose a page to add a field");
				}
			}
		}else{
					array_push($msgBad, "You must choose a page to add a field");
		}

		}else{ // ASSETS 
			if ($_POST['Xid']){
				runSql("update `_assets` set `title` = '{$_POST['Atitle']}', `path` = '{$_POST[$k]}' where `id` = {$_POST['Xid']}");
			}else{
				runSql("insert into `_assets` values(NULL, '{$_POST[$k]}', '" . str_replace("'", "&#146;", $_POST['Atitle']) . "')");
			}
			//array_push($msg, "<a target='_blank' href='{$_SITE['base_href']}{$_POST[$k]}'>Click here to see your file.</a>");

		}
	}
}

////////////////////////////////////////////////////////////////////////
//Delete an Item
////////////////////////////////////////////////////////////////////////

if($_POST['Xdel']){
	if ($_POST['XDel_assets'] == 'delete'){ // Delete _assets file
		ftpit($k, "", 'del', $_POST['XPath']);
	}
	//Make a backup
	$run = runSql("select * from {$_POST['Xtab']} where `id` = {$_POST['Xid']}");
	$_SESSION['bkp'] = mysql_fetch_assoc($run);
	$_SESSION['bkp']['XXXundo_desc'] = "Deleted from `{$_POST['Xtab']}` " . date('m/j/y \a\t H:i', time() + $_SITE['server_offset']);
	$_SESSION['bkp']['Xtab'] = $_POST['Xtab'];
	$_SESSION['bkp']['Xid'] = $_POST['Xid'];


	if ($_POST['Xadmin'] == 'page' && $_POST['Xid'] != ""){
		$run = runSql("SELECT `table` FROM `_pages` WHERE `id` = {$_POST['Xid']}");
		$row = mysql_fetch_assoc($run);
		
		if($row['table']){
			// Drop the corrosponding table if there is one
			runSQL("DROP TABLE `{$row['table']}`");
		}

		// Delete the pages entry
		runSql("DELETE FROM `_pages` WHERE `id` = {$_POST['Xid']}");

		// Delete the fields entries
		runSql("DELETE FROM `_fields` WHERE `page` = {$_POST['Xid']}");

		array_push($msg, "Successfully removed page '{$row['table']}'");
	}elseif ($_POST['Xadmin'] == 'field' && $_POST['Xid'] != ""){
		$run = runSql("SELECT a.name, b.table FROM `_fields` as a left join `_pages` as b on a.page = b.id WHERE a.`id` = {$_POST['Xid']}");
		$row = mysql_fetch_assoc($run);

		// Remove the field from the table
		runSql("ALTER TABLE `{$_POST['Xtab2']}` DROP `{$row['name']}`;");

		// Delete the field entry in the fields table
		runSql("DELETE FROM `_fields` WHERE `id` = {$_POST['Xid']}");

		array_push($msg, "Successfully removed field '{$row['name']}'");
	}else{
		runSql("DELETE FROM `{$_POST['Xtab']}` WHERE `id` = {$_POST['Xid']}");
		array_push($msg, "Successfully removed record '{$_POST['Aname']}'");
		
	}
}

////////////////////////////////////////////////////////////////////////
// Write a page
////////////////////////////////////////////////////////////////////////

function writePage($rowFields, $rowFetch, $r){
	global $_SITE;
	switch ($rowFields['type']){
	case 'text':
		echo "<tr class='tr$r'>\n";
		echo "<td>\n";
		echo "<b>{$rowFields['engName']}</b>";
		echo "</td>\n";
		echo "<td>\n";
		echo "<input type='text' size='100' id='A{$rowFields['name']}' name='A{$rowFields['name']}' value='{$rowFetch[$rowFields['name']]}' {$rowFields['param']} />";
		echo "</td>\n";
		echo "</tr>\n";
		break;
	case 'float':
	case 'number':
		echo "<tr class='tr$r'>\n";
		echo "<td>\n";
		echo "<b>{$rowFields['engName']}</b>";
		echo "</td>\n";
		echo "<td>\n";
		echo "<input type='text' size='100' id='N{$rowFields['name']}' name='N{$rowFields['name']}' value='" . ($rowFetch[$rowFields['name']] ? $rowFetch[$rowFields['name']] : "0") . "' {$rowFields['param']} />";
		echo "</td>\n";
		echo "</tr>\n";
		break;
	case 'password':
		echo "<tr class='tr$r'>\n";
		echo "<td>\n";
		echo "<b>{$rowFields['engName']}</b><br /><i>NOTE: Passwords will not appear when editing records. To leave the password unchanged leave this field blank. To change a password enter it here.</i>";
		echo "</td>\n";
		echo "<td>\n";
		echo "<input type='text' size='100' id='P{$rowFields['name']}' name='P{$rowFields['name']}' value='' {$rowFields['param']} />";
		echo "</td>\n";
		echo "</tr>\n";
		break;
	case 'textarea':
		echo "<tr class='tr$r'>\n";
		echo "<td>\n";
		echo "<b>{$rowFields['engName']}</b>";
		echo "</td>\n";
		echo "<td>\n";
		echo "<textarea id='T{$rowFields['name']}' name='T{$rowFields['name']}' " . ($rowFields['param'] ? $rowFields['param'] : "cols='76' rows='10'") . ">{$rowFetch[$rowFields['name']]}</textarea>";
		echo "</td>\n";
		echo "</tr>\n";
		break;
	case 'num_select':
		echo "<tr class='tr$r'>\n";
		echo "<td>\n";
		echo "<b>{$rowFields['engName']}</b>";
		echo "</td>\n";
		echo "<td>\n";
		$opt = explode(":",$rowFields['param']);
		$opts = sizeof($opt);
		echo "<select id='N{$rowFields['name']}' name='N{$rowFields['name']}'>";
		for ($x = 0; $x < $opts; $x+= 2){
			echo "<option value='{$opt[$x]}' " . ($opt[$x] == $rowFetch[$rowFields['name']] ? "selected" : "") . ">{$opt[$x+1]}</option>\n";
		}
		echo "</select>";
		echo "</td>\n";
		echo "</tr>\n";
		break;
	case 'alpha_select':
		echo "<tr class='tr$r'>\n";
		echo "<td>\n";
		echo "<b>{$rowFields['engName']}</b>";
		echo "</td>\n";
		echo "<td>\n";
		$opt = explode(":", $rowFields['param']);
		$opts = sizeof($opt);
		echo "<select id='A{$rowFields['name']}' name='A{$rowFields['name']}'>";
		for ($x = 0; $x < $opts; $x+= 2){
			echo "<option value='{$opt[$x]}' " . ($opt[$x] == $rowFetch[$rowFields['name']] ? "selected" : "") . ">{$opt[$x+1]}</option>\n";
		}
		echo "</select>";
		echo "</td>\n";
		echo "</tr>\n";
		break;
	case 'ro':
		echo "<tr class='tr$r'>\n";
		echo "<td>\n";
		echo "<b>{$rowFields['engName']}</b>";
		echo "</td>\n";
		echo "<td>\n";
		echo $rowFetch[$rowFields['name']];
		echo "</td>\n";
		echo "</tr>\n";
		break;
	case 'div':
		echo "<tr class='tr$r'>\n";
		echo "<td colspan='2'>\n";
		echo "<b>{$rowFields['param']}</b><hr>";
		echo "</td>\n";
		echo "</tr>\n";
		break;
	case 'alpha_module':
	case 'long_module':
	case 'num_module':
		echo "<tr class='tr$r'>\n";
		echo "<td>\n";
		echo "<b>{$rowFields['engName']}</b>";
		echo "</td>\n";
		echo "<td>\n";
		$param = explode("|", $rowFields['param']);
		foreach($param as $v){
			$opt = explode(":", $v);
			$_MODULE[$opt[0]] = $opt[1]; 
		}
		include ("_mod/{$_MODULE['file']}/index.php");
		echo "</td>\n";
		echo "</tr>\n";
		break;
	case 'timeNow':
		$t = ($rowFetch[$rowFields['name']] ? $rowFetch[$rowFields['name']] : time() + $_SITE['server_offset']);
		
		$offset = ($rowFetch[$rowFields['name']] ? 0 : $_SITE['server_offset']);

		echo "<tr class='tr$r'>\n";
		echo "<td>\n";
		echo "<b>{$rowFields['engName']}</b>";
		echo "</td>\n";
		echo "<td>\n";
		echo "<select id='day{$rowFields['name']}' onChange='makeTime(\"{$rowFields['name']}\")'>";
		for ($x=1; $x <= 31; $x++){
			echo "<option value=$x " . (date('j', $t) == $x ? "selected" : "") . ">$x</option>\n";
		}
		echo "</select>";
		echo "<select id='month{$rowFields['name']}' onChange='makeTime(\"{$rowFields['name']}\")'>";
		echo "<option value='January' " . (date('F', $t) == 'January' ? "selected" : "") . ">January</option>";
		echo "<option value='February' " . (date('F', $t) == 'February' ? "selected" : "") . ">February</option>";
		echo "<option value='March' " . (date('F', $t) == 'March' ? "selected" : "") . ">March</option>";
		echo "<option value='April' " . (date('F', $t) == 'April' ? "selected" : "") . ">April</option>";
		echo "<option value='May' " . (date('F', $t) == 'May' ? "selected" : "")  . ">May</option>";
		echo "<option value='June' " . (date('F', $t) == 'June' ? "selected" : "")  . ">June</option>";
		echo "<option value='July' " . (date('F', $t) == 'July' ? "selected" : "")  . ">July</option>";
		echo "<option value='August' " . (date('F', $t) == 'August' ? "selected" : "")  . ">August</option>";
		echo "<option value='September' " . (date('F', $t) == 'September' ? "selected" : "")  . ">September</option>";
		echo "<option value='October' " . (date('F', $t) == 'October' ? "selected" : "")  . ">October</option>";
		echo "<option value='November' " . (date('F', $t) == 'November' ? "selected" : "")  . ">November</option>";
		echo "<option value='December' " . (date('F', $t) == 'December' ? "selected" : "")  . ">December</option>";
		echo "</select>";
		echo "<input autocomplete='off' type='text' id='year{$rowFields['name']}' maxlength='4' size='4' onKeyUp='makeTime(\"{$rowFields['name']}\")' value=" . date('Y', $t) . " />";
		echo "&nbsp;&nbsp;Time: ";
		echo "<input autocomplete='off' type='text' id='hour{$rowFields['name']}' maxlength='2' size='1' onKeyUp='makeTime(\"{$rowFields['name']}\")' value=" . date('G', $t) . " />:";
		echo "<input autocomplete='off' type='text' id='minute{$rowFields['name']}' maxlength='2' size='1' onKeyUp='makeTime(\"{$rowFields['name']}\")' value=" . date('i', $t) . " />";


		echo "<input type='hidden' id='N{$rowFields['name']}' name='N{$rowFields['name']}' value='$t' />";
		echo "</td>\n";
		break;							
	case 'file':
		echo "<tr class='tr$r'>\n";
		echo "<td>\n";
		echo "<b>Current {$rowFields['engName']}</b>";
		echo "</td>\n";
		echo "<td>\n";
		echo ($rowFetch[$rowFields['name']] ? "<a target='_blank' href='{$_SITE['base_href']}{$rowFetch[$rowFields['name']]}'>Click here</a><input type='checkbox' name='XDelA{$rowFields['name']}' value='delete' />&nbsp;Delete?<input type='hidden' name='XPathA{$rowFields['name']}' value='{$rowFetch[$rowFields['name']]}' />" : "<i>none...</i>");
		echo "</td>\n";
		echo "</tr>\n";
		echo "<tr class='tr$r'>\n";
		echo "<td>\n";
		echo "<b>New {$rowFields['engName']}</b>";
		echo "</td>\n";
		echo "<td>\n";
		echo "<table>\n";
		echo "<tr><td>Use asset:</td><td><select name='A{$rowFields['name']}'>";
		echo "<option value='{$rowFetch[$rowFields['name']]}'>--NONE--</option>";
		$run = runSql("SELECT * FROM _assets");
		while($row = mysql_fetch_assoc($run)){
			echo "<option value='{$row['path']}' " . ($row['path'] == $rowFetch[$rowFields['name']] ? "selected" : "") . ">{$row['title']}</option>";
		}
		echo "</select></td></tr>";
		echo "<tr><td>Upload New:</td><td><input type='file' id='A{$rowFields['name']}' name='A{$rowFields['name']}' value='{$rowFetch[$rowFields['name']]}' {$rowFields['param']} /></td></tr></table>";
		
		list($path, $h, $w) = explode(":", $rowFields['param']);
		
		echo "<input type='hidden' id='XDIRA{$rowFields['name']}' name='XDIRA{$rowFields['name']}' value='$path' />";
		echo "<input type='hidden' id='XHEIGHT{$rowFields['name']}' name='XHEIGHT{$rowFields['name']}' value='$h' />";
		echo "<input type='hidden' id='XWIDTH{$rowFields['name']}' name='XWIDTH{$rowFields['name']}' value='$w' />";
		echo "</td>\n";
		echo "</tr>\n";
		break;
		
		default:
			echo "<b>Field error. Please check that steamEngine is up to date</b>";
			break;
	}

}



////////////////////////////////////////////////////////////////////////
// Start Main Page
////////////////////////////////////////////////////////////////////////

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
	<head profile="http://gmpg.org/xfn/11">
		<link rel="stylesheet" type="text/css" href="style.css" />
		<script language="javascript" type="text/javascript" src="tinymce/jscripts/tiny_mce/tiny_mce.js"></script>

		<script type="text/javascript" src="javascript/jquery-1.2.6.min.js"></script>
		<script language="javascript" type="text/javascript">

		var modArray = new Array();
			<?
			$runMods = runSql("select * from `_module`");
			if (mysql_num_rows($runMods)){
				while ($rowMods = mysql_fetch_assoc($runMods)){
					echo "modArray['{$rowMods['name']}'] = new Array();\r\n";
					$p = explode("|", $rowMods['param']);
					echo "  modArray['{$rowMods['name']}'][0] = 'file:{$rowMods['file']}|type:{$rowMods['type']}|';\r\n";
					$x = 1;
					foreach ($p as $v){
						echo "  modArray['{$rowMods['name']}'][$x] = '$v';\r\n";
						$x++;
					}
				}
			}
			?>



		$(document).ready(function(){

			// jQuery for the permission button
			$("a.Perm").click(function(event){
				event.preventDefault();
				if ($(this).css("color") == "rgb(204, 204, 204)" || $(this).css("color") == "#ccc"){
					$(this).css("color", "#000");
					document.getElementById("perm").value = parseInt(document.getElementById("perm").value) + parseInt(this.id);
				}else{
					$(this).css ("color", "#ccc");
					document.getElementById("perm").value = parseInt(document.getElementById("perm").value) - parseInt(this.id);
				}
			});
	
			$("input").focus(function(event){
				$(this).toggleClass("inputOn");
			});
			$("input").blur(function(event){
				$(this).toggleClass("inputOn");
			});
			$("select").focus(function(event){
				$(this).toggleClass("inputOn");
			});
			$("select").blur(function(event){
				$(this).toggleClass("inputOn");
			});

			// jQuery for the user messages
			$("div.adminMSG").fadeTo(10000, 0, function(){
				$("div.adminMSG").slideUp();
			});

			$("div.adminMSGBad").fadeTo(10000, 0, function(){
				$("div.adminMSGBad").slideUp();
			});
		});

		// Initialize timyMCE
		tinyMCE.init({
			theme : "advanced",
			mode : "textareas",
			plugins : "table",
			theme_advanced_buttons3_add : "tablecontrols"
		});

		// Changes the current time form data to an epoch date.
		function makeTime(id){
			var myDate = new Date(document.getElementById('month' + id).value + " " + document.getElementById('day' + id).value + ", " + document.getElementById('year' + id).value + " " + document.getElementById('hour' + id).value + ":" + document.getElementById('minute' + id).value + ":00"); // Your timezone!
			//alert (offset);
			var myEpoch = (myDate.getTime()/1000.0);
			document.getElementById('N' + id).value = myEpoch;
		}

		// Write the parameter fields when a module is chosen
		function changeParam(id){
			fields = modArray[id].length;
			write = "<table>";
			for (r = 1; r < fields; r++){
				write = write + "<tr><td>" + modArray[id][r] + "</td><td><input type='text' id='" + modArray[id][r] + "' onkeyUp='modParamUpdate(\"" + id + "\")' /></td></tr>";
			}
			write = write + "</table>";
			write = write + "<input type='hidden' id='Aparam' name='Aparam' value='' />";
			document.getElementById('parameters').innerHTML = write;
		}

		// Write the parameters to a hidden text file.
		function modParamUpdate(id){
			fields = modArray[id].length;
			document.getElementById('Aparam').value = modArray[id][0];
			for (r = 1; r < fields; r++){
				document.getElementById('Aparam').value += modArray[id][r] + ":" + document.getElementById(modArray[id][r]).value + "|";
			}	
		}

		// Confirm the deleting of a record
		function deleteConfirm(x){
			var answer = confirm ("This will delete this record")
			if (answer){
				x.submit();	
			}
			
		}	

		// Write notes for field types.
		function paramNote(val){
			////////////////////////////////////////////////////////////////////////
			//Show the user notes about the parameters section
			////////////////////////////////////////////////////////////////////////

			if (val == 'text' || val == 'float' || val == 'number' || val == 'password' || val == 'textarea'){
				msg = "Put in any parameters here for the text box or text area.<br /><br /><i>Example: maxlength='2' size='100' or cols='29' rows='5'</i>";
			}else if (val == 'alpha_select' || val == 'num_select'){
				msg = "The parameter box holds the options for the select box seperated by a ':'. Value first, Option second<br /><br /><i>Example: 1:One:2:Two:3:Three</i>";
			}else if (val == 'alpha_module' || val == 'num_module' || val == 'long_module'){
				msg = "The parameter box holds the options for the module. Please refer to the module's documentation for the correct parameters. 'file' will be a constant parameter and it will be the name of the directory that the files are found in your _mod directory. Each parameter and value are seperated with a ':' and each set of parameter and value are seperated with a '|'<br /><br /><i>Example: file:mod_directory|p1:100|value:apples</i>";
			}else if (val == 'file'){
				msg = "The parameter box holds the path to where the file will be uploaded to relative to your ftp href and base HREF<br />(Your current base href is: <b><?=$_SITE['ftp_base_href'];?><?=$_SITE['base_href'];?></b>)<br /><br /><i>Example: Title</i>";
			}else if (val == 'div'){
				msg = "The parameter box holds the title of the divider";
			}else{
				msg = "This option has no parameters.";
				changeParam(val);
			}
			document.getElementById('paramNote').innerHTML = msg;
			
		}

		//Turning on options on `pages`

		function pageOnOff(type, state){
			if (state == 1){
				document.getElementById(type + 'Options').style.display = 'block';
			}else{
				document.getElementById(type + 'Options').style.display = 'none';
			}
		}

		</script>
		<title><? echo $_SITE['title']; ?> - Powered by steamEngine v0.99</title>
	</head>
	<body>
<?
	if ($_SESSION['uid' . $_SERVER['SERVER_ADDR']]){
		////////////////////////////////////////////////////////////////////////
		// Logged in. Greet the user.
		////////////////////////////////////////////////////////////////////////
		$run = runSql("SELECT `username` FROM `{$_SITE['users_table']}` WHERE `id` = {$_SESSION['uid' . $_SERVER['SERVER_ADDR']]}");
		$row = mysql_fetch_assoc($run);
		echo "<div class='loginBar'><div class='loginBarBox'><img src='img/steamLogo.png' /></a></div>";
		echo "<div class='loginBarBox' style='float:right; font-weight:bold;padding-top:3px;'>Logged in as {$row['username']} (<a href='?act=logout'>Logout</a>)</a></div></div>";
	}
?>
	<div class="whole">
<? 
		//////////////////////////////////////////////////////////////////		
		// Send messages to the user
		//////////////////////////////////////////////////////////////////		

		if (is_array($msgBad)){
			foreach ($msgBad as $v){
				echo "<div class='adminMSGBad'>$v</div><div class='clear'>&nbsp;</div>";
			}
		}

		if (is_array($msg)){
			foreach ($msg as $v){
				echo "<div class='adminMSG'>$v</div><div class='clear'>&nbsp;</div>";
			}
		}

		////////////////////////////////////////////////////////////////////////
		// Init Mod will run before any of the main code 
		// the directory specified should have both an indexTOP.php and indexBOT.php 
		////////////////////////////////////////////////////////////////////////

		if ($_SITE['initMod']){
			$param = explode("|", $_SITE['initMod']);
			foreach($param as $v){
				$opt = explode(":", $v);
				$_MODULE[$opt[0]] = $opt[1]; 
			}
			include ("_mod/{$_MODULE['file']}/indexTOP.php");	       
		}

		
		////////////////////////////////////////////////////////////////////////
		// Login page
		////////////////////////////////////////////////////////////////////////

		if (!$_SESSION['uid' . $_SERVER['SERVER_ADDR']]){
			echo "<div class='loginBox'>\n";
			echo "<div class='loginHead'>\n";
			echo $_SITE['title'];
			echo "</div>\n";
			echo "<div class='loginForm'>\n";
			echo "<form method='post'>";
			echo "<table>\n";
			echo "<tr>\n";
			echo "<td>Username</td>\n";
			echo "<td><input type='text' name='_uname' maxlength='150' /></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td>Password</td>\n";
			echo "<td><input type='password' name='_pass' maxlength='150' /></td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td colspan='2'><input type='submit' value='Login' /></td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			echo "</form>\n";
			echo "</div>\n";
			echo "</div>\n";

		}else{
				////////////////////////////////////////////////////////////////////////
				// Main menu
				////////////////////////////////////////////////////////////////////////
				echo "<div style='float:left; width:230px; text-align:center;'>";
				echo ($_SITE['logo'] ? "<img src='{$_SITE['logo']}' />" : "");
				echo "<ul class='menu'>\n";
				echo "<li class='menuTitle'>Pages</li>";
				$run = runSql("SELECT * FROM `_pages`");
				while ($row = mysql_fetch_assoc($run)){
					if (adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']], $row['permission'])){
						if ($row['file'] == ""){
							echo "<li " . ($form_page == $row['table'] ? "class='menuon'" : "" ) . "><a href='?page={$row['table']}'>{$row['name']}</a></li>\n";
						}else{
							echo "<li><a href='{$row['file']}'>{$row['name']}</a></li>\n";
						}
					}
				}
				////////////////////////////////////////////////////////////////////////
				// If they're have the permission, give them the files page
				////////////////////////////////////////////////////////////////////////
				
				echo (adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']], $_SITE['files_permission']) ? "<li " . ($form_page == "_assets" ? "class='menuon'" : "" ) . "><a href='?page=_assets'>Assets</a></li>\r\n" : "");
				////////////////////////////////////////////////////////////////////////
				// If they're an admin, give them the site options.
				////////////////////////////////////////////////////////////////////////

				echo "<li class='menuTitle'>Functions</li>";
				echo "<li>" . ($_SESSION['bkp']['XXXundo_desc'] ? "<a href='?page=_undo'>Undo</a>" : "<span style='color:#ccc;'>Undo</span>") . "</li>";
				echo "<li><a target='_blank' href='{$_SITE['base_url']}{$_SITE['base_href']}'>View Site &raquo;</a></li>";
				echo (adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']], $_SITE['admin_level']) ? "<li class='menuTitle'>Admin</li>" : "");
				echo (adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']], $_SITE['admin_level']) ? "<li " . ($form_page == "_pages" ? "class='menuon'" : "" ) . "><a href='?page=_pages'>Pages</a></li>\r\n<li " . ($form_page == "_fields" ? "class='menuon'" : "" ) . "><a href='?page=_fields'>Fields</a></li>\r\n<li " . ($form_page == "_module" ? "class='menuon'" : "" ) . "><a href='?page=_module'>Modules</a></li>\r\n<li " . ($form_page == "_errlog" ? "class='menuon'" : "" ) . "><a href='?page=_errlog'>Error Log</a></li>\r\n" : "" );
				echo "</ul>\n";
				echo "</div>";
						
				echo "<div style='float:left;width:770px;'>\n";

				////////////////////////////////////////////////////////////////////////
				// Start the serious stuff: if there's a form_page, we're on an edit form
				////////////////////////////////////////////////////////////////////////

				if ($form_page){
					if ($form_id){
						$run = runSql("select * from `$form_page` where `id` = $form_id");
						$rowFetch = mysql_fetch_assoc($run);

					}
					////////////////////////////////////////////////////////////////////////
					// Get all the fields for this page
					////////////////////////////////////////////////////////////////////////
	
					$runFields = runSql("SELECT * FROM `_fields` WHERE `page` = (SELECT `id` FROM `_pages` WHERE `table` = '{$form_page}') ORDER BY `order` ASC");
					$runPage = runSql("SELECT `permission` FROM `_pages` WHERE `table` = '$form_page'");
					$rowPage = mysql_fetch_assoc($runPage);
					$pagePermission = $rowPage['permission'];

					////////////////////////////////////////////////////////////////////////
					// Start the form 
					////////////////////////////////////////////////////////////////////////
							
					if ($form_page == '_undo'){

						////////////////////////////////////////////////////////////////////////
						// Undo Page
						////////////////////////////////////////////////////////////////////////

						echo "<h3>Undo last update:</h3><p>The last action steamEngine preformed was <i>{$_SESSION['bkp']['XXXundo_desc']}</i></p>";
						echo "<p>The form below has the previous value of that record filled out. Click undo to revert this record back to the displayed data</p>";
						echo "<form method='post' enctype='multipart/form-data'>";
						
						echo "<input type='hidden' value='{$_SESSION['bkp']['Xtab']}' name='Xtab' />";
						echo "<input type='hidden' value='{$_SESSION['bkp']['Xid']}' name='Xid' />";
						echo "<input type='hidden' value='1' name='Xundo-restore' />";

						$runFields = runSql("select a.* from `_fields` as a left join `_pages` as b on a.page = b.id where b.table = '{$_SESSION['bkp']['Xtab']}'  ORDER BY `order` ASC");
						echo "<table valign='top' cellspacing='0' cellpadding='5'>";
						echo "<tr><td width='350'>&nbsp;</td><td>&nbsp;</td></tr>";
						$r = 1;
						while ($rowFields = mysql_fetch_assoc($runFields)){
							writePage($rowFields, $_SESSION['bkp'], $r);
							if ($r == 1){ $r=2; }else{ $r = 1; }
						}
						echo "</table>";
						
						echo "<input class='button greenButton' type='submit' value='Undo' name='Xsubmit' />";
						echo "</form>";
					}elseif (adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']], $pagePermission) || 
						(
							($form_page == '_assets' && adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']], $_SITE['files_permission'])) || 
							($form_page == '_pages' || $form_page == '_fields' || $form_page == '_module'|| $form_page == '_errlog') && adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']], $_SITE['admin_level'])
							
						)
					){
						echo "<form method='post' enctype='multipart/form-data'>";
						echo "<input type='hidden' name='Xid' value='{$rowFetch['id']}' />";
						echo "<input type='hidden' name='Xtab' value='{$form_page}' />";
						echo "<table valign='top' width='900'  cellspacing='0' cellpadding='5'>";
						//echo "<tr><td width='350'><b>Name</b></td><td><b>Value</b></td></tr>";
						echo "<tr><td width='350'>&nbsp;</td><td>&nbsp;</td></tr>";
	
						if ($form_page == "_errlog" && adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']],  $_SITE['admin_level'])){

							////////////////////////////////////////////////////////////////////////
							// Error Log page
							////////////////////////////////////////////////////////////////////////
							
							if ($form_clear == 1){
								runSql("TRUNCATE TABLE `_errlog`");
							}

							$run = runSql("select * from `_errlog` order by `id` DESC");
							echo "<p><a href='?page=_errlog&clear=1'>[ CLEAR ERRORS ]</a></p>";
							while($rowERR = mysql_fetch_assoc($run)){
								echo "<tr><td>{$rowERR['type']}</td><td>{$rowERR['desc']}</td></tr>";
							}

						}elseif ($form_page == "_pages" && adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']],  $_SITE['admin_level'])){

							////////////////////////////////////////////////////////////////////////
							// This is the admin pages section. It contols all the pages in the CMS
							////////////////////////////////////////////////////////////////////////
							
							echo "<input type='hidden' name='Xadmin' value='page' />";
							echo "<tr>\n";
							echo "<td colspan='2'>\n";
							echo "<strong>WARNING:</strong> These admin pages can drastically effect the site. If you don't know EXACTLY what you're doing I'd suggest you don't mess around in here.<br />These pages are not supported in the 'Undo' function<br />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td width='350'>\n";
							echo "<b>Name</b>";
							echo "</td>\n";
							echo "<td width='546'>\n";
							echo "<input type='text' name='Aname' value='{$rowFetch['name']}' />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Description</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<input type='text' name='Adesc' size='70' maxlength='255' value='{$rowFetch['desc']}' />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Permission</b>";
							echo "</td>\n";
							echo "<td>\n";
							$run = runSql("SELECT * from _permission order by value DESC");
							
							while($rowP = mysql_fetch_assoc($run)){
								echo "<a class='Perm' style='color:#" . (permCheck(($rowFetch['permission'] ? $rowFetch['permission'] : "0"), $rowP['value']) ? "000" : "ccc" ) . "' id='{$rowP['value']}'>{$rowP['name']}</a><br />";
							}
							echo "<input id='perm' type='hidden' name='Npermission' value='" . ($rowFetch['permission'] ? $rowFetch['permission'] : "0") . "' />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td colspan='2'>\n";
							echo "<b>Type of page</b><hr>";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Path to File</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<input type='text' name='Afile' value='{$rowFetch['file']}' />";
							echo "</td>\n";
							echo "</tr>";	
							echo "<tr>\n";
							echo "<td colspan='2'>\n";
							echo "<center><b>-- OR --</b></center>";
							echo "</td>\n";
							echo "</tr>\n";		
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Table</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<input type='text' name='Atable' value='{$rowFetch['table']}' />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td colspan='2'>\n";
							echo "<b>Standard Page Options</b><hr>";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Edit previous entries box</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<select name='Neditbox' onchange='pageOnOff(\"edit\", this.value);'>";
							echo "<option " . ($rowFetch['editbox'] == "0" ? "selected" : "") . " value='0'>No</option>";
							echo "<option " . ($rowFetch['editbox'] == "1" ? "selected" : "") . " value='1'>Yes</option>";
							echo "</select>";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Allow Search</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<select name='Nsearch'>";
							echo "<option " . ($rowFetch['search'] == "0" ? "selected" : "") . " value='0'>No</option>";
							echo "<option " . ($rowFetch['search'] == "1" ? "selected" : "") . " value='1'>Yes</option>";
							echo "</select>";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>RSS feed</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<select name='Nrss' onchange='pageOnOff(\"rss\", this.value);'>";
							echo "<option " . ($rowFetch['rss'] == "0" ? "selected" : "") . " value='0'>No</option>";
							echo "<option " . ($rowFetch['rss'] == "1" ? "selected" : "") . " value='1'>Yes</option>";
							echo "</select>";
							echo "</td>\n";
							echo "</tr>\n";
							echo "</table>";
							echo "<table width='900' id='rssOptions' style='display:none;'>";
							echo "<tr>\n";
							echo "<td colspan='2'>\n";
							echo "<b>RSS Options</b><br /><i>Optional: These options customize your RSS feed for this page</i><hr>";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td width='350'>\n";
							echo "<b>RSS Title</b>";
							echo "</td>\n";
							echo "<td width='546'>\n";
							echo "<input type='text' name='ArssTitle' value='{$rowFetch['rssTitle']}' />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>RSS Description</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<input type='text' name='ArssDesc' value='{$rowFetch['rssDesc']}' />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>RSS link</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<input type='text' name='ArssLink' value='{$rowFetch['rssLink']}' />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "</table>";
							echo "<table width='900' id='editOptions' style='display:none;'>";
							echo "<tr>\n";
							echo "<td colspan='2'>\n";
							echo "<b>Edit Previous Entry box options</b><br /><i>Optional: This will sort the results in the 'Edit previous entries' box by this field and give them headdings making entries easier to find.</i><hr>";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td width='350'>\n";
							echo "<b>Order By Field</b>";
							echo "</td>\n";
							echo "<td width='546'>\n";
							echo "<input type='text' name='Aorder_field' value='{$rowFetch['order_field']}' />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Order Direction</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<select name='Aorder_dir'>";
							echo "<option " . ($rowFetch['order_dir'] == "asc" ? "selected" : "") . " value='ASC'>Ascending</option>";
							echo "<option " . ($rowFetch['order_dir'] == "desc" ? "selected" : "") . " value='DESC'>Descending</option>";
							echo "</select>";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Field to show in the edit box</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<input type='text' name='Aorder_show' value='{$rowFetch['order_show']}' />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Secondary field to show in the edit box</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<input type='text' name='Aorder_show2' value='{$rowFetch['order_show2']}' />";
							echo "</td>\n";
							echo "</tr>\n";

							echo "<tr>\n";
							echo "<td colspan='2'>\n";
							echo "<b>Advance Entry box sorting options</b><br /><i>Optional: These options will help sort the entries by a specified field and can be linked to another table for id values.</i><hr>";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Sort Linked Table</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<select name='Asort_link_table'>";
							echo "<option value=''>--SELECT--</option>";
							$run = runSql("SELECT * FROM _pages");
							while($row = mysql_fetch_assoc($run)){
								echo "<option value='{$row['table']}' " . ($row['table'] == $rowFetch['sort_link_table'] ? "selected" : "") . ">{$row['name']}</option>";
							}
							echo "</select>";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Sort Field</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<input type='text' name='Asort_field' value='{$rowFetch['sort_field']}' />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Sort Linked Field</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<input type='text' name='Asort_link_field' value='{$rowFetch['sort_link_field']}' />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Sort Linked Display</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<input type='text' name='Asort_link_display' value='{$rowFetch['sort_link_display']}' />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "</table>\n";

							echo "<script>";
							echo "pageOnOff('rss', " . ($rowFetch['rss'] ? $rowFetch['rss'] : 0) . ");\n";
							echo "pageOnOff('edit', " . ($rowFetch['editbox'] ? $rowFetch['editbox'] : 0) . ");\n";
							echo "</script>";

							echo "<table>\n";
						}elseif ($form_page == "_module"){
							////////////////////////////////////////////////////////////////////////
							// Modules page for installing modules
							////////////////////////////////////////////////////////////////////////
							echo "<tr><td colspan=2><b>WARNING!</b><br/>Modules have access to a lot of the inner workings of steamEngine and thus are very helpful. With that level of access comes a similar level of threat. Only install and use modules from credible sources. If you have any reason to distrust the author or the module itself, DO NOT USE IT. Any damage caused through the use of modules is entirely the fault of the user installing it.</td></tr>";
							if ($form_id){
								echo "<tr>\n";
								echo "<td>\n";
								echo "<b>Name</b>";
								echo "</td>\n";
								echo "<td>\n";
								echo $rowFetch['engName'];
								echo "</td>\n";
								echo "</tr>\n";	
								echo "<tr>\n";
								echo "<td>\n";
								echo "<b>TYPE</b>";
								echo "</td>\n";
								echo "<td>\n";
								switch ($rowFetch['type']){
									case ("A"):
										echo "Alphabetical";
										break;
									case ("N"):
										echo "Numeric";
										break;
									case ("T"):
										echo "Long Text";
										break;
								}
								echo "</select>";
								echo "</td>\n";
								echo "</tr>\n";								
								echo "<tr>\n";
								echo "<td>\n";
								echo "<b>Path to File</b>";
								echo "</td>\n";
								echo "<td>\n";
								echo "_mod/{$rowFetch['file']}";
								echo "</td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td>\n";
								echo "<b>Parameters</b>";
								echo "</td>\n";
								echo "<td>\n";
								$p = explode("|", $rowFetch['param']);
								$d = explode("|", $rowFetch['desc']);
								echo "<table>";
								for ($z = 0; $z < sizeof($p); $z++){
									echo "<tr valign='top'><td><b>{$p[$z]}</b></td><td>{$d[$z]}</td></tr>";
								}
								echo "</table>";
								echo "</td>\n";
								echo "</tr>\n";	
							}else{
								echo "<tr>\n";
								echo "<td colspan='2'>\n";
								echo "<b>Install new Module</b><hr>";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td>\n";
								echo "Path to module";
								echo "</td>\n";
								echo "<td>\n";
								echo "_mod/<input type='text' name='Xinstall' value='' />";
								echo "</td>\n";
								echo "</tr>\n";
							}

						
						}elseif ($form_page == "_assets"){
							////////////////////////////////////////////////////////////////////////
							// Assets page for adding files.
							////////////////////////////////////////////////////////////////////////

							echo "<tr>\n";
							echo "<td colspan='2'>\n";
							echo "<h3>Assets</h3>";
							echo "<p>The Assets page is used to store images, pdfs and other files for your site that don't belong to a particular entry.</p>";
							echo "<input type='hidden' name='Xtab' value='_assets'/>";
							echo "<input type='hidden' name='Xid' value='$form_id'/>";
							echo "</td>\n";
							echo "</tr>\n";
							if ($form_id){
								echo "<tr>\n";
								echo "<td>\n";
								echo "<b>Current File:</b>";
								echo "</td>\n";
								echo "<td>\n";
								echo "<a target='_blank' href='{$_SITE['base_href']}{$rowFetch['path']}'>Click here.</a>";
								echo "</td>\n";
								echo "</tr>\n";
							}
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Title</b>\n";
							echo "</td>\n";
							echo "<td>\n";
							echo "<input type='text' value='{$rowFetch['title']}' name='Atitle' />\n";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Upload Path:</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "{$_SITE['ftp_base_href']}{$_SITE['base_href']}<input type='text' name='XDIRFupload' value='{$_SITE['files_default_dir']}' />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>File:</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<input type='file' name='Fupload' />";
							echo "</td>\n";
							echo "</tr>\n";
						
						}elseif ($form_page == "_fields"){
							////////////////////////////////////////////////////////////////////////
							// This is the fields section of the admin panel. It controlles the fields in the pages 
							////////////////////////////////////////////////////////////////////////
							echo "<input type='hidden' name='Xadmin' value='field' />";
							echo "<tr>\n";
							echo "<td colspan='2'>\n";
							echo "<strong>WARNING:</strong> These admin pages can drastically effect the site. If you don't know EXACTLY what you're doing I'd suggest you don't mess around in here.<br />These pages are not supported in the 'Undo' function<br />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Page</b>";
							echo "</td>\n";
							echo "<td>\n";
							if (!$form_id){
							echo "<select name='Npage'>";
							echo "<option value=''>--SELECT--</option>";
							$run = runSql("SELECT * FROM _pages");
							while($row = mysql_fetch_assoc($run)){
								echo "<option value='{$row['id']}' " . ($row['id'] == $rowFetch['page'] ? "selected" : "") . ">{$row['name']}</option>";
							}
							echo "</select>";
							}else{
								$run = runSql("SELECT * FROM _pages where `id` = {$rowFetch['page']}");
								$row = mysql_fetch_assoc($run);
								echo $row['name'];
								echo "<input type='hidden' name='Npage' value='{$rowFetch['page']}' />";
							}
							echo "</td>\n";
							echo "</tr>\n";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Type</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<select name='Atype' id='Atype' onChange='paramNote(this.value);'>";
							echo "<optgroup label='Basic'>";
							echo "<option " . ($rowFetch['type'] == "text" ? "selected" : "") . " value='text'>Text</option>";
							echo "<option " . ($rowFetch['type'] == "number" ? "selected" : "") . " value='number'>Number</option>";
							echo "<option " . ($rowFetch['type'] == "float" ? "selected" : "") . " value='float'>Float</option>";
							echo "<option " . ($rowFetch['type'] == "textarea" ? "selected" : "") . " value='textarea'>Text Area</option>";
							echo "<option " . ($rowFetch['type'] == "password" ? "selected" : "") . " value='password'>Password</option>";
							echo "<option " . ($rowFetch['type'] == "file" ? "selected" : "") . " value='file'>File</option>";
							echo "</optgroup>";
							echo "<optgroup label='Select'>";
							echo "<option " . ($rowFetch['type'] == "alpha_select" ? "selected" : "") . " value='alpha_select'>Alpha Select</option>";
							echo "<option " . ($rowFetch['type'] == "num_select" ? "selected" : "") . " value='num_select'>Numeric Select</option>";
							echo "</optgroup>";
							$runMods = runSql("select * from `_module`");
							if (mysql_num_rows($runMods)){
								echo "<optgroup label='Installed Modules'>";
								while ($rowMods = mysql_fetch_assoc($runMods)){
									switch (strtolower($rowMods['type'])){
										case ("n"):
											$type = "num_module";
											break;
										case ("a"):
											$type = "alpha_module";
											break;
										case ("l"):
											$type = "long_module";
										break;
									}
									echo "<option onClick='changeParam(\"{$rowMods['name']}\")' " . ($rowFetch['type'] == $rowMods['name'] ? "selected" : "") . " value='$type'>{$rowMods['engName']}</option>";
								}
								echo "</optgroup>";
							}
							echo "<optgroup label='Misc.'>";
							echo "<option " . ($rowFetch['type'] == "timeNow" ? "selected" : "") . " value='timeNow'>Current time</option>";
							echo "<option " . ($rowFetch['type'] == "ro" ? "selected" : "") . " value='ro'>Read Only</option>";
							echo "<option " . ($rowFetch['type'] == "div" ? "selected" : "") . " value='div'>Divider</option>";
							echo "</optgroup>";
							echo "<optgroup label='Module'>";
							echo "<option " . ($rowFetch['type'] == "alpha_module" ? "selected" : "") . " value='alpha_module'>Alpha Module</option>";
							echo "<option " . ($rowFetch['type'] == "num_module" ? "selected" : "") . " value='num_module'>Numeric Module</option>";
							echo "<option " . ($rowFetch['type'] == "long_module" ? "selected" : "") . " value='long_module'>Long Text Module</option>";
							echo "</optgroup>";
							echo "</select>";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Field Name</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<input type='text' name='Aname' value='{$rowFetch['name']}' />";
							echo "<input type='hidden' name='Xname' value='{$rowFetch['name']}' />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Full Field name</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<input type='text' name='AengName' value='{$rowFetch['engName']}' />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Order</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<input type='text' name='Norder' value='{$rowFetch['order']}' />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Place in RSS feed (if applicable)</b>";
							echo "</td>\n";
							echo "<td>\n";
							echo "<select name='Nrss'>";
							echo "<option " . ($rowFetch['rss'] == "0" ? "selected" : "") . " value='0'>---NONE---</option>";
							echo "<option " . ($rowFetch['rss'] == "1" ? "selected" : "") . " value='1'>Title</option>";
							echo "<option " . ($rowFetch['rss'] == "2" ? "selected" : "") . " value='2'>Content</option>";
							echo "</select>";
							echo "</td>\n";
							echo "</tr>\n";
							echo "<tr>\n";
							echo "<td>\n";
							echo "<b>Parameters</b>";
							echo "</td>\n";
							echo "<td id='parameters'>\n";
							echo "<input type='text' name='Aparam' value='{$rowFetch['param']}' />";
							echo "<p id='paramNote'></p>";
							echo "</td>\n";
							echo "</tr>\n";
	
							echo "<tr>\n";
						}else{
	
							////////////////////////////////////////////////////////////////////////
							// Now on to the user created pages. Here we rotate though all the
							// fields in a page and writes the form based on the data. 
							////////////////////////////////////////////////////////////////////////

							$runPage = runSql("select `id`, `name`, `desc` from `_pages` where `table` = '$form_page'");
							$page = mysql_fetch_assoc($runPage);
							echo "<p><b>{$page['name']}</b>" . (adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']], $_SITE['admin_level']) ? " - <a href='?page=_pages&id={$page['id']}'>[ edit ]</a>" : "") . "</p>";
							echo "<p>{$page['desc']}</p>";
							if (!mysql_num_rows($runFields)){
								echo "<p>This page is empty, click <a href='?page=_fields'>here</a> to add fields to this page.</p>";
							}
							$r = 1;
							while($rowFields = mysql_fetch_assoc($runFields)){
								writePage($rowFields, $rowFetch, $r);
								if ($r == 1){ $r=2; }else{ $r = 1; }
								
							}
						}
						if ($form_page != '_errlog'){ //No submit button on the Errlog page
							echo "<tr>\n";
							echo "<td>\n";
							echo "&nbsp;";
							echo "</td>\n";
							echo "<td>\n";
							echo "<input class='button greenButton' type='submit' name='Xsubmit' value='Submit' />";
							echo "</form>\n";
							echo "</td>\n";
							echo "</tr>\n";
						}
						echo "<tr>\n";
						echo "<td>\n";
						echo "</td>\n";
						echo "<td>\n";
						if ($form_id){
							echo "<form id='delform' action='". $_SERVER['PHP_SELF'] . "?page=$form_page' method='post' enctype='multipart/form-data'>";
							echo "<input type='hidden' name='Xid' value='{$rowFetch['id']}' />";
							echo "<input type='hidden' name='Xtab' value='{$form_page}' />";
							if ($form_page == "_pages" && adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']],  $_SITE['admin_level'])){
								echo "<input type='hidden' name='Xadmin' value='page' />";
							}elseif ($form_page == "_fields" && adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']],  $_SITE['admin_level'])){
								echo "<input type='hidden' name='Xadmin' value='field' />";
								$run = runSql("select b.table from `_fields` as a left join `_pages` as b on a.page = b.id  where a.id = $form_id");
								$row = mysql_fetch_assoc($run);
								echo "<input type='hidden' name='Xtab2' value='{$row['table']}' />";
							}
							if ($form_page == '_assets'){ //If a asset is deleted, delete the file too.
								
								echo "<input type='hidden' name='XDel_assets' value='delete' />";
								echo "<input type='hidden' name='XPath' value='{$rowFetch['path']}' />";
							}
							echo "<input type='hidden' name='Xdel' value='DELETE' />";
							echo "<input class='button redButton' type='button' value='Delete' onClick='deleteConfirm(document.getElementById(\"delform\"));' />";
							echo "</form>";
						}
						echo "</td>\n";
						echo "</tr>\n";
						if ($form_page == "_pages" && adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']],  $_SITE['admin_level'])){
							////////////////////////////////////////////////////////////////////////
							// Special edit box options for the pages page
							////////////////////////////////////////////////////////////////////////
	
							$row['editbox'] = 1;
							$row['order_field'] = "name";
							$row['order_show'] = "name";
							$row['order_dir'] = "ASC";
						}elseif ($form_page == "_assets" && adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']],  $_SITE['files_permission'])){
							////////////////////////////////////////////////////////////////////////
							// Special edit box options for the pages page
							////////////////////////////////////////////////////////////////////////
	
							$row['editbox'] = 1;
							$row['order_field'] = "title";
							$row['order_show'] = "title";
							$row['order_show2'] = "path";
							$row['order_dir'] = "ASC";						
						
						}elseif ($form_page == "_fields" && adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']],  $_SITE['admin_level'])){
							////////////////////////////////////////////////////////////////////////
							// Special edit box options for the fields page
							////////////////////////////////////////////////////////////////////////
							
							$row['editbox'] = 1;
							$row['order_field'] = "order";
							$row['sort_field'] = "page";
							$row['sort_link_display'] = "name";
							$row['sort_link_table'] = "_pages";
							$row['sort_link_field'] = "id";
							$row['order_show'] = "name";
							$row['order_show2'] = "order";
							$row['order_dir'] = "ASC";
						}elseif ($form_page == "_module" && adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']],  $_SITE['admin_level'])){
							////////////////////////////////////////////////////////////////////////
							// Special edit box options for the Modules page
							////////////////////////////////////////////////////////////////////////
							
							$row['editbox'] = 1;
							$row['order_field'] = "engName";
							$row['order_show'] = "engName";
							$row['order_dir'] = "ASC";
						}else{
							$run = runSql("SELECT * FROM `_pages` where `table` = '$form_page'");
							$row = mysql_fetch_assoc($run);
						}
						////////////////////////////////////////////////////////////////////////
						// Edit box displays older entries for this page for easy editing
						// and at only half the fat of a regular edit box!
						////////////////////////////////////////////////////////////////////////
	
						if ($row['search'] == 1){
							$runSearch = runSql("select engName, name from `_fields` where `page` = {$row['id']}");
							echo "<form method='get'>";
							echo "<tr>\n";
							echo "<td colspan='2'>\n";
							echo "<b>Search older entries " . ($row['sort_field'] ? "(ordered by {$row['sort_field']})" : "") . ":</b><hr>";
							echo "<input type='hidden' name='page' value='$form_page' />";
							echo "</td>";
							echo "</tr>";
							echo "<tr>\n";
							echo "<td colspan=2 width='268'>\n";							
							echo "Search field <select name='sfield'>";
							while($rowSearch = mysql_fetch_assoc($runSearch)){
								echo "<option value='{$rowSearch['name']}'>{$rowSearch['engName']}</option>";
							}
							echo "</select>";
							echo " for the string: <input type='text' name='sparam' value='$form_sparam' />";
							echo "<input type='submit' value='Search' />";
							echo "</td>\n";
							echo "</tr>\n";
							echo "</form>\n";
						
						}
						if ($row['editbox'] == 1 || ($form_sfield && $form_sparam)){

							if ($row['order_show'] == '') $row['order_show'] = 'id';

							if ($form_sfield == ""){ // Older entries page
								$run = runSql("SELECT * from `$form_page` order by " . ($row['sort_field'] ? "`{$row['sort_field']}` ASC, " : "" ) . ($row['order_field'] ? "`{$row['order_field']}` {$row['order_dir']}" : "`id` DESC "));
								$noneMsg = "<i>This page is empty</i>";
							}else{ // Searching
								$runType = runSql("select a.type from `_fields` as a left join `_pages` as b on a.page = b.id where b.name = '$form_page' and a.name = '$form_sfield'");
								$rowType = mysql_fetch_assoc($runType);
								$type = $rowType['type'];
								if (in_array($type, $arrNumType)){
									$run = runSql("SELECT * from `$form_page` where `$form_sfield` = $form_sparam order by " . ($row['sort_field'] ? "`{$row['sort_field']}` ASC, " : "" ) . " `{$row['order_field']}` {$row['order_dir']}");
								}else{
									$run = runSql("SELECT * from `$form_page` where `$form_sfield` like '%$form_sparam%' order by " . ($row['sort_field'] ? "`{$row['sort_field']}` ASC, " : "" ) . " `{$row['order_field']}` {$row['order_dir']}");

								}
								$noneMsg = "<i>Search returns 0 results</i>";
							}
							echo "<tr>\n";
							echo "<td colspan='2'>\n";
							echo "<b>Edit older entries " . ($form_sfield == "" ? "" : "where '$form_sfield' = '$form_sparam'") . ":</b><hr>";
							if (mysql_num_rows($run)){
								echo "<select style='width:896px;' size='10' onClick='window.location = \"index.php?page=$form_page&id=\" + this.value'>";
								$pSort = "";
								while($row2 = mysql_fetch_assoc($run)){
									if ($row['sort_link_table'] != ""){
										$runp = runSql("select `{$row['sort_link_display']}` as a from `{$row['sort_link_table']}` where `{$row['sort_link_field']}` = {$row2[$row['sort_field']]}");	
										$rowp = mysql_fetch_assoc($runp);
									}
									if ($row['sort_field'] && $pSort != $rowp['a']){
										$pSort = $rowp['a'];
										echo "</optgroup>";
										echo "<optgroup label='" . ($pSort ? $pSort : "[ None ]") . "'>\n";
									}
		
									echo "<option value='{$row2['id']}'>{$row2[$row['order_show']]}";
									if ($row2[$row['order_show2']]){
										$runType = runSql("select `type` from `_fields` as a left join `_pages` as b on a.`page` = b.`id`  where b.`table`= '$form_page' and a.`name` = '{$row['order_show2']}'");
										$rowType = mysql_fetch_assoc($runType);
										if ($rowType['type'] == 'timeNow'){
											echo " (" . date('M d Y', $row2[$row['order_show2']]) . ")";
										}else{
											echo " ({$row2[$row['order_show2']]})";
										}
									}
									echo "</option>\n";
								}
							}else{
								echo $noneMsg;
							}

							echo "</select>";
							echo "</td>\n";
							echo "</tr>\n";
						}
						echo "</table>\n";
					}else{
						echo "Permission Denied";
					}
				}else{
					echo $_SITE['welcome_msg'];
					echo "<p>Please choose from the following pages open to you:</p>";
					$run = runSql("SELECT * FROM `_pages`");
					while ($row = mysql_fetch_assoc($run)){
						if (adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']], $row['permission'])){
							echo "<p><h3><a href='?page={$row['table']}'>{$row['name']}</a></h3>&nbsp;&nbsp;&nbsp;<i>{$row['desc']}</i></p>\n";
						}
					}
					echo (adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']], $_SITE['admin_level']) ? "<p><h3><a href='?page=_pages'>Pages</a></h3><i>Edit and add pages to steamEngine</i>\r\n<p><h3><a href='?page=_fields'>Fields</a></h3><i>Add and edit fields in steamEngine Pages</i>\r\n" : "" );
					echo (adminperm($_SESSION['uid' . $_SERVER['SERVER_ADDR']], $_SITE['files_permission']) ? "<p><h3><a href='?page=_assets'>Assets</a></h3><i>Upload files to your site</i>\r\n" : "");

				}
				?>
			</div>
<? 
////////////////////////////////////////////////////////////////////////
// end login if 
////////////////////////////////////////////////////////////////////////
		
		} 

?>

		<?
		////////////////////////////////////////////////////////////////////////
		// Closeing mod.
		////////////////////////////////////////////////////////////////////////
		if ($_SITE['closeMod']){
			$param = explode("|", $_SITE['closeMod']);
			foreach($param as $v){
				$opt = explode(":", $v);
				$_MODULE[$opt[0]] = $opt[1]; 
			}
			include ("_mod/{$_MODULE['file']}/index.php");	       
		}
?>		<div class='foot'>
		steamEngine v0.99<br/>Developed for <a target='_blank' href='http://www.lushconcepts.com'>Lush Concepts</a> by <a target='_blank' href='http://www.imaginarythomas.com'>Thomas Girard</a>
		</div>
	</body>

</html>
