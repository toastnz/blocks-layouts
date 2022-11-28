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

  
    static function getThemeColoursArray(){

        $array=[];
        
        $array = self::getDefaultColours();
        
        $siteConfig = SiteConfig::current_site_config();
        if ($colours = $siteConfig->ThemeColours()->map('Title','Colour')){
            foreach($colours as $key => $colour){
                // convert string to lowercase and replace whitespaces with hyphen
                $classTitle = strtolower(str_replace(" ","-",$key));
                $array[$classTitle] = '#' . $colour;
            }
                // return $array;
        }
        if (class_exists(Subsite::class)){
            if(self::isSubsite()){
                $subsite = Subsite::currentSubsite();
                if ($colours = $subsite->ThemeColours()->map('Title','Colour')){
                    foreach($colours as $key => $colour){
                        // convert string to lowercase and replace whitespaces with hyphen
                        if ($colour){
                            $classTitle = strtolower(str_replace(" ","-",$key));
                            $array[$classTitle] = '#' . $colour;
                        }
                    }
                    
                }
            }
        }  
        return $array;
        
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
                        $thumbnail = '<img src="' . $matchedBlockImgSrcPath . '" />';
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

    static function getDefaultColours()
    {
        $defaults = ['white','black','none'];
        return $defaults;
    }

    static function getColourClassName($title = null)
    {
        if ($title){
            $siteConfig = SiteConfig::current_site_config();
            if(!$colours = $siteConfig->ThemeColours()){
                return '';
            }

           $defaults = self::getDefaultColours();
            // return default if colour title is in defaults
            if (in_array($title, $defaults)){
                return $title;
            }

            if($selectedColour = $colours->filter('Title',$title)){
                if($selectedColour->exists()){
                    return $selectedColour->first()->ColourClassName;
                }
            }
        }
    }

    static function getColourForTemplate($title = null)
    {
        if(!$title){
            return 'white light';
        }

        $classname = self::getColourClassName($title);
        $lightOrDark = self::getLightOrDark($title);
        if ($classname){
            return $classname . ' '. $lightOrDark;
        }
    }

    // Function to calculate if a colour is light or dark
    static function getLightOrDark($string = null)
    {
        if (!$string){
            return '';
        }

        $string = self::getColourClassName($string);
   
        $siteConfig = SiteConfig::current_site_config();

        if(!$colours = $siteConfig->ThemeColours()){
            return '';
        }
   
        if ($string == 'white') {
            return 'light';
        }
   
        if ($string == 'black') {
            return 'dark';
        }
   
        if($selectedColour = $colours->filter('ColourClassName',$string)){
        
            if($selectedColour->exists()){
                $hex = $selectedColour->first()->getHexColourCode();
                $hexWithoutHash = str_replace('#', '', $hex);
                $r = hexdec(substr($hexWithoutHash,0,2));
                $g = hexdec(substr($hexWithoutHash,2,2));
                $b = hexdec(substr($hexWithoutHash,4,2));
                  
                $yiq = (($r*299)+($g*587)+($b*114))/1000;

                return ($yiq >= 130) ? 'light' : 'dark';
            }
        }
    }


}