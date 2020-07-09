<?php

$content = file_get_contents('php://input');
$i = $_GET['i'];
$date = date('YmdHi',time());
$now_path = dirname(dirname(__FILE__));
$save_path = $now_path.'/logs';
if (!is_dir($save_path)) {
    mkdir($save_path);
}
$save_path = $save_path.'/'.$i;
if (!is_dir($save_path)) {
    mkdir($save_path);
}
$path = $save_path.'/'.$date.'.log';
if (file_exists($path)) {
    @$file = fopen($path,'a');
    fwrite($file, "--\r\n" . $content);
    fclose($file);
} else {
    @$file = fopen($path,'w');
    fwrite($file,$content);
    fclose($file);
}
echo '{"code":1}';
exit();