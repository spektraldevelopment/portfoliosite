<? include('locksmith.php'); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>pollPredictor</title>
<script language="javascript">AC_FL_RunContent = 0;</script>
<script language="javascript">
function closewindow (){
    self.close();
    }

</script> 

<script src="AC_RunActiveContent.js" language="javascript"></script>
</head>
<body style="background:white;">
<?

$un = unlock($_GET['r']);
$vars = explode(":", $un);
if ($vars[4] == 12){ //FRENCH
	$swf = "pollPredictor_dev_fr";
}else{
	$swf = "pollPredictor_dev";	
}

?>
<!--url's used in the movie-->
<!--text used in the movie-->
<!--
<p align="center"><font face="Univers LT 47 CondensedLt" size="50" color="#ffffff" letterSpacing="-1.000000" kerning="0">50</font></p>
Total chances accumulated:
<p align="left"><font face="Univers LT 47 CondensedLt" size="29" color="#fdff7d" letterSpacing="-1.000000" kerning="0">10,000</font></p>
-->
<!-- saved from url=(0013)about:internet -->
<div align="center"><script language="javascript">
	if (AC_FL_RunContent == 0) {
		alert("This page requires AC_RunActiveContent.js.");
	} else {
		AC_FL_RunContent(
			'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0',
			'width', '1131',
			'height', '792',
			'src', '<? echo $swf; ?>',
			'quality', 'high',
			'pluginspage', 'http://www.macromedia.com/go/getflashplayer',
			'align', 'middle',
			'play', 'true',
			'loop', 'true',
			'scale', 'showall',
			'r', '<? echo $_GET['r']; ?>',
			'wmode', 'window',
			'devicefont', 'false',
			'id', 'pollPredictor',
			'bgcolor', '#ffffff',
			'name', '<? echo $swf; ?>',
			'menu', 'true',
			'allowFullScreen', 'false',
			'allowScriptAccess','sameDomain',
			'movie', '<? echo $swf; ?>',
			'salign', ''
			); //end AC code
	}
</script>
<noscript>
	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="1131" height="792" id="pollPredictor" align="middle">
	<param name="allowScriptAccess" value="sameDomain" />
	<param name="allowFullScreen" value="false" />
	<param name="movie" value="<? echo $swf; ?>.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" />	<embed src="<? echo $swf; ?>.swf" quality="high" bgcolor="#ffffff" width="1131" height="792" name="pollPredictor" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>
</noscript>
</div>
</body>
</html>
