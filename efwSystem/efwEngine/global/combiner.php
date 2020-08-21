<?php


namespace efwEngine;


class combiner
{
    static function findCssLinks($file){
        $file = file_get_contents($file);

        $doc = new \DOMDocument();
        $doc->loadHTML($file);
        $domcss = $doc->getElementsByTagName('link');
        foreach($domcss as $links) {
            if( strtolower($links->getAttribute('rel')) == "stylesheet" ) {
                echo "This is:". $links->getAttribute('href') ."<br />";
            }
        }
    }
    static function combineDistDir(){
        foreach (new \DirectoryIterator('dist') as $fileInfo) {
            if($fileInfo->isDot()) continue;
            self::findCssLinks("dist/".$fileInfo->getFilename());
        }
    }
}