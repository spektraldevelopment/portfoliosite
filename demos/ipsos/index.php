<? include('locksmith.php'); ?>
<? include('admin/dbauth.php'); ?>
<? importRequest('g', 'form_'); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>pollPredictor</title>
<script language="javascript">AC_FL_RunContent = 0;</script>
<script src="AC_RunActiveContent.js" language="javascript"></script>
</head>
<body style="background:white;">
<?

$un = unlock($_GET['r']);
$vars = explode(":", $un);
if ($vars[4] == 12){ //FRENCH
	$swf = "pollPredictor_fr";
}else{
	$swf = "pollPredictor";	
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
			'width', '1100',
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
	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="1100" height="792" id="pollPredictor" align="middle">
	<param name="allowScriptAccess" value="sameDomain" />
	<param name="allowFullScreen" value="false" />
	<param name="movie" value="<? echo $swf; ?>.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#ffffff" />	<embed src="<? echo $swf; ?>.swf" quality="high" bgcolor="#ffffff" width="1100" height="792" name="pollPredictor" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>
</noscript>
</div>


<!--
<div style="width:700px; margin: 70px auto 0 auto; padding: 30px; border: 1px solid #ccc">
<span style="font:90%/125% Verdana, Geneva, sans-serif;">Ipsos's Poll Predictor Game is currently down for maintainance for about 2 hours.  Don't worry, we got you covered.  We've created a special link just for you to play Poll Predictor once we're back up and running.  Simply copy the link below, and keep it somewhere safe, email it to yourself, copy it into a blank document, whatever you want.  Simply paste the link into your browser later on today to continue on to Poll Predictor.<br />
<br />
Here is the link:<br /><br /></span>
<span style="font:110%/125% Verdana, Geneva, sans-serif;">http://www.ipsospollpredictor.com/?r=<? echo $_GET['r']; ?></span>

</div>

-->



</body>
</html>
