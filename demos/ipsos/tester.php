<? include ('locksmith.php'); ?>

<html>
<head><title>Test IPSOS Page</title></head>
<body>
This is user 42 doing survey 1337.<br>
<center>
	<iframe src="http://ipsospollpredictor.com/index_flash.php?r=<? echo lock("42::p1337:2:9"); ?>" height="200" width=400>
            Alternative text for browsers that do not understand IFrames.
        </iframe>
</center>


</body>
</html>
