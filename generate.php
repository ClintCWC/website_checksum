<?php

chdir ('..');
$dir = getcwd();
$file_list = find_all_files($dir);
$xml_data = "<?xml version='1.0' encoding='ISO-8859-1'?>\n<root>\n";
//header("Content-type: text/xml");
foreach ($file_list as $key => $file){
	$hash = create_hash($file);
	if ($hash){
		$hash_list[$file] = $hash;
		$xml_data .= "<item>\n";
		$xml_data .= "<filename><![CDATA[$file]]></filename>\n";
		$xml_data .= "<filehash>$hash</filehash>\n";
		$xml_data .= "</item>\n";
	}
}
$xml_data .= '</root>
';


if (xml_save_file('checksum/checksum_file.xml', $xml_data)){echo '<a href = "checksum_file.xml">Download file</a>';}else{die ('file not writen');}


function xml_save_file($file, $xml_data){
	$result = file_put_contents($file, $xml_data);
	return $result;
}


function create_hash ($file){
	if (strpos($file,'tpl.php')){return (false);}//ignore template cache
	if (file_exists($file)){
		$hash = md5_file($file);
		return $hash;
	}
	return (false);
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