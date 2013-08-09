<!--

Link table drop down Module:

Input Param:
file: Dir where the file is
ltable: linked table
lvalue: value to be used from the linked table
cvalue: value to be used from the current table
ldisp: Value to be displayed in the dropdown
lcond: any SQL conditions you want to use ie. "WHERE `active` = 1"
type: 'N' for numeric, 'A' for alpha value in lvalue
size(OPTIONAL): Size of the select box allows for multiple selection

-->
<script Language="javascript">
function loopSelected(mtype, name)
{
  var txtSelectedValuesObj = document.getElementById(mtype + name);
  var selectedArray = new Array();
  var selObj = document.getElementById(name);
  var i;
  var count = 0;
  for (i=0; i<selObj.options.length; i++) {
    if (selObj.options[i].selected) {
      selectedArray[count] = selObj.options[i].value;
      count++;
    }
  }
  txtSelectedValuesObj.value = selectedArray;
}
</script>

<?
	$selected = explode(",", $rowFetch[$_MODULE['cvalue']]);
	$opt = explode(":", $rowFields['param']);
	$opts = sizeof($opt);
	echo "<select id={$rowFields['name']} " . ($_MODULE['size'] ? "onChange='loopSelected(\"" . $_MODULE['type'] . "\",\"" . $rowFields['name'] . "\");' multiple size=" . $_MODULE['size']  : "" ) . " name='" . ($_MODULE['size'] ? "" : $_MODULE['type'] . $rowFields['name']) . "'>";
	echo "<option value='' " . (in_array($row[$_MODULE['lvalue']], $selected) ? "selected" : "") . ">--NONE--</option>";
	$run = runSql("SELECT * FROM {$_MODULE['ltable']} {$_MODULE['lcond']}");
	while($row = mysql_fetch_assoc($run)){
		echo "<option value='{$row[$_MODULE['lvalue']]}' " . (in_array($row[$_MODULE['lvalue']], $selected) ? "selected" : "") . ">{$row[$_MODULE['ldisp']]}</option>";
	}
	echo "</select>";
	if($_MODULE['size']){
		//use this hidden box to store vars if it's a multi select
		echo "<input type='hidden' id=" . $_MODULE['type'] . $rowFields['name'] . " name='" . $_MODULE['type'] . $rowFields['name'] . "' value='{$rowFetch[$_MODULE['cvalue']]}'>";
	}
?>
