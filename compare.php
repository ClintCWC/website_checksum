<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
.hashcheck{
	font-family: "Palatino Linotype", "Book Antiqua", Palatino, serif;
	font-size: 11px;
}
.hashfail_file  {
	color: #900;
}
.hashfail_image {
	color: #999;
}
</style>
</head>
<body>
<p>
  <?php
echo '<div class="hashcheck">'."\n";
chdir ('..');
$dir = getcwd();
$file_list = find_all_files($dir);

foreach ($file_list as $key => $file){
	$hash = create_hash($file);	
	$hash_list[$file] = $hash;
}

$file = 'checksum/checksum_file.xml';

if (file_exists($file)) {
    $stored_xml_hash_list = simplexml_load_file($file);
} else {
    exit('Failed to open '.$file);
}


foreach ($stored_xml_hash_list as $item){
	//convert to simple array
	$stored_hash_list[(string)$item->filename] = (string)$item->filehash;
}

foreach ($hash_list as $file => $hash){
	if (!isset($stored_hash_list[$file])){
		$stored_hash_list[$file] = false;
	}
	if (compare_hashes($file,$stored_hash_list[$file],$hash)){
		continue;
	}else{
		$cssclass = 'hashfail_file';
		$modified_date = '';
		if (stripos($file,'.jpg')){$cssclass = 'hashfail_image';}//change class to de-emphasize jpg's that fail
		if (stripos($file,'.png')){$cssclass = 'hashfail_image';}//change class to de-emphasize png's that fail
		if (stripos($file,'.gif')){$cssclass = 'hashfail_image';}//change class to de-emphasize gif's that fail
		if (stripos($file,'.zip')){$cssclass = 'hashfail_image';}//change class to de-emphasize gif's that fail
		if (!$stored_hash_list[$file]){$stored_hash_list[$file]= 'missing';}
		if ($cssclass == 'hashfail_file'){$modified_date = ' - File modified date <b>'.date ("d-m-y H:i.", filemtime($file)).'</b>';}
		
		
		echo '<div class="'.$cssclass.'">FAILED: <b>'.$file. '</b> <i> - stored hash '.$stored_hash_list[$file].$modified_date.'</i></div>'."\n";
	}
}
 
echo '</div>'; 

function compare_hashes($filename,$hash1,$hash2){
	
	if (strpos($filename,'tpl.php')){return true;}//ignore template cache
	if (strpos($filename,'/cache/')){return true;}//ignore template cache
	if (strpos($filename,'templates_c')){return true;}//ignore template cache
	if(!$hash1){return false;}
	if($hash1 == $hash2){return true;}
	return false;
}

function create_hash ($file){
	if (file_exists($file)){
		$hash = md5_file($file);
		return $hash;
	}
	return ($file . ' does not exist');
}

function find_all_files($dir)
{
    $root = scandir($dir);
    foreach($root as $value)
    {
		$path = "$dir/$value";
        if($value === '.' || $value === '..') {continue;}
        if(is_file($path)) {$result[]=$path;continue;}
		if (count(scandir($path)) == 2){continue;}
		//echo 'result '.count(scandir($path)).' - '.$dir.'/'.$value.'<br/>';
		if ($dir!="" and $value !=""){
        	foreach(@find_all_files($path) as $value)
        	{
            	$result[]=$value;
        	}
		}
    }
    return $result;
} 


?>
</body>
</html>