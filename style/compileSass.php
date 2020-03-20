<?php

$complete = (in_array("complete", $argv) || key_exists("complete", $_GET));

if(file_exists(__DIR__."/compileSass.json")){
    $json = json_decode(file_get_contents(__DIR__."/compileSass.json"), true);
}else{
    $json = [];
}

$subdirs = scandir(__DIR__."/scss");

$json["modified"] = ($json["modified"] ?? 0);
$modified = $json["modified"];

foreach($subdirs as $subdir){
    if(!is_dir(__DIR__."/scss/{$subdir}") || in_array($subdir, [".", ".."])){
        continue;
    }
    $files = scandir(__DIR__."/scss/{$subdir}");
    foreach($files as $file){
        if(substr($file, -5) === ".scss"){
            $input = __DIR__."/scss/{$subdir}/{$file}";
            $output = str_replace("scss", "css", $input);

            filemtime($input);
            if(filemtime($input) > $modified){
                $modified = filemtime($input);
            }
            if($complete || filemtime($input) > $json["modified"]){
                echo "{$input} > {$output}\n";
                exec("sass {$input} {$output}\n");
            }
        }
    }
}

$json["modified"] = $modified;
file_put_contents(__DIR__."/compileSass.json", json_encode($json));
