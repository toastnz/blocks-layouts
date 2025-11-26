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
        // get all templates that has $namespace/Layout/$parentShortName
        $allTemplates = SSViewer::get_templates_by_class(get_class($parent));
        // convert template name to Layout template
        foreach ($allTemplates as $template) {
            if (is_string($template)) {
                // Check if template contains $namespace 
                if ($namespace && strpos($template, $namespace) !== false) {
                    // split $template into two parts at $namespace
                    $parts = explode($namespace, $template);
                    // reassemble to form Layout template
                    $layoutTemplates[] = str_replace('\\', '\\', $namespace) . '\Layout' . $parts[1];
                } 
            }
        }
        // var_dump($layoutTemplates);die();
        if(empty($layoutTemplates)){
            return '';
        }
        
        $viewer = SSViewer::create($layoutTemplates);
        if($templateEngine = SSTemplateEngine::create($parent)){
            // Check if any of the layout templates exist
            foreach ($layoutTemplates as $template) {
                if($templateEngine->hasTemplate($template)){
                    $viewer = SSViewer::create($layoutTemplates, $templateEngine);
                }
            }
        }

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
