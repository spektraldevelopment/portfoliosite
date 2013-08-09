<?

include('mysql_settings.php');

$msg = array();
$msgBad = array();

function connectDB(){
	global $dbh, $sql_server, $sql_user, $sql_pass , $sql_db;
	$dbh=mysql_connect ($sql_server, $sql_user, $sql_pass);
	if ($dbh){
		mysql_select_db ($sql_db);
		return 1;
	}else{
		return 0;
	}
}

function disconnectDB(){
	global $dbh;
	mysql_close($dbh);
}

function runSQL($sql){
	global $dbh;
	global $_SITE;
	global $msgBad;
	if (connectDB()){
		$run = mysql_query($sql, $dbh);
		if (!$run){
			echo "<h3>We're sorry</h3> There has been an error with your request. If this problem persists contact our administrator.";
			if ($_SITE['debug_mode'] == 1){
				echo "<p>SQL: $sql</p>";
			}
		
			array_push($msgBad, "SQL Error: " . mysql_error());
			$run = runSql("insert into `_errlog` values(NULL, 'SQL Error', \"SQL: $sql<br />\r\nIP: {$_SERVER['REMOTE_ADDR']}<br />\r\nTime: " . date('F d y, H:i', time()) . "\");");
			$run = runSql("select * from `_site` where 1=0");
			return $run;
		}
		if ($_SITE['debug_mode'] == 1){
			echo "<!--SQL: $sql -->";
		}
		disconnectDB();
		return $run;
	}else{
		return 0;
	}

}
function sanitizeInput($v){

	/* Protection against injection */
	$good = array('&#59;', '&#34;', '&#39;', '&#61;', '&#92;', '&#47;', '&#60;', '&#62;', '&lsquo;');
	$bad =  array(';',     '"',     "'",     '=',     '\\',    '/',     '<',     '>',     '`');

	$v = str_replace($bad, $good, $v);
	connectDB();
	$v = mysql_real_escape_string($v);
	disconnectDB();
	return $v;

}

function importRequest($types, $prefix){

	// GET
	if (strpos(strtolower($types), 'g') !== FALSE && is_array($_GET)){
		foreach ($_GET as $k => $v){
			$whole = $prefix . $k;
			global $$whole;
			$$whole = sanitizeInput($v);
			$_GET[$k] = sanitizeInput($v);
		}
	}
	// POST
	if (strpos(strtolower($types), 'p') !== FALSE && is_array($_POST)){
		foreach ($_POST as $k => $v){
			$whole = $prefix . $k;
			global $$whole;
			$$whole = sanitizeInput($v);
			$_POST[$k] = sanitizeInput($v);
		}
	}
}
////////////////////////////////////////////////////////////////////////
//Get site controls
////////////////////////////////////////////////////////////////////////

$run = runSql('SELECT * FROM `_site`');
while($row = mysql_fetch_assoc($run)){
	$_SITE[$row['param_name']] = $row['param_value'];
}
?>
