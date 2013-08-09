<?
//Letters used: b,e,g,i,l,p,t,q,y,z

function lock($str){
	$enc[0] = Array(":","1","2","3","4","5","6","7","8","9","0",".", "p");
	$enc[1] = Array("t","i","e","g","q","z","y","f","l","b","c","a", "r");
	$enc[2] = Array("d","q","c","t","f","g","y","k","b","a","w","s", "o");
	$enc[3] = Array("q","w","s","d","c","v","b","t","y","u","f","g", "h");
	$enc[4] = Array("z","x","a","s","q","w","h","j","v","n","b","c", "g");
	$enc[5] = Array("t","c","b","q","w","j","l","d","r","a","y","n", "x");
	$str = str_split($str);
	
	$key = rand(1,5);
	$locked = str_replace($enc[0], $enc[$key], $str);
	$last = sizeof($locked) + 1;
	$locked[$last] = $key;
	$locked = implode($locked);

	return $locked;
}

function unlock($str){
	$enc[0] = Array(":","1","2","3","4","5","6","7","8","9","0",".", "p");
	$enc[1] = Array("t","i","e","g","q","z","y","f","l","b","c","a", "r");
	$enc[2] = Array("d","q","c","t","f","g","y","k","b","a","w","s", "o");
	$enc[3] = Array("q","w","s","d","c","v","b","t","y","u","f","g", "h");
	$enc[4] = Array("z","x","a","s","q","w","h","j","v","n","b","c", "g");
	$enc[5] = Array("t","c","b","q","w","j","l","d","r","a","y","n", "x");
	
	$key = substr($str, strlen($str)-1, 1);

	$str = substr($str, 0, strlen($str) - 1);

	$str = str_split($str);
	$unlocked = str_replace($enc[$key], $enc[0], $str);
	$unlocked = implode("", $unlocked);
	return $unlocked;
}
?>
