<?php

function get_files ($dir = ".") {
     $files = array();  
     if ($handle = opendir($dir)) {     
          while (false !== ($item = readdir($handle))) {        
               if (is_file("$dir/$item")) {
                    $files[] = "$dir/$item";
               }        
               elseif (is_dir("$dir/$item") && ($item != ".") && ($item != "..")){
                    $files = array_merge($files, get_files("$dir/$item"));
               }
          } 
          closedir($handle);
     }
     arsort($files);
     return $files; 
}

function get_files_from_db()
{
     
}