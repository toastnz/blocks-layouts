<?php

namespace Toast\Blocks;


use Page;
use ReflectionClass;
use SilverStripe\ORM\DB;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;
use Toast\Blocks\Helpers\Helper;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Member;
use SilverStripe\Control\Director;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Security\Security;
use SilverStripe\View\Requirements;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Security\Permission;
use SilverStripe\Versioned\Versioned;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Subsites\Model\Subsite;
use SilverStripe\CMS\Controllers\CMSMain;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Subsites\State\SubsiteState;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\CMS\Controllers\CMSPageEditController;

class Block extends DataObject
{
    private static $table_name = 'Blocks_Block';

    private static $singular_name = 'Block';

    private static $plural_name = 'Blocks';

    private static $db = [
        'Title'         => 'Varchar(255)',
        'Template'      => 'Varchar',
        'CSSFile'       => 'Varchar',
        'AnchorName'    => 'Varchar(255)',
        'Heading'       => 'Varchar(255)',
        'Content'       => 'HTMLText',
    ];

    private static $casting = [
        'Icon' => 'HTMLText'
    ];

    private static $summary_fields = [
        'IconForCMS'        => 'Type',
        'Title'             => 'Title',
        'ContentSummary'    => 'Content',
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
        $icon = str_replace('[resources]', TOAST_RESOURCES_DIR , self::config()->get('block-icon'));

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

        return $this->renderWith([$template, 'Toast\Blocks\Default\Block']);
    }

    public function getCMSFields()
    {
        Requirements::css('toastnz/blocks-layouts: client/dist/styles/icons.css');
        Requirements::css('toastnz/blocks-layouts: client/dist/styles/preview.css');
        Requirements::css('toastnz/blocks-layouts: client/dist/styles/page-links.css');
        Requirements::javascript('toastnz/blocks-layouts: client/dist/scripts/icons.js');

        $this->beforeUpdateCMSFields(function ($fields) {
            if ($this->ID) {
                // Generate HTML for the list of links
                $linksHtml = Helper::getBlockPageLinksHTMLForCMS($this);

                $fields->addFieldsToTab('Root.More', [
                    HeaderField::create('UsageHeading', 'Link to this block'),
                    LiteralField::create('BlockLink', 'Block Link <br><a href="' . $this->AbsoluteLink() . '" target="_blank">' . $this->AbsoluteLink() . '</a><hr>'),
                    ReadonlyField::create('Shortcode', 'Shortcode', '[block,id=' . $this->ID . ']'),
                    ReadonlyField::create('BlockID', 'Block ID', $this->getBlockID()),
                ]);

                if ($preview = $this->getPagePreview()) {
                    $fields->addFieldToTab('Root.More', $preview);
                }

                $fields->insertBefore('Title', HeaderField::create('PageLinksHeading', 'Pages using this block'));
                $fields->insertBefore('Title', LiteralField::create('PageLinks', $linksHtml));
                $fields->insertBefore('Title', HeaderField::create('BlockSettingsHeading', 'Block Settings'));
            }

            $fields->removeByName([
                'Template',
                'CSSFile'
            ]);

            // Add fields to the form
            $fields->addFieldsToTab('Root.Main', [
                TextField::create('Title', 'Title')
                    ->setDescription('Title used for internal reference only and does not appear on the site.'),
                TextField::create('AnchorName', 'Anchor Name')
                    ->setDescription('This will be the name that appears in the URL when linking to this block manually. <br> <strong class="warning">Please ensure this heading is unique on the page.</strong>'),
                TextField::create('Heading', 'Heading'),
                HTMLEditorField::create('Content', 'Content')
            ]);

            if ($layoutOptions = $this->getBlockLayouts()){
                // Add the $layoutOptions to the Main tab, AFTER the Title field
                $fields->insertAfter('AnchorName', $layoutOptions);
            }

        });

        return parent::getCMSFields();
    }

    public function getPagePreview($anchor = null)
    {
        return LiteralField::create('Preview', '<div id="BlockPreviewFrame"><iframe src="' . $this->getBlockPreviewURL($anchor) . '"></iframe></div>');
    }

