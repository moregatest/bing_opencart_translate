<?php
require_once 'vendor/autoload.php';
define('BING_PWS','your bing app password');
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
define('__ORG__',__DIR__ . DIRECTORY_SEPARATOR . 'org' . DIRECTORY_SEPARATOR);
define('__AFTER__',__DIR__ . DIRECTORY_SEPARATOR . 'after' . DIRECTORY_SEPARATOR);
require_once 'BingTranslator.class.php';
require_once 'curl.class.php';
include __DIR__ . '/functions.php';
$cli_cmd = new Commando\Command();
$cli_cmd->option('s')
->aka('source')->default('en-gb')
->describedAs("support single file(.php) or folder")
->must(function($p){
    echo __ORG__ . $p;
	return file_exists( __ORG__ . $p);
});
$cli_cmd->option('t')->aka('target')->require()->describedAs("must be a vaid language")->must(function($p) {        
        $lngs = getLngs();
		return isset($lngs[$p]);
    });
$fds = array();
$source     = __ORG__ . $cli_cmd['source'];
$reference  = __ORG__ . $cli_cmd['target'];
if(is_dir($source)){
    $fds = preg_ls($source,true,"#.+\.(.+)$#i");    
}elseif(file_exists($source)){
    $fds[] = $source;
}
$logger = new Katzgrau\KLogger\Logger(__DIR__.'/logs');
	
$btr = new BingTranslator();
//echo $btr->Translate("tienen distribuidor en Europa????");

$target = $cli_cmd['target'];
foreach($fds as $fn){
    echo $fn,"\n";
    $s_ = loadLng($fn);    
    
    //目標語系參考檔案路徑
    $reference_fn =  getReference($fn,$cli_cmd['source'],$cli_cmd['target']);
    echo $reference_fn,"\n";
    $r_ = loadLng($reference_fn);
    //var_dump($r_);
    
    $_ = array();
    $transalte_count = 0;    
    foreach($s_ as $k => $v){
        if($transalte_count > 9) {continue;}
        if(isset($r_[$k])){
            $_[$k] = $r_[$k];
        }else{
            $transalte_count ++;
            $_[$k] = $btr->Translate($v,'en',$cli_cmd['target']);
            echo "$v\n",$_[$k],"\n";
        }
    }    
    $target_fn = getTarget($fn,$cli_cmd['source'],$cli_cmd['target']);
    write($target_fn,$_);
    echo $target_fn,"\n";
    //exit();
}
