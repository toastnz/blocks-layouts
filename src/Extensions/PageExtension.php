<?php

namespace Toast\Blocks\Extensions;

use Page;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Versioned\VersionedGridFieldItemRequest;
use SilverStripe\View\Requirements;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Toast\Blocks\Block;
use Toast\Blocks\Helpers\Helper;
use Toast\Blocks\PageContentBlock;

/**
 * Adds content blocks to pages: the `ContentBlocks` relation, its CMS GridField and the front-end
 * stylesheet/preview helpers used in templates.
 *
 * @extends Extension<Page>
 */
class PageExtension extends Extension
{
    private static $many_many = [
        'ContentBlocks' => Block::class
    ];

    private static $many_many_extraFields = [
        'ContentBlocks' => [
            'SortOrder' => 'Int'
        ]
    ];

    /**
     * How many block stylesheets are inlined as critical CSS by $BlockStyles; the rest are combined into
     * one file.
     *
     * @config
     */
    private static $critical_block_stylesheets = 2;

    protected function updateCMSFields(FieldList $fields): void
    {
        $fields->removeByName('ContentBlocks');

        if ($this->getOwner()->exists()) {
            $fields->addFieldToTab('Root.Blocks', $this->getContentBlocksGridField());
        }
    }

    /**
     * Block classes editors can add to this page. Extensions on the page can change the list with the
     * `updateAvailableBlocks` hook.
     *
     * @return string[]
     */
    public function getAvailableBlockClasses(): array
    {
        $classes = Helper::getAvailableBlockClasses();

        $this->getOwner()->extend('updateAvailableBlocks', $classes);

        return $classes;
    }

    /**
     * The sortable "Blocks" GridField. Extensions on the page can change its config with the
     * `updateContentBlocksGridFieldConfig` hook.
     */
    protected function getContentBlocksGridField(): GridField
    {
        $owner = $this->getOwner();

        $config = GridFieldConfig_RelationEditor::create(50)
            ->removeComponentsByType([
                GridFieldAddNewButton::class,
                GridFieldFilterHeader::class,
                GridField_ActionMenu::class,
            ])
            ->addComponent(Helper::createAddNewMultiClass($this->getAvailableBlockClasses()))
            ->addComponent(GridFieldOrderableRows::create('SortOrder'));

        $config->getComponentByType(GridFieldAddExistingAutocompleter::class)
            ?->setSearchFields(['Title:PartialMatch']);

        $config->getComponentByType(GridFieldDetailForm::class)
            ->setItemRequestClass(VersionedGridFieldItemRequest::class)
            ->setItemEditFormCallback(function (Form $form, GridFieldDetailForm_ItemRequest $itemRequest) use ($owner) {
                // New blocks are added to the end of the page
                if (!$itemRequest->getRecord()->exists()) {
                    $nextSortOrder = (int) $owner->ContentBlocks()->max('SortOrder') + 1;
                    $form->Fields()->add(HiddenField::create('ManyMany[SortOrder]', 'Sort Order', $nextSortOrder));
                }
            });

        $owner->extend('updateContentBlocksGridFieldConfig', $config);

        return GridField::create('ContentBlocks', 'Blocks', $owner->ContentBlocks(), $config);
    }

    /**
     * Stylesheets (relative to the project root) used by the layouts of the blocks on this page, in order
     * and without duplicates. Extensions can add blocks with `updateCombinedBlocks` or change the final
     * list with `updateBlockStylesheets`.
     *
     * @return string[]
     */
    public function getBlockStylesheets(): array
    {
        $owner = $this->getOwner();

        $components = ArrayList::create();
        $components->merge($owner->ContentBlocks()->sort('SortOrder'));
        $owner->extend('updateCombinedBlocks', $components);

        $styles = [];

        foreach ($components as $block) {
            // Resolved live (rather than from the stored CSSFile column) so config and hook changes apply
            $cssFile = $block->hasMethod('getCSSFile') ? $block->getCSSFile() : null;

            if ($cssFile && is_file(BASE_PATH . '/' . $cssFile)) {
                $styles[$cssFile] = $cssFile;
            }
        }

        $styles = array_values($styles);

        $owner->extend('updateBlockStylesheets', $styles);

        return $styles;
    }

    /**
     * Template helper ($BlockStyles): inlines the first stylesheets as critical CSS and combines the rest
     * into a single blocks.css file.
     */
    public function getBlockStyles(): void
    {
        $styles = $this->getBlockStylesheets();

        if (!$styles) {
            return;
        }

        $criticalCount = (int) $this->getOwner()->config()->get('critical_block_stylesheets');
        $criticalFiles = array_splice($styles, 0, max(0, $criticalCount));

        $criticalCss = implode("\n", array_map(
            fn (string $cssFile) => (string) file_get_contents(BASE_PATH . '/' . $cssFile),
            $criticalFiles
        ));

        if (trim($criticalCss) !== '') {
            Requirements::customCSS($criticalCss, 'toast-blocks-critical-css');
        }

        if ($styles) {
            Requirements::combine_files('blocks.css', $styles);
        }
    }

    /**
     * Scripts loaded inside the CMS preview iframe. Extensions can add more paths to the list.
     */
    public function getBlockPreviewJavaScriptPaths(): ArrayList
    {
        return ArrayList::create([
            ArrayData::create([
                'Path' => ModuleResourceLoader::resourceURL('toastnz/blocks-layouts: client/dist/scripts/block-preview-receiver.js'),
            ]),
        ]);
    }

    /**
     * Template helper ($BlockPreviewScripts): imports the preview scripts, only when inside the CMS preview.
     */
    public function getBlockPreviewScripts(): DBHTMLText
    {
        $imports = '';

        foreach ($this->getOwner()->getBlockPreviewJavaScriptPaths() as $file) {
            $imports .= 'await import(' . json_encode((string) $file->Path, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) . ');';
        }

        $script = '<script type="module">if (window.self !== window.top) (async function() {' . $imports . '})();</script>';

        return DBField::create_field(DBHTMLText::class, $script);
    }

    /**
     * Template helper ($ColourBlockScript): loads the <colour-block> custom element.
     */
    public function getColourBlockScript(): void
    {
        Requirements::javascript('toastnz/blocks-layouts: client/dist/scripts/colour-block.js', ['defer' => true]);
    }

    /**
     * Removes the PageContentBlock from page types that don't use PageContentBlockPageExtension.
     */
    protected function onAfterWrite(): void
    {
        $owner = $this->getOwner();

        if ($owner->config()->get('pageContentBlockLinked')) {
            return;
        }

        $pageContentBlock = $owner->ContentBlocks()->filter('ClassName', PageContentBlock::class)->first();

        if ($pageContentBlock) {
            $owner->ContentBlocks()->remove($pageContentBlock);
        }
    }
}
