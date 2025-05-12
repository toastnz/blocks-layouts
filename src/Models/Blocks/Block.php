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
use SilverStripe\Forms\DropdownField;
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
use SilverStripe\Forms\HiddenField;
use Toast\OpenCMSPreview\Fields\OpenCMSPreview;

class Block extends DataObject
{
    private static $table_name = 'Blocks_Block';

    private static $singular_name = 'Block';

    private static $plural_name = 'Blocks';

    protected static $icon_class = 'font-icon-block-content';

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

    public function getContentSummary()
    {
        if ($this->Content) {
            return DBField::create_field(DBHTMLText::class, $this->Content)->Summary();
        } elseif ($this->Heading) {
            return DBField::create_field(DBHTMLText::class, $this->Heading);
        }
        return null;
    }

    public function getIconForCMS()
    {
        if (self::config()->get('block-icon') == null) {
            return DBField::create_field('HTMLText', '
                <div data-block-id="' . $this->BlockID . '" class="toast-block-icon" style="text-align: center; margin: 0 auto; margin; padding: 10px;">
                    <span class="toast-block-icon__media ' . static::$icon_class . '" style="position: relative; font-size: 40px; line-height: 0;"></span>
                </div>
                <span class="toast-block-title" style="display: block; font-size: 10px; font-weight: bold; line-height: 10px; text-transform: uppercase; text-align: center; margin: 0; padding: 0;">' . $this->i18n_singular_name() . '</span>
            ');
        }

        $icon = str_replace('[resources]', TOAST_RESOURCES_DIR, self::config()->get('block-icon'));

        return DBField::create_field('HTMLText', '
            <div data-block-id="' . $this->BlockID . '" title="' . $this->i18n_singular_name() . '" style="margin: 0 auto;width:50px; height:50px; white-space:nowrap; ">
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
        Requirements::css('toastnz/blocks-layouts: client/dist/styles/page-links.css');
        Requirements::javascript('toastnz/blocks-layouts: client/dist/scripts/icons.js');

        $this->beforeUpdateCMSFields(function ($fields) {
            if ($this->ID) {
                // Generate HTML for the list of links
                $linksHtml = Helper::getBlockPageLinksHTMLForCMS($this);

                $fields->addFieldsToTab('Root.More', [
                    OpenCMSPreview::create($this->getBlockPreviewURL()),
                    HeaderField::create('UsageHeading', 'Link to this block'),
                    LiteralField::create('BlockLink', 'Block Link <br><a href="' . $this->AbsoluteLink() . '" target="_blank">' . $this->AbsoluteLink() . '</a><hr>'),
                    ReadonlyField::create('Shortcode', 'Shortcode', '[block,id=' . $this->ID . ']'),
                    ReadonlyField::create('BlockID', 'Block ID', $this->getBlockID()),
                ]);

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
                    ->setDescription('This will be the name that appears in the URL when linking to this block manually. <br> <strong class="warning">Please ensure this heading is unique on the page.</strong> <br> <strong class="warning">Updating this value will break any existing anchor links pointing to this block!</strong>'),
                TextField::create('Heading', 'Heading'),
                HTMLEditorField::create('Content', 'Content')
            ]);

            if ($layoutOptionsField = $this->getTemplateOptionsField()) {
                $fields->insertAfter('AnchorName', $layoutOptionsField);
            }
        });

        return parent::getCMSFields();
    }

    public function getAvailableLayouts($className = null)
    {
        // This will be a unique list of layouts
        $layouts = [];
        // These will come from the module
        $moduleLayouts = [];
        // These will come from the theme
        $additionalLayouts = [];

        // Get the module's layouts directory
        $moduleLayoutsDir = BASE_PATH . '/' . TOAST_BLOCKS_DIR . '/' . TOAST_BLOCKS_TEMPLATE_DIR;
        // Get the additional layouts directory
        $additionalLayoutsDir = BASE_PATH . '/' . Config::inst()->get('Toast\Blocks\Extensions\PageExtension', 'layout_src');

        // Get the folder name of the module layouts
        $moduleLayoutsName = basename($moduleLayoutsDir);

        // Scan the module's layouts directory to get all the templates
        $moduleTemplates = array_values(array_diff(scandir('/' . $moduleLayoutsDir), array('.', '..')));

        // Loop all the module templates and add them to the $moduleLayouts array
        foreach ($moduleTemplates as $template) {
            $moduleLayouts[$moduleLayoutsName][] = $template;
        }

        // If there are additional layouts, scan the directory to get all the templates
        if (file_exists($additionalLayoutsDir)) {
            // Get an array of all the subdirectories in the additional layouts directory
            $additionalLayoutFolders = array_diff(scandir('/' . $additionalLayoutsDir), array('.', '..'));

            // Loop all the additional layout folders
            foreach ($additionalLayoutFolders as $folder) {
                // Construct the path to the folder
                $path = '/' . $additionalLayoutsDir . DIRECTORY_SEPARATOR . $folder;

                // Check if the path is a directory
                if (is_dir($path)) {
                    // Scan the $folder directory to get all the templates (.ss files)
                    $additionalTemplates = array_values(array_diff(scandir($path), array('.', '..')));

                    // Make sure the $additionalTemplates are all files that have the .ss extension
                    $additionalTemplates = array_filter($additionalTemplates, function ($template) {
                        return pathinfo($template, PATHINFO_EXTENSION) === 'ss';
                    });

                    // Loop all the additional templates and add them to the $additionalLayouts array
                    foreach ($additionalTemplates as $template) {
                        $additionalLayouts[$folder][] = $template;
                    }
                }
            }
        }

        // Now set up the $layouts array using the module layouts, and allowing the additional layouts to override them
        $layouts = array_merge_recursive($moduleLayouts, $additionalLayouts);

        if ($className) {
            $layouts = array_map(function ($layout) use ($className) {
                return array_filter($layout, function ($item) use ($className) {
                    $itemName = pathinfo($item, PATHINFO_FILENAME);
                    return $itemName === $className;
                });
            }, $layouts);
        }

        return $layouts;
    }

    public function getOptionsForLayouts($layouts = [])
    {
        // Initialize the $icons array with the same structure as $layouts
        $icons = [];
        $optionset = [];

        foreach ($layouts as $layout => $templates) {
            foreach ($templates as $template) {
                $icons[$layout][] = (object) [
                    'template' => $template,
                    'icon' => null
                ];
            }
        }

        if ($iconSrcFolder = Config::inst()->get('Toast\Blocks\Extensions\PageExtension', 'layout_icon_src')) {
            // Get the full path to the icon folder
            $iconSrcPath = Director::publicFolder() . '/' . str_replace('[resources]', RESOURCES_DIR, $iconSrcFolder);

            if (file_exists($iconSrcPath)) {
                // Iterate over the layouts to check for icon files
                foreach ($layouts as $layout => $templates) {
                    foreach ($templates as $template) {
                        $iconFolder = strtolower($layout);
                        $iconFile = strtolower(pathinfo($template, PATHINFO_FILENAME));
                        // Construct the path to the icon file
                        $iconPath = $iconSrcPath . '/' . $iconFolder . '/' . $iconFile . '.svg';

                        // Check if the icon file exists
                        if (file_exists($iconPath)) {
                            // Get the icon file contents
                            $icon = file_get_contents($iconPath);

                            // Update the $icons array with the icon content
                            foreach ($icons[$layout] as $iconObj) {
                                if ($iconObj->template === $template) {
                                    $iconObj->icon = $icon;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Generate the $optionset array with HTML
        foreach ($icons as $layout => $templates) {
            foreach ($templates as $iconObj) {
                $template = $iconObj->template;
                $icon = $iconObj->icon;
                $className = 'Toast\Blocks\\' . $layout . '\\' . pathinfo($template)['filename'];

                // Generate the HTML for the icon
                if (!$icon) break;

                $html = '<div class="blockThumbnail">' . $icon . '</div><strong class="title" title="Template file: ' . $template . '">' . $layout . '</strong>';
                $optionset[$className] = DBField::create_field(DBHTMLText::class, $html);
            }
        }

        return $optionset;
    }

    public function getTemplateOptionsField()
    {
        // Create an array of layout options
        $options = [];
        // Get the block's short name
        $shortName = (new ReflectionClass($this))->getShortName();
        // Get the available layouts
        $layouts = $this->getAvailableLayouts($shortName);

        // Get the icons for the layouts
        $icons = $this->getOptionsForLayouts($layouts);

        // Flag to check if all matching layouts have icons
        $allHaveIcons = true;

        // Set the field to null by default
        $field = HiddenField::create('Template', 'Layout', $this->Template);

        // Loop all the layouts
        foreach ($layouts as $folder => $templates) {
            // Loop all the templates
            foreach ($templates as $template) {
                // Get the template name without extension
                $name = pathinfo($template, PATHINFO_FILENAME);

                $className = 'Toast\Blocks\\' . $folder . '\\' . $name;

                // Check if the template name matches the block's class name
                if ($name === $shortName) {
                    // Check if the icon exists for this template
                    if (empty($icons[$className])) {
                        $allHaveIcons = false;
                    }
                    // Add the template to the options array
                    $options[$className] = $folder;
                }
            }
        }

        // Return the hidden field if there are no extra options
        if (count($options) < 2) return $field;

        // Create the field based on whether all matching layouts have icons
        if ($allHaveIcons) {
            // Create an OptionsetField with the layout options and icons
            $field = OptionsetField::create('Template', 'Layout', $icons, $this->Template)
                ->addExtraClass('toast-block-layouts');
        } else {
            // Create a DropdownField with the layout options
            $field = DropdownField::create('Template', 'Layout', $options, $this->Template)
                ->addExtraClass('toast-block-layouts');
        }

        // Return the field
        return $field;
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
        if (!$this->Template) {
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
        if (!$this->Template) {
            $this->Template =  $this->getTemplateClass();
        }
        parent::populateDefaults();
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
        } else {
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

    public function getBlockLink($parent)
    {
        if ($parent && $parent->exists()) {
            return $parent->Link() . '#' . $this->getBlockID();
        }

        return '';
    }

    public function getBlockPreviewURL($anchor = null)
    {
        // Get the base URL
        $baseURL = Director::absoluteBaseURL();

        // remove any hash
        $splitLink = explode('#', $this->getLink());

        $location = $splitLink[0];
        $hash = $splitLink[1] ?? null;

        $link = Controller::join_links($baseURL, $location);

        // Add the necessary query string parameters
        $link .= '?stage=Stage&CMSPreview=1';

        if (class_exists(Subsite::class)) {
            // Get the current subsite ID
            $currentSubsiteID = SubsiteState::singleton()->getSubsiteId();
            // Add the subsite ID to the query string
            $link .= '&SubsiteID=' . $currentSubsiteID;
        }

        if ($anchor) {
            $link .= '#' . $anchor;
        } else if ($hash) {
            $link .= '#' . $hash;
        }

        return $link;
    }

    // public function getBlockPreviewURL($anchor = null)
    // {
    //     // Get the current controller
    //     $controller = Controller::curr();
    //     $path = null;
    //     // Get the base URL
    //     $baseURL = Director::absoluteBaseURL();

    //     // // Ensure the controller is an instance of CMSMain
    //     if ($controller instanceof CMSMain) {
    //         $path = $controller->currentPage()->Link();
    //     }

    //     // Generate the link
    //     $link = Controller::join_links($baseURL, $path);
    //     // Add the necessary query string parameters
    //     $link .= '?stage=Stage&CMSPreview=1';

    //     if (class_exists(Subsite::class)) {
    //         // Get the current subsite ID
    //         $currentSubsiteID = SubsiteState::singleton()->getSubsiteId();
    //         // Add the subsite ID to the query string
    //         $link .= '&SubsiteID=' . $currentSubsiteID;
    //     }

    //     // Add the block ID as a hash
    //     $link .= '#' . ($anchor ?: $this->getBlockID());

    //     return $link;
    // }

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
        if (!$image = Image::get()->byID($imageid)) return;
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

    public function getPage()
    {
        $currentController = Controller::curr();

        if ($currentController->ID) {
            $parent = \Page::get()->leftJoin('Page_ContentBlocks', '"Page_ContentBlocks"."PageID" = "SiteTree"."ID"')
                ->where('"Page_ContentBlocks"."Blocks_BlockID" = ' . $this->owner->ID)
                ->where('"Page_ContentBlocks"."PageID" = ' . $currentController->ID)
                ->first();

            // get the page that has this block
            if ($parent && $parent->exists()) {
                return $parent;
            }
        }

        return;
    }

    public function getBlockID()
    {
        // Set an ID var
        $id = '';

        // First check if there is a AnchorName
        if ($this->AnchorName) {
            // Remove any number, punctuation, and special characters
            $id = preg_replace('/[^a-zA-Z]+/', ' ', $this->AnchorName);
            // Convert to Upper Camel Case (Pascal Case)
            $id = str_replace(' ', '', ucwords(trim($id)));
        }

        // Make sure the ID has at least 1 character, otherwise return the default ID
        return (strlen($id) > 0) ? $id : $this->getHtmlID();
    }

    public function getExtraRequirements()
    {
        $extraRequirements = null;

        $this->extend('updateExtraRequirements', $extraRequirements);

        return $extraRequirements;
    }

    public function getCMSEditLink()
    {
        if ($parent = $this->getCMSParentPage()) {
            $parentID = $parent->ID;
            $parentEditLink = $this->getCMSParentPage()->CMSEditLink();
            // Replace /show/$ID with /EditForm/$ID
            $parentEditFormLink = str_replace("/show/$parentID", "/EditForm/$parentID", $parentEditLink);

            return $parentEditFormLink . '/field/ContentBlocks/item/' . $this->ID . '/edit';
        }
    }

    public function getCMSParentPage()
    {
        // Get the current controller
        $controller = Controller::curr();

        $parent = null;

        if ($controller instanceof CMSMain) {
            // Call the currentPage() method on the controller instance
            $parent = $controller->currentPage();
        }

        if ($parent && $parent->exists()) {
            return $parent;
        }

        return null;
    }

    public function getCMSSiblingBlocks()
    {
        $parent = $this->getCMSParentPage();

        if ($parent && $parent->exists()) {
            return $parent->ContentBlocks()->sort('SortOrder');
        }

        return null;
    }

    public function getCMSSiblingBlocksLinks()
    {
        $blocks = $this->getCMSSiblingBlocks();

        if ($blocks) {
            $links = [];

            foreach ($blocks as $block) {
                $active = $block->ID == $this->ID ? 'active' : '';
                // $icon = $block->IconForCMS ? '<img src="' . $block->IconForCMS . '" alt="Icon" class="cms-icon">' : '';
                $links[] = '<a class="' . $active . '" href="' . $block->getCMSEditLink() . '" data-block-id="' . $this->BlockID . '">' . $block->IconForCMS . '</a>';
            }

            return '<div class="content-block-siblings">' . implode('', $links) . '</div>';
        }

        return null;
    }

    public function getBlockSpecificExtraClasses()
    {
        $classes = [];
        $this->extend('updateBlockSpecificExtraClasses', $classes);
        return $classes;
    }
}
