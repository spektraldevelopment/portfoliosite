<?
/*
function session_secure(){
    // wrapped for the php entry....
    $alph =array('A','a','B','b','C','c','D','d','E',
    'e','F','f','G','g','H','h','I','i','J','K','k',
    'L','l','M','m','N','n','O','o','P','p','Q','q',
    'R','r','S','s','T','t','U','u','V','v','W','w',
    'X','x','Y','y','Z','z');
    for($i=0;$i<rand(10,20);$i++){
        $tmp[] =$alph[rand(0,count($alph))];
        $tmp[] =rand(0,9);
    }
    return implode('',shuffle($tmp));
}
 */

session_start();
//session_secure();

////////////////////////////////////////////////////////////////////////
//DB connecter file
////////////////////////////////////////////////////////////////////////
include('dbauth.php');

////////////////////////////////////////////////////////////////////////
//pulls in GET args
////////////////////////////////////////////////////////////////////////
import_request_variables('g', 'form_');


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
	$_SESSION['uid' . $_SITE['sitename']] = "";
}

////////////////////////////////////////////////////////////////////////
//Login
////////////////////////////////////////////////////////////////////////

if ($_POST['_uname']){
	$run = runSql("SELECT * FROM `{$_SITE['users_table']}` WHERE `username` = '{$_POST['_uname']}'");
	if (mysql_num_rows($run)){
		$row = mysql_fetch_assoc($run);
		if (md5($_POST['_pass']) == $row['password']){
			$_SESSION['uid' . $_SITE['sitename']] = $row['id']; 
		}else{
			echo "Invalid Password";
		}
	}else{
		echo "Invalid Username";
	}
	
}




////////////////////////////////////////////////////////////////////////
// Logerithmic Permission check
////////////////////////////////////////////////////////////////////////


function adminPerm($u,$check){
	global $_SITE;
	$perm = array(0);
        $run = runSql("select permission from `{$_SITE['users_table']}` where id = $u");
        $row = mysql_fetch_assoc($run);
	$l = $row['permission'];
        if ($l){
            while($l > 1){
               
                $p = pow(2, (floor(log($l,2))));
                array_push($perm, $p);
               
                $l = $l - $p;
            }
	}
	// Permission Debug
	//foreach ($perm as $k){ echo "$k<br>"; }
       

        if (in_array($check,$perm)){
            return 1;
        }else{
            return 0;
        }
}

////////////////////////////////////////////////////////////////////////
//FTP
////////////////////////////////////////////////////////////////////////

function ftpit($t, $dir){
	// set up basic connection
	global $_SITE;
	if (isset($_FILES)){

		if ($_FILES[$t]["name"] != ""){

			$ftp_server = $_SITE['ftp_server'];

			$conn_id = ftp_connect($ftp_server)  or die("Could not connect");

			// login with username and password

			$ftp_user_name = $_SITE['ftp_user'];
			$ftp_user_pass = $_SITE['ftp_password'];

			$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
			// check connection
			if ((!$conn_id) || (!$login_result)) {
			        echo "FTP connection has failed!";
			        echo "Attempted to connect to $ftp_server for user $ftp_user_name";
			        exit;
			} else {
			        echo "Connected to $ftp_server, for user $ftp_user_name";
			}

			// upload the file

			$source_file = $_FILES[$t]["tmp_name"];

			$destination_file = $_SITE['base_href'] . $dir . $_FILES[$t]["name"];
			echo " --- img: $t" . $destination_file . " , " . $source_file;

			$upload = ftp_put($conn_id, $destination_file, $source_file, FTP_BINARY);

			// check upload status
			if (!$upload) {
				echo "FTP upload has failed!! ";
			} else {
				echo "Uploaded $source_file to $ftp_server as $destination_file";
			}

			// close the FTP stream
			ftp_close($conn_id);

			return $dir . $_FILES[$t]["name"];
		}else{
			return -1;
		}
	}
}





////////////////////////////////////////////////////////////////////////
//Submit a form
////////////////////////////////////////////////////////////////////////

