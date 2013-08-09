<!--

SEO Friendly URL Module

Input Param:
file: Dir where the file is
trans: Field to translate
field: field to store translation

***NOTE****

This Module requires steamEngine 0.9 at least.

-->






<script language=javascript>
function seo(field){
		x = field.value;
		x = x.replace(/ /g, "-");
		x = x.replace(/[!?,.;"'()\[\]&@’]/g, "");
		x = x.replace(/&#146;/g, "");
		document.getElementById('A<?=$_MODULE['field']?>').value = x;
	}

</script>

<input type=text id='A<?=$_MODULE['field']?>' name='A<?=$_MODULE['field']?>' value='<?=$rowFetch[$_MODULE['field']]?>' readonly />
<input type="button" onClick="seo(document.getElementById('A<?=$_MODULE['trans'];?>'))" value="Generate" />
