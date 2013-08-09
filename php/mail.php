<?php
    $to = "spektraldevelopment@gmail.com";
    $subject = $_GET["s"];
    $message = $_GET["m"];
    $headers = "From: " . $_GET["e"];
    mail($to,$subject,$message,$headers);
    echo "Mail Sent.: " . $to . " subject: " . $subject . " message: " . $message . " headers: " . $headers;
?>