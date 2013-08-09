<?php
if (isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
$data = $GLOBALS["HTTP_RAW_POST_DATA"];
$filename = time() + rand(0,9000);
$file = fopen("MonsterStory" . $filename . ".pdf", "wb");
fwrite($file, $data);
fclose($file);
header("Location: ../../form_story.php?pdf=MonsterStory$filename");
}
?>