if($_POST['Xsubmit']){
	global $_SITE;
	$base_href = $_SITE['base_href'];
	$ftp_server = $_SITE['ftp_server'];
	$ftp_user_name = $_SITE['ftp_uname'];
	$ftp_user_pass = $_SITE['ftp_pass'];
	foreach($_FILES as $k => $v){
		if ($v['name'] != ""){

		$_POST[$k] = ftpit($k, $_POST['XDIR' . $k]);
/*
		if (copy($v['tmp_name'], $base_href . $picDir . $v['name'])){
			echo "Successfully uploaded: $k = " . $base_href . $picDir . $v['name'];
			$_POST[$k] = $picDir . $v['name'];
		}
 */
		}
	}
	$f = "";	//clear field
	$v = ""; 	//clear values
	$garb = array('\'', '"');
	$rep = array('&#39;', '&quot;');
	$table = $_POST['Xtab'];
	foreach ($_POST as $key => $value){
		if (substr($key, 0,1) != 'X' && substr($key, 0,1) != 'F'){	//Skip over form fields we dont want to insert
			if (!(substr($key, 0,1) == 'P' && $value == "")){ // Do not update empty password fields
				$keyFixed = substr($key, 1, strlen($key) - 1);
				$f .= "||| `$keyFixed`";
				if (substr($key, 0,1) == 'N'){
					$v .= "||| " . (str_replace($garb, $rep, $value) == "" ? 0 : str_replace($garb, $rep, $value));
				}elseif (substr($key, 0,1) == 'T'){ //TinyMCE fields
					$v .= "||| '" . $value . "'";
				}elseif (substr($key, 0,1) == 'A'){
					$v .= "||| '" . str_replace($garb, $rep, $value) . "'";
				}elseif (substr($key, 0,1) == 'P'){
					$v .= "||| '" . md5($value) . "'";
				}elseif (substr($key, 0,1) == 'D'){ //Date format
					$v .= "||| " . strtotime($value);
				}
			}
		}
	}
	if ($_POST['Xid']){
		$sql = "update $table set `id` = {$_POST['Xid']}";
		//echo "<br> $f <br> $v<br>!!";
			
		$fArr = explode("||| ", $f);
		$vArr = explode("|||", $v);
		
		foreach($fArr as $k => $v){
			if ($v != ""){
				$sql .= ", $v = {$vArr[$k]}";
			}
		}
		$sql .= " where `id` = {$_POST['Xid']};";
		$action = "updated";

	}else{
		$f = str_replace('|||', ',', $f);
		$v = str_replace('|||', ',', $v);
		//$f = substr($f, 0, strlen($f) - 1);
		//$v = substr($v, 0, strlen($v) - 1);
		$sql = "insert into $table (id $f) values(NULL $v)";
		$action = "added";

	}
	//echo $sql;
	runSql($sql);
	global $msg;
	$msg = "'$table' entry $action successfully.";

	////////////////////////////////////////////////////////////////////////
	// Admin pages need to do some serious SQL work to modify the DB
	////////////////////////////////////////////////////////////////////////

	if ($_POST['Xadmin'] == 'page' && $_POST['Xid'] == ""){ // new table
		runSQL('CREATE TABLE `' . str_replace($garb, $rep, $_POST['Atable']) . '` (`id` INT NOT NULL AUTO_INCREMENT ,PRIMARY KEY ( `id` )) ENGINE = InnoDB');
		$msg = "Successfully Added page '" . str_replace($garb, $rep, $_POST['Atable']) . "'";
	}elseif ($_POST['Xadmin'] == 'field'){ //new field
		$type = "";
		switch($_POST['Atype']){
			case 'alpha_select':
			case 'password':
			case 'file':
			case 'alpha_module':
			case 'text':
				$type = "VARCHAR( 255 )";
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
			$run = runSql("SELECT `table` FROM `_pages` WHERE `id` = {$_POST['Npage']}");
			$row = mysql_fetch_assoc($run);
			if ($_POST['Xid']){
				runSQL("ALTER TABLE `{$row['table']}` CHANGE `{$_POST['Xname']}` `{$_POST['Aname']}` $type NOT NULL  ");
				$msg = "Successfully modified field '{$_POST['Aname']}'";
			}else{		
				runSQL("ALTER TABLE `{$row['table']}` ADD `{$_POST['Aname']}` $type NOT NULL ;");
				$msg = "Successfully added field '{$_POST['Aname']}'";
			}
		}
	}
}

