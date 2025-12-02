<?php

namespace Toast\Blocks;

use SilverStripe\View\SSViewer;
use SilverStripe\ORM\DataObject;
use SilverStripe\Blog\Model\Blog;
use SilverStripe\Model\ArrayData;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\LiteralField;
use Toast\OpenCMSPreview\Fields\OpenCMSPreview;
use SilverStripe\TemplateEngine\SSTemplateEngine;
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
        $layoutTemplates = $this->getParentLayoutTemplates($parent);
        
        if (empty($layoutTemplates)) {
            return '';
        }
        
        $viewer = SSViewer::create($layoutTemplates);
        return $viewer->process($controller);
    }

    protected function getParentLayoutTemplates($parent): array
    {
        $templates = [];
        $classTemplates = SSViewer::get_templates_by_class(get_class($parent));
        
        // Normalize and add Layout versions
        foreach ($classTemplates as $templateGroup) {
            $templates = array_merge(
                $templates, 
                $this->normalizeTemplateGroup($templateGroup)
            );
        }
        
        return array_unique($templates);
    }

    protected function normalizeTemplateGroup($templateGroup): array
    {
        if (is_string($templateGroup)) {
            return $this->generateTemplateVariations($templateGroup);
        }
        
        if (is_array($templateGroup)) {
            $variations = [];
            foreach ($templateGroup as $template) {
                $variations = array_merge($variations, $this->generateTemplateVariations($template));
            }
            return $variations;
        }
        
        return [];
    }

    protected function generateTemplateVariations(string $template): array
    {
        $normalized = str_replace('\\', '/', $template);
        $variations = [$normalized];
        
        // Add Layout version
        $parts = explode('/', $normalized);
        $className = array_pop($parts);
        $namespace = implode('/', $parts);
        
        if ($namespace) {
            $variations[] = "{$namespace}/Layout/{$className}";
        } else {
            $variations[] = "Layout/{$className}";
        }
        
        return $variations;
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
