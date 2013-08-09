<!--

Related Items Order List Module:

Input Param:
file: Dir where the file is
rfield: Field to store related ID's
rtable: Table to get related items from
rtableIdent: Field from rtable to identify each item




-->
<script language=javascript>
		function getOrder() {
			var orderList = '';
			orderedNodes = document.getElementById("sortable_list").getElementsByTagName("li");
			for (var i=0;i < orderedNodes.length;i++) {
				orderList += orderedNodes[i].getAttribute('recordid') + ', ';
			}
			orderList = orderList.substring(0, orderList.length - 2);
			return orderList;
		}
		 
		function removeRelated(id, title){
			inner = document.getElementById('sortable_list').innerHTML;
			document.getElementById('sortable_list').innerHTML = "";
			leftover = inner.split("</li>");
			if (leftover.length == 1){ //IE sucks
				leftover = inner.split("</LI>");
			}
			for(i=0; i<leftover.length; i++){
				if (leftover[i].indexOf('"'+ id + '"') == -1){
					document.getElementById('sortable_list').innerHTML += leftover[i] + "</li>";
				}
			}
			Sortable.create("sortable_list");
			document.getElementById('Arelated').value = getOrder();
		}	
		function addRelated(idtitle){
			id = idtitle.split(":");
			document.getElementById('sortable_list').innerHTML += "<li style='position: relative;' recordid=\"" + id[0] + "\"><a onclick=\"removeRelated('" + id[0] + "','" + id[1] +"');\"><img border=0 src='img/close.png'></a>" + id[1] + "</li>";
			Sortable.create("sortable_list");
			document.getElementById('Arelated').value = getOrder();
			
		}
</script>

<table>
<tr>
<td width=300>
<?
if ($form_id && $rowFetch[$_MODULE['rfield']] != ''){
	$run = runSql("select {$_MODULE['rfield']} from $form_page where id=$form_id");
	$row2 = mysql_fetch_assoc($run);
	echo "<input type=hidden id=A{$_MODULE['rfield']} name=A{$_MODULE['rfield']} value='{$row[$_MODULE['rfield']]}'>";
	echo "<ul class='sortList' id='sortable_list' style='cursor: move'>";
	$rel = explode(",", $rowFetch[$_MODULE['rfield']]);
	for($r = 0; $r < sizeof($rel); $r++){
		$run = runSql("select title from {$_MODULE['rtable']} where id={$rel[$r]}");
		$row2 = mysql_fetch_assoc($run);
		echo "<li style='position: relative;' recordid='{$rel[$r]}'><a onclick=\"removeRelated('{$rel[$r]}','{$row2['title']}');\"><img border=0 src='img/close.png'></a>{$row2['title']}</li>";
	}
	echo "</ul>";
}else{
	echo "<input type=hidden id=A{$_MODULE['rfield']} name=A{$_MODULE['rfield']} value=''>";
	echo "<ul class='sortList' id='sortable_list' style='cursor: move'>";
	echo "</ul>";
}
?>
</td>
<td>
	<select size=7 style="width:350px;" onChange="addRelated(this.value);">													
		<?
		$run = runSql("select id, {$_MODULE['rtableIdent']} from {$_MODULE['rtable']}");
		while($row2 = mysql_fetch_assoc($run)){
			echo "<option value='{$row2['id']}:{$row2['title']}'>{$row2['title']}</option>";
		}
		?>
	</select>
</td>
</tr>
</table>
<script language="JavaScript">
		Sortable.create("sortable_list");
</script>
<tr>
<td>
</td>
<td>
<b>MAKE SURE YOU SET YOUR ORDER ANY TIME IT IS CHANGED</b> <input type=button onclick="document.getElementById('A<? echo $_MODULE['rfield']; ?>').value = getOrder();" value="SET">
</td>
</tr>
