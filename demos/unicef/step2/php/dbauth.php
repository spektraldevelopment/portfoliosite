<?

include('mss.php');


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

function runSQL($sql, $type){
	global $dbh;
	if ($type == 'i'){
	if (connectDB()){
		$run = mysql_query($sql, $dbh);
		if (!$run){
			if(!$suppress) echo "<h3>We're sorry</h3> There has been an error with your request. If this problem persists contact our administrator.";
			echo "<p>SQL: $sql</p>";
			echo "! " . mysql_error($dbh);
		}
		$inserted = mysql_insert_id();
		disconnectDB();
		return $inserted;

	}else{
		return 0;
	}
	}else{
	if (connectDB()){
		$run = mysql_query($sql, $dbh);
		if (!$run){
			if(!$suppress) echo "<h3>We're sorry</h3> There has been an error with your request. If this problem persists contact our administrator.";
			echo "<p>SQL: $sql</p>";
			echo "! " . mysql_error($dbh);
		}
		disconnectDB();
		return $run;

	}else{
		return 0;
	}

	}

}
function sanitizeInput($v){

	/* Protection against injection */
	$good = array('&#59;', '&#34;', '&#39;', '&#61;', '&#92;', '&#47;', '&#60;', '&#62;', '&lsquo;', "&#41;", "&#40;");
	$bad =  array(';',     '"',     "'",     '=',     '\\',    '/',     '<',     '>',     '`',       ')',     '(');

	$v = str_replace($bad, $good, $v);
	connectDB();
	$v = mysql_real_escape_string($v);
	disconnectDB();
	return $v;

}

function importRequest($types, $prefix){

	// Imports all request variables and sanitizes them. Dirty, dirty variables. //
	// Works just like import_request_varables //


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
?>
