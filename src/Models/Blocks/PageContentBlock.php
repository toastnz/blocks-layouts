<?php

namespace Toast\Blocks;

use SilverStripe\CMS\Controllers\ModelAsController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\View\SSViewer;

/**
 * Placeholder block that marks where a page's own Layout template renders among its blocks.
 * See {@link \Toast\Blocks\Extensions\PageContentBlockPageExtension}.
 */
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

        // This block has no editable content of its own, so strip everything back to an explanation
        $fieldNames = array_merge(
            ['BlockSettingsHeading', 'PageLinksHeading', 'PageLinks', 'More'],
            array_map(fn ($field) => $field->getName(), $this->getDataFieldsFrom($fields))
        );

        $fields->removeByName($fieldNames);

        $fields->addFieldToTab(
            'Root.Main',
            LiteralField::create('Info', '<p class="message">This block is used to position the main page content within the flexible content area. It does not have its own editable content.</p>')
        );

        return $fields;
    }

    /**
     * FieldList::getDataFields() replaces dataFields() from Silverstripe 6.2; support both.
     */
    private function getDataFieldsFrom(FieldList $fields): array
    {
        return method_exists($fields, 'getDataFields') ? $fields->getDataFields() : $fields->dataFields();
    }

    /**
     * Helper to identify this block type in templates.
     *
     * @return bool
     */
    public function IsPageContentBlock()
    {
        return true;
    }

    /**
     * Render the parent page's content within this block
     */
    public function forTemplate(): string
    {
        return $this->renderParentLayout();
    }

    /**
     * Render the parent page's Layout template with its controller, so the page's main content area can be
     * positioned among its blocks.
     */
    public function renderParentLayout(): string
    {
        $parent = $this->getParentPage();

        if (!$parent || !$parent->exists()) {
            return '';
        }

        $layoutTemplates = $this->getParentLayoutTemplates($parent);

        if (empty($layoutTemplates)) {
            return '';
        }

        return (string) SSViewer::create($layoutTemplates)->process(ModelAsController::controller_for($parent));
    }

    /**
     * Candidate templates for the parent page in priority order, e.g. for Toast\Pages\ProjectsPage:
     * ["Toast/Pages/ProjectsPage", "Toast/Pages/Layout/ProjectsPage", "Page", "Layout/Page", ...]
     *
     * @param SiteTree $parent
     */
    protected function getParentLayoutTemplates($parent): array
    {
        $templates = [];

        foreach (SSViewer::get_templates_by_class(get_class($parent)) as $templateGroup) {
            array_push($templates, ...$this->normalizeTemplateGroup($templateGroup));
        }

        return array_values(array_unique($templates));
    }

    /**
     * Template variations for one entry from SSViewer::get_templates_by_class(). Typed entries such as
     * ['type' => 'Includes', 'Page'] are skipped as they never point at a layout.
     *
     * @param string|array $templateGroup
     */
    protected function normalizeTemplateGroup($templateGroup): array
    {
        if (is_string($templateGroup)) {
            return $this->generateTemplateVariations($templateGroup);
        }

        if (!is_array($templateGroup) || isset($templateGroup['type'])) {
            return [];
        }

        $variations = [];

        foreach ($templateGroup as $template) {
            array_push($variations, ...$this->normalizeTemplateGroup($template));
        }

        return $variations;
    }

    /**
     * The template path for a (namespaced) class name, plus its Layout version.
     *
     * - "SilverStripe\Blog\Model\Blog" → ["SilverStripe/Blog/Model/Blog", "SilverStripe/Blog/Model/Layout/Blog"]
     * - "Page" → ["Page", "Layout/Page"]
     */
    protected function generateTemplateVariations(string $template): array
    {
        $normalized = str_replace('\\', '/', $template);
        $separator = strrpos($normalized, '/');

        $layout = $separator === false
            ? 'Layout/' . $normalized
            : substr($normalized, 0, $separator) . '/Layout' . substr($normalized, $separator);

        return [$normalized, $layout];
    }

    /**
     * Hidden for this block type because the list of pages could be massive.
     */
    public function getLinkedPagesList(): string
    {
        return '';
    }

    // Editors can't edit or delete this block; it is managed automatically
    public function canEdit($member = null): bool
    {
        return false;
    }

    public function canDelete($member = null): bool
    {
        return false;
    }
}
