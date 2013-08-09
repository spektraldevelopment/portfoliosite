<form method=post>

	Seach for games:<br>
	<input type=text name=str>

</form>

<?
	include ("dbauth.php");
	if ($_POST['str'] != ""){
		$_POST['str'] = htmlspecialchars($_POST['str'], ENT_QUOTES);
		$run = runSql("select `question`, `id` from game where `question` LIKE('%" . str_replace(" ", "%", $_POST['str']) . "%')");
		echo mysql_num_rows($run) . " Records found<br>";
		while ($row = mysql_fetch_assoc($run)){
			echo "<a href='index.php?page=game&id={$row['id']}'>{$row['question']}</a><br>";
		}
	}
?>
