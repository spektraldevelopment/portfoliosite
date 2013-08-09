<html>
<body style="font-family:courier;">
<?

include ("locksmith.php");

echo "Test Encryption<br><br>";
echo "USERID:RECOVERYID:SURVEYID:COUNTRYID:LANGUAGEID<br><br>";
echo "<form method=get><input name=str type=text value='{$_GET['str']}'><input type=submit></form>";

if ($_GET['key']){
	echo "Decrypt<br>" . $_GET['key'] . " => ";
	$val = unlock($_GET['key']);
	echo $val . "<br><br><a href='?str=$val'>Encrypt</a>";
}

if ($_GET['str']){
	echo "Encrypt<br>" . $_GET['str'] . " => ";
	$val = lock($_GET['str']);
	echo $val . "<br><a href='index.php?r=$val'>Test</a><br><br><a href='?key=$val'>Decrypt</a>";
}

?>
<br><a href='index_flash.php?game=npn&c=1&l=9'>No purchase Nessessary game ENG USA</a>
<br><a href='index_flash.php?game=npn&c=2&l=9'>No purchase Nessessary game ENG CANADA</a>
<br><a href='index_flash.php?game=npn&c=2&l=12'>No purchase Nessessary game FR CANADA</a>
</body>
</html>
