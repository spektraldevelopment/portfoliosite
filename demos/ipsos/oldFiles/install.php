<?

include ('dbauth.php');

$tableSQL ="
CREATE TABLE `_fields` (
  `id` int(11) NOT NULL auto_increment,
  `page` int(11) NOT NULL,
  `type` enum('text','textarea','select','module','file','timeNow','div') NOT NULL,
  `param` mediumtext NOT NULL,
  `name` varchar(100) NOT NULL,
  `engName` varchar(100) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;
	
CREATE TABLE `_pages` (
  `id` int(11) NOT NULL auto_increment,
  `table` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `editbox` tinyint(4) NOT NULL,
  `order_field` varchar(255) NOT NULL,
  `order_dir` enum('asc','desc') NOT NULL,
  `order_show` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

CREATE TABLE `_site` (
  `id` int(11) NOT NULL auto_increment,
  `param_name` varchar(255) NOT NULL,
  `param_value` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;


CREATE TABLE `_permission` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

CREATE TABLE `_users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `permission` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;


";


$dbauth = "<?php

\$dbInfo = array('uname'=>'thatgir3_thomOS', 'pass'=>'k177yl1773r', 'db'=>'thatgir3_thomOS');

function connectDB(){
	global \$dbInfo;
	global \$dbh;
	\$dbh=mysql_connect ('localhost', \$dbInfo['uname'], \$dbInfo['pass']) or die ('Cannot connect to the database because: ' . mysql_error());
	mysql_select_db (\$dbInfo['db']);
}

function disconnectDB(){
	global \$dbh;
	mysql_close(\$dbh);
}

function runSql(\$sql){
	connectDB();
	global \$dbh;
	\$row = mysql_query(\$sql, \$dbh) or die('counter INSERT error: '.mysql_errno().','.mysql_error() . '    ' . \$sql);
	if (\$_COOKIE['debug']=='debug') echo '<!-- \$sql -->';
	disconnectDB();
	return \$row;
}
?>";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
	<head profile="http://gmpg.org/xfn/11">
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>ThomOS</title>
	</head>
	<body>
		<div class="whole">

			<form method=post>
				<table>
					<tr>
						<td>Base Href</td>
						<td><input type="" value="<? echo $_SERVER['DOCUMENT_ROOT']?>"></td>
					</tr>
					<tr>
						<td>FTP Server</td>
						<td><input type="" value=""></td>
					</tr>
					<tr>
						<td>FTP Username</td>
						<td><input type="" value=""></td>
					</tr>
					<tr>
						<td>FTP Password</td>
						<td><input type="" value=""></td>
					</tr>
					<tr>
						<td>mySQL Username</td>
						<td><input type="" value=""></td>
					</tr>
					<tr>
						<td>mySQL Password</td>
						<td><input type="" value=""></td>
					</tr>
				</table>
			</form>
		</div>
	</body>
	
