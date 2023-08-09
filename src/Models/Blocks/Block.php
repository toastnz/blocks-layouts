<?php

namespace Toast\Blocks;


use Page;
use ReflectionClass;
use SilverStripe\ORM\DB;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Member;
use SilverStripe\Control\Director;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Security\Permission;
use SilverStripe\Versioned\Versioned;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_Base;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\CMS\Controllers\CMSPageEditController;
use SilverStripe\View\SSViewer;
use SilverStripe\SiteConfig\SiteConfig;
use Toast\Blocks\Helpers\Helper;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\View\Requirements;

class Block extends DataObject
{
    private static $table_name = 'Blocks_Block';

    private static $singular_name = 'Block';

    private static $plural_name = 'Blocks';

    private static $db = [
        'Title'         => 'Varchar(255)',
        'Template'      => 'Varchar',
    ];

    private static $casting = [
        'Icon' => 'HTMLText'
    ];

    private static $summary_fields = [
        'IconForCMS'        => 'Type',
        'Title'             => 'Title',
        'ContentSummary'    => 'Content',
        'Template'          => 'Template'
    ];

    private static $searchable_fields = [
        'Title'
    ];

    private static $extensions = [
        Versioned::class
    ];

    private static $versioned_gridfield_extensions = true;

    public function getIconForCMS()
    {
        if(self::config()->get('block-icon') == null){
            return;
        }
        
        $icon = str_replace('[resources]', RESOURCES_DIR, self::config()->get('block-icon'));

        return DBField::create_field('HTMLText', '
            <div title="' . $this->i18n_singular_name() . '" style="margin: 0 auto;width:50px; height:50px; white-space:nowrap; ">
                <img style="width:100%;height:100%;display:inline-block !important" src="' . $icon . '">
            </div>
            <span style="font-weight:bold;color:#377cff;display:block;line-height:10px;text-align:center;margin:0px 0 0;padding:0;font-size:10px;text-transform:uppercase;">' . $this->i18n_singular_name() . '</span>
        ');
    }

    public function IconForCMS()
    {
        return $this->getIconForCMS();
    }

    public function forTemplate()
    {
        $template = $this->Template;
  
        $this->extend('updateBlockTemplate', $template);
        // load css file if exists in directory specified in config yml
        if ($cssFilePath = $this->getCSSFile()){
            Requirements::css($cssFilePath);
        }
        return $this->renderWith([$template, 'Toast\Blocks\Default\Block']);
    }

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            if ($this->ID) {
                $fields->addFieldsToTab('Root.More', [
                    LiteralField::create('BlockLink', 'Block Link <br><a href="' . $this->AbsoluteLink() . '" target="_blank">' . $this->AbsoluteLink() . '</a><hr>'),
                    ReadonlyField::create('Shortcode', 'Shortcode', '[block,id=' . $this->ID . ']')
                ]);
            }

            $fields->removeByName([
                'Template'
            ]);

            $fields->addFieldsToTab('Root.Main', [
                TextField::create('Title', 'Title')
                    ->setDescription('Title used for internal reference only and does not appear on the site.')
            ]);

            if ($layoutOptions = $this->getBlockLayouts()){
                // Add the $layoutOptions to the Main tab, AFTER the Title field
                $fields->insertAfter('Title', $layoutOptions);
            }

        });

        return parent::getCMSFields();
    }

    public function getBlockLayouts()
    {
        // scan the app directory for block layouts and return them as an array
        $layouts = [];
        $optionalLayouts = [];
        $baseFolder = Director::baseFolder();
        $theme = Helper::getThemes();
        // module dir
        $module_src = BASE_PATH . '/' . TOAST_BLOCKS_DIR . '/' . TOAST_BLOCKS_TEMPLATE_DIR  . '/' ;
        $module_imgsrc =  TOAST_BLOCKS_IMAGE_DIR . '/client/images/layout-icons/default/' ;
        // $module_imgsrc = BASE_PATH . '/vendor/toastnz/' . TOAST_BLOCKS_DIR . '/client/images/layout-icons/default/' ;
       // get default layouts
        $layouts = Helper::getAvailableBlocksLayouts($this, $module_src, $module_imgsrc, true);
   
        // alternate layouts if specified
        if ($layout_src = Helper::getLayoutSrc()){
            $layout_src = BASE_PATH . '/' . $layout_src;
            // NOTE: I have commented this out as this is what I believe was causing templates to not display for me.
            // get layout dir from yml
            // if (!$layout_imgsrc = Helper::getLayoutIconSrc()){
            //     return;
            // }
            $dirs = array_values(array_diff(scandir('/'.$layout_src), array('.', '..')));
            foreach ($dirs as $dir) { 
                 if (!$layout_imgsrc = Helper::getLayoutIconSrc()){
                    // NOTE: I have updated this return to a continue as this is what I believe was causing templates to not display for me.
                    continue;
                }
                $optionalSrcPath = $layout_src . '/' . $dir . '/';
                $optionalImgSrcPath = $layout_imgsrc . '/' . strtolower($dir) . '/';

                $optionalLayouts[] = Helper::getAvailableBlocksLayouts($this, $optionalSrcPath, $optionalImgSrcPath, false);
            }
        }

        if (count($optionalLayouts) > 0){
            foreach($optionalLayouts as $layout){
                if ($layout){
                    // merge alternate layouts with default layout
                    $layouts = array_merge($layouts, $layout);
                }
            }
        }

         if (count($layouts) > 0){
            $tplField = OptionsetField::create(
                "Template",
                "Choose a layout",
                $layouts,
                $this->Template
            )->addExtraClass('toast-block-layouts');
            return $tplField;
        }
        return ;
    }

    public function getLayoutDirs(){
        if ($layout_src = Helper::getLayoutSrc()){
            $layout_src = BASE_PATH . '/' . $layout_src;
            $dirs = array_values(array_diff(scandir('/'.$layout_src), array('.', '..')));
            $output = [];
            foreach ($dirs as $dir) { 
                $output[] = strtolower($dir);
            }
            return $output;
        }
    }

    public function getCSSFile()
    {
        $cssFilePath = null;
        if($cssDir = Config::inst()->get('Toast\Blocks\Extensions\PageExtension', 'layout_dist_dir')){
            if($this->Template){
                // check block template dir for css file
               $template =explode('\\', $this->Template);
               // layoutname is always going to be in the pos 2 of the array
               if(isset($template[2])){
                   $layoutName = strtolower($template[2]);
                   $cssFilePath = BASE_PATH . '/' . $cssDir . '/' . $layoutName . '-' . strtolower($this->getBlockTemplateName()) . '.css';
                   if (file_exists($cssFilePath)){
                       // $cssFilePath =   '/' .$cssDir . '/' . $layout . '-' . strtolower($this->getBlockTemplateName()) . '.css';
                       $cssFilePath =   $cssDir . '/' . $layoutName . '-' . strtolower($this->getBlockTemplateName()) . '.css';
                       
                   }
               }
           }
        }

        $this->extend('updateBlockTemplateCSS', $cssFilePath);

        return $cssFilePath;

    }

    public function onBeforeWrite()
    {
        if (!$this->Template){
            $this->Template =  $this->getTemplateClass();
        }
         parent::onBeforeWrite();
     
    }

    public function getTemplateClass()
    {
        return 'Toast\Blocks\\Default\\' . $this->getBlockTemplateName();
    }

    public function populateDefaults()
	{
        if (!$this->Template){
            $this->Template =  $this->getTemplateClass();
        }
		parent::populateDefaults();
	}

    public function getContentSummary()
    {
        return DBField::create_field(DBHTMLText::class, '');
    }

    public function getTitle()
    {
        if ($this->ID) {
            return $this->getField('Title') ?: $this->i18n_singular_name();
        } else {
            return $this->getField('Title');
        }
    }

    public function getApiURL()
    {
        return Controller::join_links(Controller::curr()->AbsoluteLink(), 'Block', $this->ID);
    }

    public function getLink($action = null)
    {
        $parent = $this->getParentPage();

        if ($parent && $parent->exists()) {
            return $parent->Link($action) . '#' . $this->getHtmlID();
        }

        $parent = Page::get()->leftJoin('Page_ContentBlocks', '"Page_ContentBlocks"."PageID" = "SiteTree"."ID"')
            ->where('"Page_ContentBlocks"."Blocks_BlockID" = ' . $this->ID)
            ->first();

        if ($parent && $parent->exists()) {
            return $parent->Link($action) . '#' . $this->getHtmlID();
        }

        return '';
    }

    public function Link($action = null)
    {
        return $this->getLink($action);
    }

    public function getAbsoluteLink($action = null)
    {
        return Controller::join_links(Director::absoluteBaseURL(), $this->Link($action));
    }

    public function AbsoluteLink($action = null)
    {
        return $this->getAbsoluteLink($action);
    }

    public function getBlockTemplateName()
    {
        $reflect = new ReflectionClass($this);

        $templateName = $reflect->getShortName() ?: '';

        return $templateName;
    }

    public function getHtmlID()
    {
        $reflect = new ReflectionClass($this);

        $templateName = $reflect->getShortName() ?: $this->ClassName;

        return $templateName . '_' . $this->ID;
    }

    public function getDisplayTitle()
    {
        $title = $this->Title;

        $parent = $this->getParentPage();

        if ($parent && $parent->exists()) {
            $title .= ' (on page ' . $parent->Title . ')';
        }

        return $title;
    }

    public function getImageFocusPosition($imageid = null)
    {
        // If we don't have an image, return nothing
        if (!$imageid) return;
        // get image by id
        if(!$image = Image::get()->byID($imageid)) return;
        // Make sure the image is an instance of Image
        if (!$image instanceof Image) return;
        // Make sure there is a focus point
        if (!$image->FocusPoint) return;

        // Get the image focus point
        $focusPoint = $image->FocusPoint;
        return $focusPoint->PercentageX() . '% ' . $focusPoint->PercentageY() . '%';
    }

    public function canView($member = null)
    {
        if ($member && Permission::checkMember($member, ["ADMIN", "SITETREE_VIEW_ALL"])) {
            return true;
        }

        $extended = $this->extendedCan('canView', $member);

        if ($extended !== null) {
            return $extended;
        }

        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canEdit($member = null)
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canDelete($member = null)
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canCreate($member = null, $context = [])
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canDeleteFromLive($member = null)
    {
        $extended = $this->extendedCan('canDeleteFromLive', $member);

        if ($extended !== null) {
            return $extended;
        }

        return $this->canPublish($member);
    }

    public function canPublish($member = null)
    {
        if (!$member || !(is_a($member, Member::class)) || is_numeric($member)) {
            $member = Member::currentUser();
        }

        if ($member && Permission::checkMember($member, "ADMIN")) {
            return true;
        }

        $extended = $this->extendedCan('canPublish', $member);
        if ($extended !== null) {
            return $extended;
        }

        return $this->canEdit($member);
    }

    public function isPublished()
    {
        if ($this->isNew()) {
            return false;
        }

        return (DB::prepared_query("SELECT \"ID\" FROM \"Blocks_Block_Live\" WHERE \"ID\" = ?", [$this->ID])->value())
            ? true
            : false;
    }

    public function isNew()
    {
        if (empty($this->ID)) {
            return true;
        }

        if (is_numeric($this->ID)) {
            return false;
        }

        return stripos($this->ID, 'new') === 0;
    }

    public function getParentPage()
    {
        if ($controller = Controller::curr()) {
            if (!$controller instanceof CMSPageEditController) {
                try {
                    if ($data = $controller->data()) {
                        if ($data->ID) {
                            return SiteTree::get()->byID($data->ID);
                        }
                    }
                } catch (\Exception $e) {                    
                }
            }
        }
    }

    public function doArchive()
    {
        $this->invokeWithExtensions('onBeforeArchive', $this);

        $thisID = $this->ID;

        if (!$this->isPublished() || $this->doUnpublish()) {
            $this->delete();

            DB::prepared_query("DELETE FROM \"Page_ContentBlocks\" WHERE \"Blocks_BlockID\" = ?", [$thisID]);

            $this->invokeWithExtensions('onAfterArchive', $this);

            return true;
        }

        return false;
    }

    public function canArchive($member = null)
    {
        if (!$member) {
            $member = Member::currentUser();
        }

        $extended = $this->extendedCan('canArchive', $member);
        if ($extended !== null) {
            return $extended;
        }

        if (!$this->canDelete($member)) {
            return false;
        }

        if ($this->ExistsOnLive && !$this->canDeleteFromLive($member)) {
            return false;
        }

        return true;
    }

    public function getPage() {
        $currentController = Controller::curr();
        $parent = \Page::get()->leftJoin('Page_ContentBlocks', '"Page_ContentBlocks"."PageID" = "SiteTree"."ID"')
            ->where('"Page_ContentBlocks"."Blocks_BlockID" = ' . $this->owner->ID)
            ->where('"Page_ContentBlocks"."PageID" = ' . $currentController->ID)
            ->first();

        // get the page that has this block
        if ($parent && $parent->exists()) {
            return $parent;
        }

        return;
    }

}
