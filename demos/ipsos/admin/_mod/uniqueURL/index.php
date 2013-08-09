<!--

Unique URL Module:

Input Param:
file: Dir where the file is
url: The first part of the URL

-->
<?
include('../locksmith.php');

$id = $rowFetch['id'];
$uid = $rowFetch['uid'];
$country = $rowFetch['country'];
$lang = $rowFetch['lang'];

$locked = lock("$uid:$id::$country:$lang");
echo "<u style='color:blue;'>http://{$_MODULE['url']}$locked</u>";
?>

