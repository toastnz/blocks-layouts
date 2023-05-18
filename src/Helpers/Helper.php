<?php

namespace Toast\Blocks\Helpers;

use SilverStripe\Core\Environment;
use SilverStripe\Security\Security;
use SilverStripe\Security\Permission;
use SilverStripe\Control\Director;
use SilverStripe\Control\Controller;
use Toast\Tasks\GenerateThemeCssFileTask;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Core\Config\Config;
use DirectoryIterator;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Subsites\Model\Subsite;
use SilverStripe\Subsites\State\SubsiteState;

class Helper 
{
    static function getAvailableBlocks()
    {
        $availableBlocks = Config::inst()->get('Toast\Blocks\Extensions\PageExtension', 'available_blocks');
        if (!file_exists(BASE_PATH . '/' . $availableBlocks)) {
            return ;
        }
        return $availableBlocks;
    }

    static function getLayoutSrc()
    {
        $availableBlocks = Config::inst()->get('Toast\Blocks\Extensions\PageExtension', 'layout_src');

        if (!file_exists(BASE_PATH . '/' . $availableBlocks)) {
            return ;
        }

        return $availableBlocks;
    }

    static function getLayoutIconSrc()
    {
        $availableBlocks = Config::inst()->get('Toast\Blocks\Extensions\PageExtension', 'layout_icon_src');
        if (!file_exists(BASE_PATH . '/' . $availableBlocks)) {
            return ;
        }
        return $availableBlocks;
    }

    static function getAvailableBlocksLayouts($block, $src, $imgsrc, $default = true)
    { 
        $optionset = [];
        // if this is optional directory
        // TODO: merge layouts with other specified folder in yml
        // Scan each directory for files

        $optionset = self::getLayoutIcons($block, $src, $imgsrc, $default );

        return $optionset;
    }


    static function getLayoutIcons($block,$src, $imgsrc, $default = true)
    {
       
        $currentTemplateName = strtolower($block->getBlockTemplateName());
        $optionset = [];
        $layouts = [];
        $icons = [];
        $basePath = BASE_PATH . '/' ;
        
        $extensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg');

        if (is_dir($src) && is_dir( $basePath  .$imgsrc)) {     
            // get icons for layouts
            // if folder not exist in app, fall back to module default
            if ($directory = new DirectoryIterator($imgsrc)){
              
                $imgsrc_dir = basename( $basePath . $imgsrc);
                $src_dir = basename( $basePath . $src);
                foreach ($directory as $fileinfo){
                   
                    if ($fileinfo->isFile()){
                        $imgfilename = $fileinfo->getFilename();
                        $extension = strtolower(pathinfo($imgfilename, PATHINFO_EXTENSION));
                        // Only add to our available icons if it's an extension we're after
                        if (in_array($extension, $extensions)){					
                            $imgfilename = pathinfo($imgfilename)['filename'];
                            $icons[$imgsrc.$fileinfo->getFilename()] = $imgfilename;
                        }
                    }
                } 
                
                foreach (glob($src . "*.ss") as $filename) {
                    if ($name = pathinfo($filename)['filename']){
                            $name = strtolower($name);
                    }
                    $className = 'Toast\Blocks\\' . $src_dir . '\\' . pathinfo($filename)['filename'];
                    // if this block is in the icon array value, add it to the optionset
                    if ($name == $currentTemplateName && in_array($name, array_values($icons))) {
                        // get the index 
                        $index = array_search($name, array_values($icons));
                        // array of keys in the img src array
                        $filepath_keys   = array_keys( $icons );
                        // assign img path 
                        $matchedBlockImgSrcPath = $filepath_keys[$index];
                    
                        // If the matchedBlockImgSrcPath is a .svg file
                        if (pathinfo($matchedBlockImgSrcPath, PATHINFO_EXTENSION) == 'svg') {
                            // Get the file contents from the matchedBlockImgSrcPath
                            $svg = file_get_contents($matchedBlockImgSrcPath);
                            $thumbnail = $svg;
                        } else {
                            $thumbnail = '<img src="' . $matchedBlockImgSrcPath . '" />';
                        }
                    
                        $html = '<div class="blockThumbnail">' . $thumbnail . '</div><strong class="title" title="Template file: ' . $filename . '">'.$imgsrc_dir.'</strong>';
                        $optionset[$className] = DBField::create_field(DBHTMLText::class, $html);
                    }
                }   
            }
             
		}
        return $optionset;
    }

    static function getThemes()
    {
        // display all themes in the themes folder
        $themes = [];
        if (is_dir(THEMES_PATH)) {
            foreach (scandir(THEMES_PATH) as $theme) {
                if ($theme[0] == '.') {
                    continue;
                }
                $theme = strtok($theme, '_');
                $themes[] = $theme;
            }
            ksort($themes);
        }
        return $themes;
    }
}