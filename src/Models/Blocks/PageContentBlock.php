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

   /**
     * Render the parent page's Layout template content
     * This allows the page's main content area to be positioned within blocks
     * 
     * @return string Rendered HTML from the parent page's Layout template
     */
    public function renderParentLayout(): string
    {
    $parent = $this->getParentPage();

        if (!$parent || !$parent->exists()) {
            return '';
        }
        
        // Get the controller for the parent page to provide proper context
        $controller = ModelAsController::controller_for($parent);
        
        // Get all possible Layout template paths for this page type
        $layoutTemplates = $this->getParentLayoutTemplates($parent);
        
        if (empty($layoutTemplates)) {
            return '';
        }
        
        // Render the Layout template with the parent page's controller context
        $viewer = SSViewer::create($layoutTemplates);
        return $viewer->process($controller);
    }

    /**
     * Get all possible Layout template paths for the parent page
     * Converts class-based templates to Layout/ClassName format
     * 
     * @param SiteTree $parent The parent page object
     * @return array Array of template paths in priority order
     */
    protected function getParentLayoutTemplates($parent): array
    {
        $templates = [];
        
        // Get all templates for this class and its ancestors (e.g., ProjectsHolderPage, Blog, Page)
        $classTemplates = SSViewer::get_templates_by_class(get_class($parent));
        
        // Normalize each template and add Layout versions
        foreach ($classTemplates as $templateGroup) {
            $templates = array_merge(
                $templates, 
                $this->normalizeTemplateGroup($templateGroup)
            );
        }
        
        // Remove duplicates while preserving order
        return array_unique($templates);
    }

    /**
     * Normalize a template or group of templates
     * Handles both single template strings and arrays of templates
     * 
     * @param string|array $templateGroup Template name(s) to normalize
     * @return array Normalized template variations
     */
    protected function normalizeTemplateGroup($templateGroup): array
    {
        // Single template string
        if (is_string($templateGroup)) {
            return $this->generateTemplateVariations($templateGroup);
        }
        
        // Array of templates (process each)
        if (is_array($templateGroup)) {
            $variations = [];
            foreach ($templateGroup as $template) {
                $variations = array_merge($variations, $this->generateTemplateVariations($template));
            }
            return $variations;
        }
        
        return [];
    }

    /**
     * Generate template path variations for a given template name
     * Converts namespaced class names to template paths and adds Layout versions
     * 
     * Examples:
     * - "SilverStripe\Blog\Model\Blog" → ["SilverStripe/Blog/Model/Blog", "SilverStripe/Blog/Model/Layout/Blog"]
     * - "Toast\Pages\ProjectsHolderPage" → ["Toast/Pages/ProjectsHolderPage", "Toast/Pages/Layout/ProjectsHolderPage"]
     * - "Page" → ["Page", "Layout/Page"]
     * 
     * @param string $template Template name (may include namespace backslashes)
     * @return array Array containing original and Layout template paths
     */
    protected function generateTemplateVariations(string $template): array
    {
        // Convert namespace backslashes to forward slashes for template paths
        $normalized = str_replace('\\', '/', $template);
        $variations = [$normalized];
        
        // Split the path to separate namespace from class name
        $parts = explode('/', $normalized);
        $className = array_pop($parts); // Get the class name (last part)
        $namespace = implode('/', $parts); // Rejoin the namespace parts
        
        // Add the Layout version of the template
        if ($namespace) {
            // Namespaced: Namespace/Layout/ClassName
            $variations[] = "{$namespace}/Layout/{$className}";
        } else {
            // Non-namespaced: Layout/ClassName
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