    public function getBlockLayouts()
    {
        // scan the app directory for block layouts and return them as an array
        $layouts = [];
        $optionalLayouts = [];
        $baseFolder = BASE_PATH;
        $theme = Helper::getThemes();
        // module dir
        $module_src = BASE_PATH . '/' . TOAST_BLOCKS_DIR . '/' . TOAST_BLOCKS_TEMPLATE_DIR  . '/' ;
        $module_imgsrc = Helper::getLayoutIconSrc()  ? Helper::getLayoutIconSrc() . TOAST_DEFAULT_DIR : BASE_PATH . '/' . TOAST_BLOCKS_IMAGE_DIR ;
       // get default layouts
        $layouts = Helper::getAvailableBlocksLayouts($this, $module_src, $module_imgsrc, true);

        // alternate layouts if specified
        if ($layout_src = Helper::getLayoutSrc()){
            $layout_src = BASE_PATH . '/' . $layout_src;
            $dirs = array_values(array_diff(scandir('/'.$layout_src), array('.', '..')));
            foreach ($dirs as $dir) {
                $layout_imgsrc = Helper::getLayoutIconSrc();
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

        $tplField = OptionsetField::create(
            "Template",
            "Choose a layout",
            $layouts,
            $this->Template
        )->addExtraClass('toast-block-layouts');

        return $tplField;
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
        // Get the CSS directory from the configuration
        $cssDir = Config::inst()->get('Toast\Blocks\Extensions\PageExtension', 'layout_dist_dir');

        // Get the template name
        $template = $this->Template;

        // If either the CSS directory or the template name is not set, return null
        if (!$cssDir || !$template) {
            return null;
        }

        // Split the template name into parts
        $templateParts = explode('\\', $template);

        // If the template name doesn't have at least 3 parts, return null
        if (!isset($templateParts[2])) {
            return null;
        }

        // Get the layout name from the template parts and convert it to lowercase
        $layoutName = strtolower($templateParts[2]);

        // Get the block template name and convert it to lowercase
        $blockTemplateName = strtolower($this->getBlockTemplateName());

        // Construct the CSS file name
        $cssFileName = $layoutName . '-' . $blockTemplateName . '.css';

        // Construct the full path to the CSS file
        $cssFilePath = BASE_PATH . '/' . $cssDir . '/' . $cssFileName;

        // If the CSS file doesn't exist, return null
        if (!file_exists($cssFilePath)) {
            return null;
        }

        // Construct the relative path to the CSS file
        $cssFilePath = $cssDir . '/' . $cssFileName;

        // Allow other extensions to update the CSS file path
        $this->extend('updateBlockTemplateCSS', $cssFilePath);

        // Return the CSS file path
        return $cssFilePath;
    }

    public function onBeforeWrite()
    {
        if (!$this->Template){
            $this->Template =  $this->getTemplateClass();
        }

        $this->CSSFile = $this->getCSSFile();

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
        // Get the current controller
        $controller = Controller::curr();

        // Initialise the parent variable
        $parent = null;

        $pages = $this->getAllPages();

        // Ensure the controller is an instance of CMSMain
        if ($controller instanceof CMSMain) {
            // Call the currentPage() method on the controller instance
            $parent = $controller->currentPage();
        }

        else {
            $parent = $this->getParentPage();

            if (!$parent || !$parent->exists()) {
                if (count($pages) > 0) $parent = $this->getAllPages()[0];
            }
        }

        if ($parent && $parent->exists()) {
            return $parent->Link($action) . '#' . $this->getBlockID();
        }

        return '';
    }

    public function Link($action = null)
    {
        return $this->getLink($action);
    }

    public function getBlockLink($parent) {
        if ($parent && $parent->exists()) {
            return $parent->Link() . '#' . $this->getBlockID();
        }

        return '';
    }
    /* ?SubsiteID=1&stage=Stage&CMSPreview=1 */
    public function getBlockPreviewURL($anchor = null)
    {
        // Get the current controller
        $controller = Controller::curr();
  	$path = null;
        // Get the base URL
        $baseURL = Director::absoluteBaseURL();

        // // Ensure the controller is an instance of CMSMain
        if ($controller instanceof CMSMain) {
            $path = $controller->currentPage()->Link();
        }

        // Generate the link
        $link = Controller::join_links($baseURL, $path);
        // Add the necessary query string parameters
        $link .= '?stage=Stage&CMSPreview=1';

        if (class_exists(Subsite::class)) {
            // Get the current subsite ID
            $currentSubsiteID = SubsiteState::singleton()->getSubsiteId();
            // Add the subsite ID to the query string
            $link .= '&SubsiteID=' . $currentSubsiteID;
        }

        // Add the block ID as a hash
        $link .= '#' . ($anchor ?: $this->getBlockID());

        return $link;
    }

    public function getAbsoluteLink($action = null)
    {
        // Get the current controller
        $controller = Controller::curr();

        $link = Director::absoluteBaseURL();

        // Ensure the controller is an instance of CMSMain
        if ($controller instanceof CMSMain) {
            // Call the currentPage() method on the controller instance
            $link = $controller->currentPage()->AbsoluteLink();
        }

        return $link . '?stage=Stage#' . $this->owner->getBlockID();
    }

    public function AbsoluteLink($action = null)
    {
        return $this->getAbsoluteLink($action);
    }

    public function getAllPages()
    {
        $pages = array_merge($this->getPagesFromMainSite(), $this->getPagesFromSubsites());

        // make the array unique
        return array_unique($pages, SORT_REGULAR);
    }

    public function getPagesFromSiteTree()
    {
        $pages = SiteTree::get()
            ->leftJoin('Page_ContentBlocks', '"Page_ContentBlocks"."PageID" = "SiteTree"."ID"')
            ->where('"Page_ContentBlocks"."Blocks_BlockID" = ' . $this->ID)
            ->distinct(true, ['"SiteTree"."ID"']);

        return $pages->toArray();
    }

    public function getPagesFromMainSite()
    {
        $pages = $this->getPagesFromSiteTree();

        if (class_exists(Subsite::class)) {
            // Get the current subsite ID
            $currentSubsiteID = SubsiteState::singleton()->getSubsiteId();
            // Change to the main site context
            Subsite::changeSubsite(0);

            $pages = $this->getPagesFromSiteTree();

            // Return to the original subsite context
            Subsite::changeSubsite($currentSubsiteID);
        }

        return $pages;
    }

    public function getPagesFromSubsites()
    {
        $allPages = [];

        if (class_exists(Subsite::class)) {
            // Fetch all subsites
            $subsites = Subsite::get();
            // Get the current subsite ID
            $currentSubsiteID = SubsiteState::singleton()->getSubsiteId();

            // Iterate through each subsite
            foreach ($subsites as $subsite) {
                // Temporarily switch to the subsite context
                Subsite::changeSubsite($subsite->ID);

                // Fetch unique pages related to the block within the current subsite
                $pages = $this->getPagesFromSiteTree();

                // Merge the pages into the allPages array
                $allPages = array_merge($allPages, $pages);
            }

            // Return to the main site context
            Subsite::changeSubsite($currentSubsiteID);

            // Return the unique pages
            return $allPages;
        }

        return [];
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
            $member = Security::getCurrentUser();
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
            $member = Security::getCurrentUser();
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

    public function getBlockID()
    {
        // Set an ID var
        $id = '';

        // First check if there is a NavigationHeading
        if ($this->NavigationHeading) {
            // Remove any number, punctuation, and special characters
            $id = preg_replace('/[^a-zA-Z]+/', ' ', $this->NavigationHeading);
            // Convert to Upper Camel Case (Pascal Case)
            $id = str_replace(' ', '', ucwords(trim($id)));
        }

        // Make sure the ID has at least 1 character, otherwise return the default ID
        return (strlen($id) > 0) ? $id : $this->getHtmlID();
    }

    public function getExtraRequirements()
    {
        $extraRequirements = [];

        $this->extend('updateExtraRequirements', $extraRequirements);

        return $extraRequirements;
    }
}