////////////////////////////////////////////////////////////////////////
//Delete an Item
////////////////////////////////////////////////////////////////////////

if($_POST['Xdel']){
	if ($_POST['Xadmin'] == 'page' && $_POST['Xid'] != ""){
		$run = runSql("SELECT `table` FROM `_pages` WHERE `id` = {$_POST['Xid']}");
		$row = mysql_fetch_assoc($run);

		// Drop the corrosponding table
		runSQL("DROP TABLE `{$row['table']}`");

		// Delete the pages entry
		runSql("DELETE FROM `_pages` WHERE `id` = {$_POST['Xid']}");

		// Delete the fields entries
		runSql("DELETE FROM `_fields` WHERE `page` = {$_POST['Xid']}");

		$msg = "Successfully removed page '{$row['table']}'";
	}elseif ($_POST['Xadmin'] == 'field' && $_POST['Xid'] == ""){
		$run = runSql("SELECT `table` FROM `_pages` WHERE `id` = {$_POST['Npage']}");
		$row = mysql_fetch_assoc($run);

		// Remove the field from the table
		runSQL("ALTER TABLE `{$row['table']}` DROP `{$_POST['Aname']}`;");

		// Delete the field entry in the fields table
		runSql("DELETE FROM `_fields` WHERE `id` = {$_POST['Xid']}");

		$msg = "Successfully removed field '{$_POST['Aname']}'";
	}else{
		runSql("DELETE FROM `{$_POST['Xtab']}` WHERE `id` = {$_POST['Xid']}");
	}
}


////////////////////////////////////////////////////////////////////////
// Start Main Page
////////////////////////////////////////////////////////////////////////

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
	<head profile="http://gmpg.org/xfn/11">
		<link rel="stylesheet" type="text/css" href="style.css">
		<script language="javascript" type="text/javascript" src="tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
		<script language="JavaScript" src="javascript/prototype.js"></script>
		<script language="JavaScript" src="javascript/src/scriptaculous.js"></script>
		<script language="javascript" type="text/javascript">
		tinyMCE.init({
			theme : "advanced",
			mode : "textareas"
		});
		function paramNote(val){
			////////////////////////////////////////////////////////////////////////
			//Show the user notes about the parameters section
			////////////////////////////////////////////////////////////////////////

			if (val == 'text' || val == 'number' || val == 'password' || val == 'textarea'){
				msg = "Put in any parameters here for the text box or text area.<br><br><i>Example: maxlength=2 size=100 or cols=29 rows=5</i>";
			}else if (val == 'alpha_select' || val == 'num_select'){
				msg = "The parameter box holds the options for the select box seperated by a ':'. Value first, Option second<br><br><i>Example: 1:One:2:Two:3:Three</i>";
			}else if (val == 'alpha_module' || val == 'num_module'){
				msg = "The parameter box holds the options for the module. Please refer to the module's documentation for the correct parameters. 'file' will be a constant parameter and it will be the name of the directory that the files are found in your _mod directory. Each parameter and value are seperated with a ':' and each set of parameter and value are seperated with a '|'<br><br><i>Example: file:mod_directory|p1:100|value:apples</i>";
			}else if (val == 'file'){
				msg = "The parameter box holds the path to where the file will be uploaded to relative to your base HREF<br>(Your current base href is: <b><?=$_SITE['base_href'];?></b>)<br><br><i>Example: Title</i>";
			}else if (val == 'div'){
				msg = "The parameter box holds the title of the divider<br><br><i>Example: /images/</i>";
			}else{
				msg = "This option has no parameters.";
			}
			document.getElementById('paramNote').innerHTML = msg;
			
		}
		</script>
		<title><? echo $_SITE['title']; ?></title>
	</head>
	<body>
	<div class="whole">
