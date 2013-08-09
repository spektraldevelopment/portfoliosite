<?php

include('dbauth.php');
connectDB();
$fname = mysql_real_escape_string($_GET['firstName']);
$lname = mysql_real_escape_string($_GET['lastName']);
$eMail = mysql_real_escape_string($_GET['eMailAddress']);
disconnectDB();
$recieveUpdates = ($_GET['GetUpdates'] == 'true' ? 1 : 0);


runSql("insert into `signup` values(NULL, '$fname', '$lname', '$eMail', $recieveUpdates);", "i");

//echo "insert into `signup` values(NULL, '$fname', '$lname', '$eMail', $recieveUpdates);";

?> 
