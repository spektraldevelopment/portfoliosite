<?PHP
$returnVars = array();
$returnVars['six'] = "6";
$returnVars['five'] = "5";
$returnVars['four'] = "4";
$returnVars['three'] = "3";
$returnVars['two'] = "BLAH";
$returnVars['one'] = "0";

$returnString = http_build_query($returnVars);

echo $returnString;
?>