<? 
		//////////////////////////////////////////////////////////////////		
		// Send messages to the user
		//////////////////////////////////////////////////////////////////		

		if ($msg) echo "<div class='adminMSG'>$msg</div>";


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

		if (!$_SESSION['uid' . $_SITE['sitename']]){
			echo "You must login.\n";
			echo "<form method=post>";
			echo "<table>\n";
			echo "<tr>\n";
			echo "<td>Username</td>\n";
			echo "<td><input type=text name=_uname maxlength=150</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td>Password</td>\n";
			echo "<td><input type=password name=_pass maxlength=150</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td colspan=2><input type='submit' value='Login'></td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			echo "</form>\n";

		}else{

			////////////////////////////////////////////////////////////////////////
			// Logged in. Greet the user.
			////////////////////////////////////////////////////////////////////////
				$run = runSql("SELECT `username` FROM `{$_SITE['users_table']}` WHERE `id` = {$_SESSION['uid' . $_SITE['sitename']]}");
				$row = mysql_fetch_assoc($run);
				echo "Logged in as {$row['username']} (<a href='?act=logout'>Logout</a>)";
	
				////////////////////////////////////////////////////////////////////////
				// Main menu
				////////////////////////////////////////////////////////////////////////

				echo "<ul class='menu'>\n";
				$run = runSql("SELECT * FROM `_pages`");
				while ($row = mysql_fetch_assoc($run)){
					if (adminperm($_SESSION['uid' . $_SITE['sitename']], $row['permission'])){
						echo "<li " . ($form_page == $row['table'] ? "class='menuon'" : "" ) . "><a href='?page={$row['table']}'>{$row['name']}</a></li>\n";
					}
				}
				////////////////////////////////////////////////////////////////////////
				// If they're an admin, give them the site options.
				////////////////////////////////////////////////////////////////////////

				echo (adminperm($_SESSION['uid' . $_SITE['sitename']], $_SITE['admin_level']) ? "<li " . ($form_page == "_pages" ? "class='menuon'" : "" ) . "><a href='?page=_pages'>Pages</a></li> <li " . ($form_page == "_fields" ? "class='menuon'" : "" ) . "><a href='?page=_fields'>fields</a></li>" : "" );

				echo "</ul>\n";
						
				echo "<div style='float:left;width:770px;'>\n";

				////////////////////////////////////////////////////////////////////////
				// Start the serious stuff: if there's a form_page, we're on an edit form
				////////////////////////////////////////////////////////////////////////

				if ($form_page){

					////////////////////////////////////////////////////////////////////////
					// If there's a form_id, then we're on an edit page so write a DELETE option
					////////////////////////////////////////////////////////////////////////
					if ($form_id){
						$run = runSql("select * from $form_page where id = $form_id");
						$rowFetch = mysql_fetch_assoc($run);
						echo "<form method=post enctype='multipart/form-data'>";
						echo "<input type='hidden' name='Xid' value='{$rowFetch['id']}'>";
						echo "<input type='hidden' name='Xtab' value='{$form_page}'>";
						if ($form_page == "_pages" && adminperm($_SESSION['uid' . $_SITE['sitename']],  $_SITE['admin_level'])){
							echo "<input type='hidden' name='Xadmin' value='page'>";
						}elseif ($form_page == "_fields"){
							echo "<input type='hidden' name='Xadmin' value='field'>";
						}
						echo "<input type='submit' name='Xdel' value='DELETE'>";
						echo "</form>";
					}
					////////////////////////////////////////////////////////////////////////
					// Get all the fields for this page
					////////////////////////////////////////////////////////////////////////
	
					$runFields = runSql("SELECT * FROM `_fields` WHERE `page` = (SELECT `id` FROM `_pages` WHERE `table` = '{$form_page}') ORDER BY `order` ASC");


					////////////////////////////////////////////////////////////////////////
					// Start the form 
					////////////////////////////////////////////////////////////////////////
					echo "<form method=post enctype='multipart/form-data'>";
					echo "<input type='hidden' name='Xid' value='{$rowFetch['id']}'>";
					echo "<input type='hidden' name='Xtab' value='{$form_page}'>";
					echo "<table valign=top>";
					echo "<tr><td width=350><b>Name</b></td><td><b>Value</b></td></tr>";

					if ($form_page == "_pages" && adminperm($_SESSION['uid' . $_SITE['sitename']],  $_SITE['admin_level'])){
						////////////////////////////////////////////////////////////////////////
						// This is the admin pages section. It contols all the pages in the CMS
						////////////////////////////////////////////////////////////////////////
						echo "<input type='hidden' name='Xadmin' value='page'>";
						echo "<tr>\n";
						echo "<td colspan=2>\n";
						echo "<strong>WARNING:</strong> These admin pages can drastically effect the site. If you don't know EXACTLY what you're doing I'd suggest you don't mess around in here.<br><br>";
						echo "</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "<td>\n";
						echo "Table";
						echo "</td>\n";
						echo "<td>\n";
						echo "<input type=text name='Atable' value='{$rowFetch['table']}'>";
						echo "</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "<td>\n";
						echo "Name";
						echo "</td>\n";
						echo "<td>\n";
						echo "<input type=text name='Aname' value='{$rowFetch['name']}'>";
						echo "</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "<td>\n";
						echo "Permission";
						echo "</td>\n";
						echo "<td>\n";
						echo "<select name='Npermission'>";
						$run = runSql("SELECT * from _permission order by value ASC");
						while($rowP = mysql_fetch_assoc($run)){
							echo "<option " . ($rowFetch['permission'] == $rowP['value'] ? "selected" : "") . " value='{$rowP['value']}'>{$rowP['name']}</option>";
						}
						echo "</select>";
						echo "</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "<td>\n";
						echo "Edit previous entries box";
						echo "</td>\n";
						echo "<td>\n";
						echo "<select name='Neditbox'>";
						echo "<option " . ($rowFetch['editbox'] == "0" ? "selected" : "") . " value='0'>No</option>";
						echo "<option " . ($rowFetch['editbox'] == "1" ? "selected" : "") . " value='1'>Yes</option>";
						echo "</select>";
						echo "</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "<td colspan=2>\n";
						echo "<b>Advance Entry box options</b><br><i>Optional: This will sort the results in the 'Edit previous entries' box by this field and give them headdings making entries easier to find.</i><hr>";
						echo "</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "<tr>\n";
						echo "<td>\n";
						echo "Order By Field";
						echo "</td>\n";
						echo "<td>\n";
						echo "<input type=text name='Aorder_field' value='{$rowFetch['order_field']}'>";
						echo "</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "<td>\n";
						echo "Order Direction";
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
						echo "Field to show in the edit box";
						echo "</td>\n";
						echo "<td>\n";
						echo "<input type=text name='Aorder_show' value='{$rowFetch['order_show']}'>";
						echo "</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "<td colspan=2>\n";
						echo "<b>Advance Entry box sorting options</b><br><i>Optional: These options will help sort the entries by a specified field and can be linked to another table for id values.</i><hr>";
						echo "</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "<td>\n";
						echo "Sort Field";
						echo "</td>\n";
						echo "<td>\n";
						echo "<input type=text name='Asort_field' value='{$rowFetch['sort_field']}'>";
						echo "</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "<td>\n";
						echo "Sort Linked Table";
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
						echo "Sort Linked Field";
						echo "</td>\n";
						echo "<td>\n";
						echo "<input type=text name='Asort_link_field' value='{$rowFetch['sort_link_field']}'>";
						echo "</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "<td>\n";
						echo "Sort Linked Display";
						echo "</td>\n";
						echo "<td>\n";
						echo "<input type=text name='Asort_link_display' value='{$rowFetch['sort_link_display']}'>";
						echo "</td>\n";
						echo "</tr>\n";
						

					}elseif ($form_page == "_fields"){
						////////////////////////////////////////////////////////////////////////
						// This is the fields section of the admin panel. It controlles the fields in the pages 
						////////////////////////////////////////////////////////////////////////
						echo "<input type='hidden' name='Xadmin' value='field'>";
						echo "<tr>\n";
						echo "<td colspan=2>\n";
						echo "<strong>WARNING:</strong> These admin pages can drastically effect the site. If you don't know EXACTLY what you're doing I'd suggest you don't mess around in here.<br><br>";
						echo "</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "<td>\n";
						echo "Page";
						echo "</td>\n";
						echo "<td>\n";
						echo "<select name='Npage'>";
						echo "<option value=''>--SELECT--</option>";
						$run = runSql("SELECT * FROM _pages");
						while($row = mysql_fetch_assoc($run)){
							echo "<option value='{$row['id']}' " . ($row['id'] == $rowFetch['page'] ? "selected" : "") . ">{$row['name']}</option>";
						}
						echo "</select>";
						echo "</td>\n";
						echo "</tr>\n";
						echo "</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "<td>\n";
						echo "Type";
						echo "</td>\n";
						echo "<td>\n";
						echo "<select name='Atype' onChange='paramNote(this.value);'>";
						echo "<optgroup label='Basic'>";
						echo "<option " . ($rowFetch['type'] == "text" ? "selected" : "") . " value='text'>Text</option>";
						echo "<option " . ($rowFetch['type'] == "number" ? "selected" : "") . " value='number'>Number</option>";
						echo "<option " . ($rowFetch['type'] == "textarea" ? "selected" : "") . " value='textarea'>Text Area</option>";
						echo "<option " . ($rowFetch['type'] == "password" ? "selected" : "") . " value='password'>Password</option>";
						echo "<option " . ($rowFetch['type'] == "file" ? "selected" : "") . " value='file'>File</option>";
						echo "</optgroup>";
						echo "<optgroup label='Select'>";
						echo "<option " . ($rowFetch['type'] == "alpha_select" ? "selected" : "") . " value='alpha_select'>Alpha Select</option>";
						echo "<option " . ($rowFetch['type'] == "num_select" ? "selected" : "") . " value='num_select'>Numeric Select</option>";
						echo "</optgroup>";
						echo "<optgroup label='Module'>";
						echo "<option " . ($rowFetch['type'] == "alpha_module" ? "selected" : "") . " value='alpha_module'>Alpha Module</option>";
						echo "<option " . ($rowFetch['type'] == "num_module" ? "selected" : "") . " value='num_module'>Numeric Module</option>";
						echo "<option " . ($rowFetch['type'] == "long_module" ? "selected" : "") . " value='long_module'>Long Text Module</option>";
						echo "<optgroup label='Misc.'>";
						echo "<option " . ($rowFetch['type'] == "timeNow" ? "selected" : "") . " value='timeNow'>Current time</option>";
						echo "<option " . ($rowFetch['type'] == "div" ? "selected" : "") . " value='div'>Divider</option>";
						echo "</optgroup>";
						echo "</select>";
						echo "</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "<td>\n";
						echo "Field Name";
						echo "</td>\n";
						echo "<td>\n";
						echo "<input type=text name='Aname' value='{$rowFetch['name']}'>";
						echo "<input type=hidden name='Xname' value='{$rowFetch['name']}'>";
						echo "</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "<td>\n";
						echo "Full Field name";
						echo "</td>\n";
						echo "<td>\n";
						echo "<input type=text name='AengName' value='{$rowFetch['engName']}'>";
						echo "</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "<td>\n";
						echo "Order";
						echo "</td>\n";
						echo "<td>\n";
						echo "<input type=text name='Norder' value='{$rowFetch['order']}'>";
						echo "</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
						echo "<td>\n";
						echo "Parameters";
						echo "</td>\n";
						echo "<td>\n";
						echo "<input type=text name='Aparam' value='{$rowFetch['param']}'>";
						echo "<p id=paramNote></p>";
						echo "</td>\n";
						echo "</tr>\n";

						echo "<tr>\n";
					}else{

						////////////////////////////////////////////////////////////////////////
						// Now on to the user created pages. Here we rotate though all the
						// fields in a page and writes the form based on the data. 
						////////////////////////////////////////////////////////////////////////

						while($rowFields = mysql_fetch_assoc($runFields)){
							switch ($rowFields['type']){
							case 'text':
								echo "<tr>\n";
								echo "<td>\n";
								echo $rowFields['engName'];
								echo "</td>\n";
								echo "<td>\n";
								echo "<input type=text size=100 name='A{$rowFields['name']}' value='{$rowFetch[$rowFields['name']]}' {$rowFields['param']}>";
								echo "</td>\n";
								echo "</tr>\n";
								break;
							case 'number':
								echo "<tr>\n";
								echo "<td>\n";
								echo $rowFields['engName'];
								echo "</td>\n";
								echo "<td>\n";
								echo "<input type=text size=100 name='N{$rowFields['name']}' value='" . ($rowFetch[$rowFields['name']] ? $rowFetch[$rowFields['name']] : "0") . "' {$rowFields['param']}>";
								echo "</td>\n";
								echo "</tr>\n";
								break;
							case 'password':
								echo "<tr>\n";
								echo "<td>\n";
								echo $rowFields['engName'];
								echo "</td>\n";
								echo "<td>\n";
								echo "<input type=text size=100 name='P{$rowFields['name']}' value='' {$rowFields['param']}>";
								echo "</td>\n";
								echo "</tr>\n";
								break;
							case 'textarea':
								echo "<tr>\n";
								echo "<td>\n";
								echo $rowFields['engName'];
								echo "</td>\n";
								echo "<td>\n";
								echo "<textarea name='T{$rowFields['name']}' " . ($rowFields['param'] ? $rowFields['param'] : "cols=20 rows=10") . ">{$rowFetch[$rowFields['name']]}</textarea>";
								echo "</td>\n";
								echo "</tr>\n";
								break;
							case 'num_select':
								echo "<tr>\n";
								echo "<td>\n";
								echo $rowFields['engName'];
								echo "</td>\n";
								echo "<td>\n";
								$opt = explode(":",$rowFields['param']);
								$opts = sizeof($opt);
								echo "<select name='N{$rowFields['name']}'>";
								echo "<option value='' " . ($opt[$x] == $rowFetch[$rowFields['name']] ? "selected" : "") . ">--SELECT--</option>";
								for ($x = 0; $x < $opts; $x+= 2){
									echo "<option value='{$opt[$x]}' " . ($opt[$x] == $rowFetch[$rowFields['name']] ? "selected" : "") . ">{$opt[$x+1]}</option>\n";
								}
								echo "</select>";
								echo "</td>\n";
								echo "</tr>\n";
								break;
							case 'alpha_select':
								echo "<tr>\n";
								echo "<td>\n";
								echo $rowFields['engName'];
								echo "</td>\n";
								echo "<td>\n";
								$opt = explode(":", $rowFields['param']);
								$opts = sizeof($opt);
								echo "<select name='A{$rowFields['name']}'>";
								echo "<option value='' " . ($opt[$x] == $rowFetch[$rowFields['name']] ? "selected" : "") . ">--SELECT--</option>";
								for ($x = 0; $x < $opts; $x+= 2){
									echo "<option value='{$opt[$x]}' " . ($opt[$x] == $rowFetch[$rowFields['name']] ? "selected" : "") . ">{$opt[$x+1]}</option>\n";
								}
								echo "</select>";
								echo "</td>\n";
								echo "</tr>\n";
								break;
							case 'ro':
								echo "<tr>\n";
								echo "<td>\n";
								echo $rowFields['engName'];
								echo "</td>\n";
								echo "<td>\n";
								echo $rowFetch[$rowFields['name']];
								echo "</td>\n";
								echo "</tr>\n";
								break;
							case 'div':
								echo "<tr>\n";
								echo "<td colspan=2>\n";
								echo "<b>{$rowFields['param']}</b><hr>";
								echo "</td>\n";
								echo "</tr>\n";
								break;
							case 'alpha_module':
							case 'long_module':
							case 'num_module':
								echo "<tr>\n";
								echo "<td>\n";
								echo $rowFields['engName'];
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
								echo "<input type=hidden name='N{$rowFields['name']}' value='" . ($rowFetch[$rowFields['name']] ? $rowFetch[$rowFields['name']] : time()) . "'>";
								break;							
							case 'file':
								echo "<tr>\n";
								echo "<td>\n";
								echo "Current " . $rowFields['engName'];
								echo "</td>\n";
								echo "<td>\n";
								echo ($rowFetch[$rowFields['name']] ? "<img src='{$rowFetch[$rowFields['name']]}'>" : "<i>none...</i>");
								echo "</td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
								echo "<td>\n";
								echo "New " . $rowFields['engName'];
								echo "</td>\n";
								echo "<td>\n";
								echo "<input type=file name='A{$rowFields['name']}' value='{$rowFetch[$rowFields['name']]}' {$rowFields['param']}>";
								echo "<input type=hidden name='XDIRA{$rowFields['name']}' value='{$rowFields['param']}'>";
								echo "</td>\n";
								echo "</tr>\n";
								break;
	
								default:
									echo "error";
									break;
							}
						}
					}
					echo "<tr>\n";
					echo "<td>\n";
					echo "&nbsp;";
					echo "</td>\n";
					echo "<td>\n";
					echo "<input type='submit' name='Xsubmit' value='Submit'>";
					echo "</td>\n";
					echo "</tr>\n";
					if ($form_page == "_pages" && adminperm($_SESSION['uid' . $_SITE['sitename']],  $_SITE['admin_level'])){
						////////////////////////////////////////////////////////////////////////
						// Special edit box options for the pages page
						////////////////////////////////////////////////////////////////////////

						$row['editbox'] = 1;
						$row['order_field'] = "name";
						$row['order_show'] = "name";
						$row['order_dir'] = "ASC";
					}elseif ($form_page == "_fields" && adminperm($_SESSION['uid' . $_SITE['sitename']],  $_SITE['admin_level'])){
						////////////////////////////////////////////////////////////////////////
						// Special edit box options for the fields page
						////////////////////////////////////////////////////////////////////////
						
						$row['editbox'] = 1;
						$row['order_field'] = "name";
						$row['sort_field'] = "page";
						$row['sort_link_display'] = "name";
						$row['sort_link_table'] = "_pages";
						$row['sort_link_field'] = "id";
						$row['order_show'] = "name";
						$row['order_dir'] = "ASC";
					}else{
						$run = runSql("SELECT * FROM `_pages` where `table` = '$form_page'");
						$row = mysql_fetch_assoc($run);
					}
					////////////////////////////////////////////////////////////////////////
					// Edit box displays older entries for this page for easy editing
					// and at only half the fat of a regular edit box!
					////////////////////////////////////////////////////////////////////////

					if ($row['editbox'] == 1){
						echo "<tr>\n";
						echo "<td colspan=2>\n";
						echo "<b>Edit older entries " . ($row['sort_field'] ? "(ordered by {$row['sort_field']})" : "") . ":</b><hr>";
						echo "<select style='width:896px;' size=10 onClick='window.location = \"index.php?page=$form_page&id=\" + this.value'>";
						$run = runSql("SELECT * from `$form_page` order by " . ($row['sort_field'] ? "`{$row['sort_field']}` ASC, " : "" ) . " `{$row['order_field']}` {$row['order_dir']}");
						$pSort = "";
						while($row2 = mysql_fetch_assoc($run)){
							if ($row['sort_link_table'] != ""){
								$runp = runSql("select `{$row['sort_link_display']}` as a from `{$row['sort_link_table']}` where `{$row['sort_link_field']}` = {$row2[$row['sort_field']]}");	
								$rowp = mysql_fetch_assoc($runp);
							}
							if ($row['sort_field'] && $pSort != $rowp['a']){
								$pSort = $rowp['a'];
								//echo "<option style='font-weight:bold;' value=''>" . $pSort . "</option>\n";
								echo "</optgroup>";
								echo "<optgroup label='" . $pSort . "'>\n";
							}

							echo "<option value='{$row2['id']}'>{$row2[$row['order_show']]}</option>\n";
						}
						echo "</select>";
						echo "</td>\n";
						echo "</tr>\n";
					}
					echo "</table>\n";
					echo "</form>\n";

				}else{
					echo $_SITE['welcome_msg'];
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
		if ($_SITE['initMod']){
			$param = explode("|", $_SITE['initMod']);
			foreach($param as $v){
				$opt = explode(":", $v);
				$_MODULE[$opt[0]] = $opt[1]; 
			}
			include ("_mod/{$_MODULE['file']}/indexBOT.php");	       
		}
		?>
		</div>
	</body>

</html>
