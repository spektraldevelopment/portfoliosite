<!--

Link table drop down Module:

Input Param:
file: Dir where the file is
imgpath: path to the images.
fieldname: field name
textparam: Text area parameters

-->
<script language=javascript>

	function addImg<? echo $_MODULE['fieldname']; ?>(i){
		document.getElementById('<? echo $_MODULE['fieldname']; ?>img').innerHTML += "[IMG" + i + "]<input type=file id=f" + i + " name=f" + i + "><br><input type=hidden name='XDIRf" + i + "' value='<? echo $_MODULE['imgpath']; ?>'>";
		document.getElementById('<? echo $_MODULE['fieldname']; ?>imgnum').value = i;
	}

	function format<? echo $_MODULE['fieldname']; ?>(id){
		//text = document.getElementById('form<? echo $_MODULE['fieldname']; ?>').value;
		var editorInstance = tinyMCE.getInstanceById('form<? echo $_MODULE['fieldname']; ?>');
		text = editorInstance.getContent();
		for (x = 1; x <= document.getElementById('imgnum').value; x++){
			split = document.getElementById('f' + x).value.split('\\');
			text = text.toLowerCase().replace('[img' + x +']', '<img border=0 src="<? echo $_MODULE['imgpath']; ?>' + split[0] + '">');
		}
		editorInstance.setContent(text);
		//document.getElementById('form<? echo $_MODULE['fieldname']; ?>').value = text;
	}

</script>
<div id=<? echo $_MODULE['fieldname']; ?>img></div>
<input type="button" onClick="format<? echo $_MODULE['fieldname']; ?>('form<? echo $_MODULE['fieldname']; ?>');" value="Format">
<input type="hidden" value=0 id ="<? echo $_MODULE['fieldname']; ?>imgnum">
<a onClick="addImg<? echo $_MODULE['fieldname']; ?>(Number(document.getElementById('<? echo $_MODULE['fieldname']; ?>imgnum').value) + 1);">Add new image</a><br>
<textarea id=form<? echo $_MODULE['fieldname']; ?> name=T<? echo $_MODULE['fieldname']; ?> <? echo $_MODULE['textparam']; ?>><? echo $rowFetch[$_MODULE['fieldname']] ?></textarea>

