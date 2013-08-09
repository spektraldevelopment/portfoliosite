<!--

Get My ID:

Input Param:
file: Dir where the file is
dispfield: Field to display

-->
<?
	global $_SITE;
	if ($rowFetch[$rowFields['name']]){
		//Older entry, get that users info:
		$id = $rowFetch[$rowFields['name']];
	}else{
		$id = $_SESSION['uid' . $_SERVER['SERVER_ADDR']];
	}
	$run1 = runSql("SELECT id, {$_MODULE['dispfield']}  FROM {$_SITE['users_table']} WHERE `id` = $id");
	while($row = mysql_fetch_assoc($run1)){
		echo "<input name='N{$rowFields['name']}' type=hidden value='{$row['id']}'>{$row[$_MODULE['dispfield']]}";
	}
?>
