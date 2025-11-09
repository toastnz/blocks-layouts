<?php

namespace Toast\Blocks;

use SilverStripe\View\SSViewer;
use SilverStripe\Model\ArrayData;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\LiteralField;
use Toast\OpenCMSPreview\Fields\OpenCMSPreview;
use SilverStripe\CMS\Controllers\ModelAsController;

class PageContentBlock extends Block
{
    private static $singular_name = 'Page Content Block';
    private static $plural_name = 'Page Content Blocks';
    private static $description = 'Used to position page template content within a flexible content area';
    private static $table_name = 'Blocks_PageContentBlock';
    protected static $icon_class = 'font-icon-block-virtual-page';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fieldNames = [
            'BlockSettingsHeading',
            'PageLinksHeading',
            'PageLinks',
            'More'
        ];

        foreach ($fields->dataFields() as $field) {
            $fieldNames[] = $field->getName();
        }

        // Remove all fields
        $fields->removeByName($fieldNames);

        // Add a literal field to explain the purpose of this block
        $fields->addFieldsToTab('Root.Main', [
            LiteralField::create('Info', '<p class="message">This block is used to position the main page content within the flexible content area. It does not have its own editable content.</p>'),
            OpenCMSPreview::create($this->getBlockPreviewURL()),
        ]);

        return $fields;
    }

    // Helper to identify this block type
    public function IsPageContentBlock()
    {
        return true;
    }
    // Render the parent page's content within this block
    public function forTemplate(): string
    {
        // return the parent page's layout
        return $this->renderParentLayout();
    }

    public function renderParentLayout(): string
    {
        $parent = $this->getParentPage();

        if (!$parent || !$parent->exists()) {
            return '';
        }
        $controller = ModelAsController::controller_for($parent);

        $layoutTemplates = [];
        $namespace = $parent->getNamespace();
        $shortName = $parent->getShortName();
        // Namespaced template: Toast/Pages/Layout/GeneralHolderPage.ss
        if ($namespace && $shortName) {
            $layoutTemplates[] =  str_replace('\\', '/', $namespace) . '/Layout/' . $shortName;
        }
        // fallback on Layout/PageName
        $layoutTemplates[] = 'Layout/' . $shortName;

        if(empty($layoutTemplates)){
            return '';
        }

        $viewer = SSViewer::create($layoutTemplates);

        return $viewer->process($controller);
    }

    // Override this to prevent "Linked Pages" from showing because the list could be massive
    public function getLinkedPagesList()
    {
        return '';
    }

    // the belows are to prevent deletion and unlinking of this block type
    public function canEdit($member = null)
    {
        return false;
    }

    public function canDelete($member = null)
    {
        return false;
    }
}
