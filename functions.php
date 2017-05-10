<?php 

function preg_ls($path = ".", $rec = false, $pat = "/.*/") {	
	// it's going to be used repeatedly, ensure we compile it for speed.
	//$pat=preg_replace("|(/.*/[^S]*)|s", "\\1S", $pat);
	//Remove trailing slashes from path
	while (substr($path, -1, 1) == "/") $path = substr($path, 0, -1);
	//also, make sure that $path is a directory and repair any screwups
	if (!is_dir($path)) $path = dirname($path);
	//assert either truth or falsehoold of $rec, allow no scalars to mean truth
	if ($rec !== true) $rec = false;
	//get a directory handle
	//$firephp->log($path,"¤ÀªR¸ô®|");
	if (!is_dir($path)) {
		return false;
	}
	$d = @dir($path);
	//initialise the output array
	$ret = Array();
	//loop, reading until there's no more to read
	while (false !== ($e = $d->read())) {
		//Ignore parent- and self-links
		if (($e == ".") || ($e == "..")) continue;
		//If we're working recursively and it's a directory, grab and merge
		if ($rec && is_dir($path . DIRECTORY_SEPARATOR . $e)) {
			$ret = array_merge($ret, preg_ls($path . DIRECTORY_SEPARATOR . $e, $rec, $pat));
			continue;
		}
		//If it don't match, exclude it
		if (!preg_match($pat, $e)) continue;
		$ret[] = $path . DIRECTORY_SEPARATOR . $e;
	}
	//finally, return the array
	return $ret;
}
function loadLng($fn){
    $_ = array();
    if(file_exists($fn)){
        include($fn);
    }else{
       // throw new Exception("[ERROR] $fn");
    }
    return $_;
}
function getReference($filepath,$s='en-gb',$t='zh-CHT'){
    $delimiter = DIRECTORY_SEPARATOR;
    $search = DIRECTORY_SEPARATOR.$s.DIRECTORY_SEPARATOR;
    $replace = DIRECTORY_SEPARATOR.$t.DIRECTORY_SEPARATOR;
    $reference = str_replace($search,$replace,$filepath);
    $basename = ltrim(substr($filepath, strrpos($filepath, $delimiter)),$delimiter);
    if($basename == $s.'.php'){
        $search = $s.'.php';
        $replace = $t.'.php';
        $reference = str_replace($search,$replace,$reference);        
    }
    return $reference;   
}
function write($fn,$_){
    $tmp = pathinfo($fn);
    if(!is_dir($tmp['dirname'])){
        mkdir($tmp['dirname'],0777,true);
    }
    $cont = '<?php' . "\n" . '$_ = ';
    $cont .= var_export($_,true) . ';' . "\n" . '?>';
    file_put_contents($fn,$cont);    
}
function getTarget($filepath,$s='en-gb',$t='zh-CHT'){
    $delimiter = DIRECTORY_SEPARATOR;
    $search = __ORG__ . $s . DIRECTORY_SEPARATOR;
    $replace = __AFTER__ . $t . DIRECTORY_SEPARATOR;
    $reference = str_replace($search,$replace,$filepath);
    $basename = ltrim(substr($filepath, strrpos($filepath, $delimiter)),$delimiter);
    if($basename == $s.'.php'){
        $search = $s.'.php';
        $replace = $t.'.php';
        $reference = str_replace($search,$replace,$reference);
        var_dump($reference);
    }
    return $reference;   
}  
function getLngs(){
	$all = <<<EOF
ar	Arabic
cs	Czech
da	Danish
de	German
en	English
et	Estonian
fi	Finnish
fr	French
nl	Dutch
el	Greek
he	Hebrew
ht	Haitian Creole
hu	Hungarian
id	Indonesian
it	Italian
ja	Japanese
ko	Korean
lt	Lithuanian
lv	Latvian
no	Norwegian
pl	Polish
pt	Portuguese
ro	Romanian
es	Spanish
ru	Russian
sk	Slovak
sl	Slovene
sv	Swedish
th	Thai
tr	Turkish
uk	Ukrainian
vi	Vietnamese
zh-CHS	Simplified Chinese
zh-CHT	Traditional Chinese
EOF;
	$tmp = explode("\n",$all);
	unset($all);
	$lngs = array();
	foreach($tmp as $ln){
		if(empty($ln)){continue;}
		$ln = trim($ln);
		list($code,$lng) = explode("\t",$ln);
		$lngs[$code] = $lng;
	}
	return $lngs;
}
?>