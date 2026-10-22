<?php

namespace Toast\Blocks;

use Page;
use SilverStripe\Admin\CMSEditLinkExtension;
use SilverStripe\Admin\Forms\UsedOnTable;
use SilverStripe\CMS\Controllers\CMSMain;
use SilverStripe\CMS\Controllers\CMSPageEditController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\HTMLReadonlyField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\Validation\CompositeValidator;
use SilverStripe\Forms\Validation\RequiredFieldsValidator;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\CMSPreviewable;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use SilverStripe\Subsites\Model\Subsite;
use SilverStripe\Subsites\State\SubsiteState;
use SilverStripe\Versioned\Versioned;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Toast\Blocks\Admin\BlocksAdmin;
use Toast\Blocks\Extensions\PageExtension;
use Toast\Blocks\Helpers\Helper;

/**
 * Base class for all content blocks.
 *
 * Blocks are versioned records linked to pages through the `ContentBlocks` many_many relation added by
 * {@link PageExtension}. Each block renders with a "layout" template, discovered from the module and from
 * the project's `layout_src` directory, e.g. `Toast\Blocks\Default\TextBlock` or `Toast\Blocks\Split\TextBlock`.
 *
 * Per-layout configuration, keyed by short layout name. Supports: label (string), layout_icon_path (path,
 * supports [resources] token), layout_icon_class (string: CSS class), disabled (bool).
 *
 *   Toast\Blocks\TextBlock:
 *     layout_config:
 *       Default:
 *         label: 'Default'
 *         layout_icon_path: '[resources]/path/to/your/icon.svg'
 *       Split:
 *         label: 'Split'
 *         layout_icon_class: 'layout-icons-text-columns'
 *         disabled: true
 *     exclude_layouts:
 *       - Other
 *
 * Note: once `layout_config` has entries, only the layouts it names are offered in the CMS.
 * Full class-name keys are still supported for backward compatibility.
 * To see available layout_icon_class options refer to client/fonts/layout-icons/font/preview.html
 */
class Block extends DataObject implements CMSPreviewable
{
    private static $table_name = 'Blocks_Block';

    private static $singular_name = 'Block';

    private static $plural_name = 'Blocks';

    protected static $icon_class = 'font-icon-block-content';

    private static $common_block_classes = [];

    private static $first_block_classes = [
        'first',
        'in-view'
    ];

    private static $last_block_classes = [
        'js-in-view',
        'last'
    ];

    private static $other_block_classes = [
        'js-in-view'
    ];

    private static $layout_config = [];

    private static $exclude_layouts = [];

    /**
     * Fields that must be filled in before the block can be saved in the CMS.
     *
     * @config
     */
    private static $required_fields = [];

    /**
     * Show the "Draft" / "Published" switches in the CMS preview panel.
     *
     * @config
     */
    private static $show_stage_link = true;

    private static $show_live_link = true;

    protected static $available_layouts_cache = [];

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
        'BlockLayoutName'   => 'Layout',
        'LinkedPagesList'   => 'Linked Pages',
    ];

    private static $searchable_fields = [
        'Title',
        'Template'
    ];

    private static $extensions = [
        Versioned::class,
        CMSEditLinkExtension::class,
    ];

    /**
     * Where blocks are edited when they aren't being edited through a page.
     *
     * @see CMSEditLinkExtension
     */
    private static string $cms_edit_owner = BlocksAdmin::class;

    /**
     * Show Silverstripe's built-in "Used on" table instead of the module's own list of pages using the block.
     *
     * @config
     */
    private static $use_native_used_on_table = false;

    private static $versioned_gridfield_extensions = true;

    /* ------------------------------------------------------------------
     * Rendering and CMS display
     * ------------------------------------------------------------------ */

    /**
     * Short summary of the block content for CMS lists.
     *
     * @return string|DBField|null
     */
    public function getContentSummary()
    {
        $summary = null;

        if ($this->Content) {
            $summary = DBField::create_field(DBHTMLText::class, $this->Content)->Summary();
        } elseif ($this->Heading) {
            $summary = DBField::create_field('Varchar', $this->Heading);
        }

        $this->extend('updateContentSummary', $summary);

        return $summary;
    }

    public function getIconForCMS(): ?DBField
    {
        $iconPath = $this->config()->get('block-icon');

        $html = (string) Helper::renderTemplate('BlockIcon', [
            'BlockID' => $this->getBlockID(),
            'Name' => $this->i18n_singular_name(),
            'IconURL' => $iconPath ? Helper::resolveResourceURL($iconPath) : null,
            'IconClass' => static::$icon_class,
        ]);

        $this->extend('updateIconForCMS', $html);

        return DBField::create_field(DBHTMLText::class, $html);
    }

    public function IconForCMS(): ?DBField
    {
        return $this->getIconForCMS();
    }

    public function forTemplate(): string
    {
        $template = $this->resolveTemplateWithFallback($this->Template);

        $this->extend('updateBlockTemplate', $template);

        return $this->renderWith([$template, $this->getDefaultTemplateClass(), 'Toast\Blocks\Default\Block']);
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName(['Template', 'CSSFile']);

            $fields->addFieldsToTab('Root.Main', [
                TextField::create('Title', 'Title')
                    ->setDescription('Title used for internal reference only and does not appear on the site.'),
                TextField::create('AnchorName', 'Anchor Name')
                    ->setDescription('This will be the name that appears in the URL when linking to this block manually. <br> <strong class="warning">Please ensure this heading is unique on the page.</strong> <br> <strong class="warning">Updating this value will break any existing anchor links pointing to this block!</strong>'),
                TextField::create('Heading', 'Heading')
                    ->setDescription('&lt;h2&gt;'),
                HTMLEditorField::create('Content', 'Content')
            ]);

            $fields->insertAfter('AnchorName', $this->getTemplateOptionsField());

            if ($notice = $this->getTemplateMissingNoticeField()) {
                $fields->insertAfter('AnchorName', $notice);
            }

            // The remaining fields only make sense once the block has been saved
            if (!$this->exists()) {
                return;
            }

            $absoluteLink = $this->AbsoluteLink();

            $fields->addFieldsToTab('Root.More', [
                HeaderField::create('UsageHeading', 'Link to this block'),
                HTMLReadonlyField::create('BlockLinkURL', 'Block Link', Helper::renderTemplate('BlockLink', ['URL' => $absoluteLink])),
                ReadonlyField::create('Shortcode', 'Shortcode', '[block,id=' . $this->ID . ']'),
                ReadonlyField::create('BlockID', 'Block ID', $this->getBlockID()),
            ]);

            $fields->insertBefore('Title', HeaderField::create('PageLinksHeading', 'Pages using this block'));
            $fields->insertBefore('Title', $this->getPageLinksField());
            $fields->insertBefore('Title', HeaderField::create('BlockSettingsHeading', 'Block Settings'));
        });

        return parent::getCMSFields();
    }

    /**
     * The "Pages using this block" field: the module's own list (grouped by subsite), or Silverstripe's
     * built-in "Used on" table when `use_native_used_on_table` is set.
     */
    protected function getPageLinksField(): FormField
    {
        if ($this->config()->get('use_native_used_on_table')) {
            return UsedOnTable::create('PageLinks');
        }

        return LiteralField::create('PageLinks', Helper::getBlockPageLinksHTMLForCMS($this));
    }

    /* ------------------------------------------------------------------
     * CMS helpers for subclasses
     * ------------------------------------------------------------------ */

    /**
     * Sortable GridField for a has_many relation of block items. Extensions can adjust the config with the
     * `updateItemsGridFieldConfig` hook.
     */
    protected function getItemsGridField(string $relationName, string $title, ?int $itemsPerPage = 50): GridField
    {
        $config = GridFieldConfig_RecordEditor::create($itemsPerPage)
            ->addComponent(GridFieldOrderableRows::create('SortOrder'));

        $this->extend('updateItemsGridFieldConfig', $config, $relationName);

        return GridField::create($relationName, $title, $this->getComponents($relationName), $config);
    }

    /**
     * Add the items GridField for a relation, or a "save first" notice while the block is unsaved.
     */
    protected function addItemsGridField(
        FieldList $fields,
        string $relationName,
        string $title,
        ?int $itemsPerPage = 50,
        string $tab = 'Root.Main'
    ): void {
        $fields->removeByName($relationName);

        $fields->addFieldToTab(
            $tab,
            $this->exists() ? $this->getItemsGridField($relationName, $title, $itemsPerPage) : $this->getSaveFirstNoticeField()
        );
    }

    protected function getSaveFirstNoticeField(): LiteralField
    {
        return Helper::createCMSNoticeField(
            'SaveFirstNotice',
            'Save this block and more options will become available.'
        );
    }

    /* ------------------------------------------------------------------
     * Validation
     * ------------------------------------------------------------------ */

    public function getCMSCompositeValidator(): CompositeValidator
    {
        $validator = parent::getCMSCompositeValidator();
        $requiredFields = $this->getCMSRequiredFields();

        if ($requiredFields) {
            $validator->addValidator(RequiredFieldsValidator::create($requiredFields));
        }

        return $validator;
    }

    /**
     * Names of the fields that must be filled in, from the `required_fields` config. Extensions can change
     * the list with the `updateCMSRequiredFields` hook.
     *
     * @return string[]
     */
    public function getCMSRequiredFields(): array
    {
        $fields = (array) ($this->config()->get('required_fields') ?: []);

        $this->extend('updateCMSRequiredFields', $fields);

        return array_values(array_unique($fields));
    }

    /**
     * @deprecated getCMSValidator() is no longer called by Silverstripe 6. Use the `required_fields` config
     * or getCMSCompositeValidator() instead.
     * @return RequiredFieldsValidator
     */
    public function getCMSValidator()
    {
        $required = RequiredFieldsValidator::create($this->getCMSRequiredFields());

        $this->extend('updateCMSValidator', $required);

        return $required;
    }

    /* ------------------------------------------------------------------
     * Layout configuration
     * ------------------------------------------------------------------ */

    protected function getLayoutConfig(): array
    {
        $layoutConfig = (array) ($this->config()->get('layout_config') ?: []);

        // Be forgiving if exclude_layouts was mistakenly nested under layout_config.
        unset($layoutConfig['exclude_layouts']);

        return $layoutConfig;
    }

    protected function getExcludedLayouts(): array
    {
        $excludeLayouts = (array) ($this->config()->get('exclude_layouts') ?: []);
        $nested = ((array) ($this->config()->get('layout_config') ?: []))['exclude_layouts'] ?? null;

        if (is_array($nested)) {
            $excludeLayouts = array_merge($excludeLayouts, $nested);
        }

        return array_values(array_unique(array_map('strval', $excludeLayouts)));
    }

    protected function getLayoutClassName(string $layout, string $templateName): string
    {
        return 'Toast\\Blocks\\' . $layout . '\\' . $templateName;
    }

    protected function getLayoutConfigEntry(string $layout, string $templateName): array
    {
        $layoutConfig = $this->getLayoutConfig();

        foreach ([$layout, $this->getLayoutClassName($layout, $templateName)] as $key) {
            if (!empty($layoutConfig[$key]) && is_array($layoutConfig[$key])) {
                return $layoutConfig[$key];
            }
        }

        return [];
    }

    protected function isLayoutDisabled(string $layout, string $templateName): bool
    {
        $config = $this->getLayoutConfigEntry($layout, $templateName);

        if (array_key_exists('disabled', $config)) {
            return (bool) $config['disabled'];
        }

        return in_array($layout, $this->getExcludedLayouts(), true);
    }

    protected function getLayoutDiscoveryCacheKey(): string
    {
        return md5(json_encode([
            BASE_PATH,
            $this->getModuleTemplateDirectory(),
            Config::inst()->get(PageExtension::class, 'layout_src'),
        ]));
    }

    protected function replaceResourcesToken(?string $path, string $replacement): ?string
    {
        return $path ? str_replace('[resources]', $replacement, $path) : null;
    }

    /**
     * Filesystem path of a configured resource: module resource syntax ("vendor/module: path") or a path
     * starting with the legacy "[resources]" token.
     */
    protected function resolvePublicResourcePath(?string $path): ?string
    {
        if ($path && Helper::isModuleResource($path)) {
            $relativePath = ModuleResourceLoader::resourcePath($path);

            return $relativePath ? BASE_PATH . '/' . $relativePath : null;
        }

        $resolvedPath = $this->replaceResourcesToken($path, RESOURCES_DIR);

        return $resolvedPath ? Director::publicFolder() . '/' . $resolvedPath : null;
    }

    /* ------------------------------------------------------------------
     * Template names
     * ------------------------------------------------------------------ */

    protected function getTemplateFileName(string $template): string
    {
        return pathinfo($template, PATHINFO_FILENAME);
    }

    protected function getTemplateParts(?string $template): array
    {
        return $template ? explode('\\', $template) : [];
    }

    /**
     * The layout segment of a template class, e.g. "Split" for "Toast\Blocks\Split\TextBlock".
     */
    protected function getTemplateLayoutName(?string $template): ?string
    {
        $layout = $this->getTemplateParts($template)[2] ?? '';

        return $layout !== '' ? $layout : null;
    }

    protected function getDefaultTemplateClass(): string
    {
        return $this->getLayoutClassName('Default', $this->getBlockTemplateName());
    }

    protected function isTemplateMissing(): bool
    {
        if (!$this->Template) {
            return false;
        }

        $availableTemplates = $this->getAvailableTemplateClasses($this->getBlockTemplateName());

        return !empty($availableTemplates) && !in_array($this->Template, $availableTemplates, true);
    }

    protected function getTemplateMissingNoticeField(): ?LiteralField
    {
        if (!$this->isTemplateMissing()) {
            return null;
        }

        $layoutName = $this->getTemplateLayoutName($this->Template) ?: $this->Template;
        $defaultLayoutName = $this->getTemplateLayoutName($this->getDefaultTemplateClass()) ?: 'Default';

        return Helper::createCMSNoticeField(
            'TemplateMissingNotice',
            sprintf(
                "The previously selected layout '%s' is no longer available. This block is currently rendering with the %s layout. Please choose a new layout below and save.",
                $layoutName,
                $defaultLayoutName
            ),
            'warning',
            'Layout not available:'
        );
    }

    protected function getAvailableTemplateClasses(string $templateName): array
    {
        $classes = [];

        foreach ($this->getAvailableLayouts($templateName) as $layout => $templates) {
            foreach ($templates as $template) {
                $classes[] = $this->getLayoutClassName($layout, $this->getTemplateFileName($template));
            }
        }

        return array_values(array_unique($classes));
    }

    /**
     * The given template if it is still available, otherwise the Default layout, otherwise the first available.
     */
    protected function resolveTemplateWithFallback(?string $template): string
    {
        $defaultTemplate = $this->getDefaultTemplateClass();
        $availableTemplates = $this->getAvailableTemplateClasses($this->getBlockTemplateName());

        if (empty($availableTemplates)) {
            return $defaultTemplate;
        }

        if ($template && in_array($template, $availableTemplates, true)) {
            return $template;
        }

        if (in_array($defaultTemplate, $availableTemplates, true)) {
            return $defaultTemplate;
        }

        return $availableTemplates[0];
    }

    /* ------------------------------------------------------------------
     * Layout CSS
     * ------------------------------------------------------------------ */

    protected function getCSSDirectory(): ?string
    {
        return Config::inst()->get(PageExtension::class, 'layout_dist_dir') ?: null;
    }

    protected function getCSSFileName(?string $template = null): ?string
    {
        $layoutName = $this->getTemplateLayoutName($template ?: $this->Template);

        if (!$layoutName) {
            return null;
        }

        $cssFileName = strtolower($layoutName) . '-' . strtolower($this->getBlockTemplateName()) . '.css';

        $this->extend('updateBlockTemplateCSS', $cssFileName);

        return $cssFileName;
    }

    protected function getCSSFileAbsolutePath(?string $template = null): ?string
    {
        $relativePath = $this->getCSSFileRelativePath($template);

        return $relativePath ? BASE_PATH . '/' . $relativePath : null;
    }

    protected function getCSSFileRelativePath(?string $template = null): ?string
    {
        $cssDir = $this->getCSSDirectory();
        $cssFileName = $cssDir ? $this->getCSSFileName($template) : null;

        return $cssFileName ? $cssDir . '/' . $cssFileName : null;
    }

    /**
     * Path (relative to the project root) of the stylesheet for the current layout, if the file exists.
     */
    public function getCSSFile(): ?string
    {
        $relativePath = $this->getCSSFileRelativePath();

        return $relativePath && is_file(BASE_PATH . '/' . $relativePath) ? $relativePath : null;
    }

    /* ------------------------------------------------------------------
     * Layout discovery
     * ------------------------------------------------------------------ */

    protected function getModuleTemplateDirectory(): string
    {
        return Helper::getModulePath() . '/' . TOAST_BLOCKS_TEMPLATE_DIR;
    }

    /**
     * All layouts from the module and the project, as [layout name => [template file names]].
     */
    protected function discoverLayouts(): array
    {
        $cacheKey = $this->getLayoutDiscoveryCacheKey();

        if (isset(static::$available_layouts_cache[$cacheKey])) {
            return static::$available_layouts_cache[$cacheKey];
        }

        // Keep layouts additive across module + project sources.
        // Using merge here avoids replacing indexed template arrays for the same folder.
        $layouts = array_merge_recursive(
            $this->discoverModuleLayouts(),
            $this->discoverAdditionalLayouts()
        );

        $layouts = array_map(fn (array $templates) => array_values(array_unique($templates)), $layouts);

        return static::$available_layouts_cache[$cacheKey] = $this->removeEmptyLayouts($layouts);
    }

    protected function discoverModuleLayouts(): array
    {
        $directory = $this->getModuleTemplateDirectory();

        return [
            basename($directory) => $this->scanTemplateDirectory($directory),
        ];
    }

    protected function discoverAdditionalLayouts(): array
    {
        $layoutSource = Config::inst()->get(PageExtension::class, 'layout_src');

        if (!$layoutSource) {
            return [];
        }

        $layouts = [];

        foreach (glob(BASE_PATH . '/' . $layoutSource . '/*', GLOB_ONLYDIR) ?: [] as $folderPath) {
            $layouts[basename($folderPath)] = $this->scanTemplateDirectory($folderPath);
        }

        return $layouts;
    }

    protected function scanTemplateDirectory(string $directory): array
    {
        if (!is_dir($directory)) {
            return [];
        }

        return array_map('basename', glob($directory . '/*.ss') ?: []);
    }

    /* ------------------------------------------------------------------
     * Layout filtering
     * ------------------------------------------------------------------ */

    /**
     * Keep only the templates the callback accepts, then drop layouts left empty.
     *
     * @param callable(string $layout, string $templateName): bool $keep
     */
    protected function filterLayoutTemplates(array $layouts, callable $keep): array
    {
        foreach ($layouts as $layout => $templates) {
            $layouts[$layout] = array_values(array_filter(
                $templates,
                fn (string $template) => $keep((string) $layout, $this->getTemplateFileName($template))
            ));
        }

        return $this->removeEmptyLayouts($layouts);
    }

    protected function removeEmptyLayouts(array $layouts): array
    {
        return array_filter($layouts, fn (array $templates) => !empty($templates));
    }

    protected function filterLayoutsByClassName(array $layouts, string $className): array
    {
        return $this->filterLayoutTemplates(
            $layouts,
            fn (string $layout, string $templateName) => $templateName === $className
        );
    }

    /**
     * When layout_config has entries, only the layouts it names are offered.
     */
    protected function filterToConfiguredLayouts(array $layouts): array
    {
        $configKeys = array_keys($this->getLayoutConfig());

        if (empty($configKeys)) {
            return $layouts;
        }

        return array_filter($layouts, function ($layout) use ($configKeys) {
            // Match by short layout name key (preferred)
            if (in_array($layout, $configKeys, true)) {
                return true;
            }

            // Match by full class-name key prefix (backward compatibility)
            $prefix = 'Toast\\Blocks\\' . $layout . '\\';

            foreach ($configKeys as $key) {
                if (str_starts_with((string) $key, $prefix)) {
                    return true;
                }
            }

            return false;
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function filterDisabledLayouts(array $layouts): array
    {
        if (!$this->getLayoutConfig() && !$this->getExcludedLayouts()) {
            return $layouts;
        }

        return $this->filterLayoutTemplates(
            $layouts,
            fn (string $layout, string $templateName) => !$this->isLayoutDisabled($layout, $templateName)
        );
    }

    /* ------------------------------------------------------------------
     * Layout icons and picker
     * ------------------------------------------------------------------ */

    protected function getLayoutIconSourcePath(): ?string
    {
        $path = $this->resolvePublicResourcePath(Config::inst()->get(PageExtension::class, 'layout_icon_src'));

        return $path && is_dir($path) ? $path : null;
    }

    /**
     * Contents of an icon file, or null if it doesn't exist or can't be read.
     */
    protected function readIconFile(?string $path): ?string
    {
        if (!$path || !is_file($path)) {
            return null;
        }

        $icon = file_get_contents($path);

        return $icon === false ? null : $icon;
    }

    protected function getDiscoveredLayoutIcon(string $layout, string $templateName, ?string $iconSourcePath): ?string
    {
        if (!$iconSourcePath) {
            return null;
        }

        return $this->readIconFile($iconSourcePath . '/' . strtolower($layout) . '/' . strtolower($templateName) . '.svg');
    }

    protected function getConfiguredLayoutIcon(string $layout, string $templateName): ?string
    {
        $config = $this->getLayoutConfigEntry($layout, $templateName);

        return $this->readIconFile($this->resolvePublicResourcePath($config['layout_icon_path'] ?? null));
    }

    protected function getLayoutOptionLabel(string $layout, string $templateName): string
    {
        $label = $this->getLayoutConfigEntry($layout, $templateName)['label'] ?? '';

        return $label ? (string) $label : $layout;
    }

    protected function getLayoutIconClass(string $layout, string $templateName): ?string
    {
        $iconClass = $this->getLayoutConfigEntry($layout, $templateName)['layout_icon_class'] ?? null;

        return $iconClass ? (string) $iconClass : null;
    }

    /**
     * Icon + label markup for one layout option.
     *
     * Icon priority: 1) explicit SVG path (layout_icon_path), 2) icon class (layout_icon_class) — both from
     * YAML config and both override directory discovery. 3) auto-discovered SVG from layout_icon_src directory.
     */
    protected function buildLayoutOption(string $layout, string $template, ?string $iconSourcePath): ?DBField
    {
        $templateName = $this->getTemplateFileName($template);
        $iconClass = $this->getLayoutIconClass($layout, $templateName);

        $icon = $this->getConfiguredLayoutIcon($layout, $templateName)
            ?? ($iconClass ? sprintf('<span class="layout-icons %s"></span>', Convert::raw2att($iconClass)) : null)
            ?? $this->getDiscoveredLayoutIcon($layout, $templateName, $iconSourcePath);

        if (!$icon) {
            return null;
        }

        return DBField::create_field(DBHTMLText::class, Helper::renderTemplate('BlockLayoutOption', [
            'Icon' => DBField::create_field(DBHTMLText::class, $icon),
            'TemplateFile' => $template,
            'Label' => $this->getLayoutOptionLabel($layout, $templateName),
        ])->forTemplate());
    }

    protected function getTemplateFieldOptions(array $layouts, string $shortName, array $icons): array
    {
        $options = [];
        $allHaveIcons = true;

        foreach ($layouts as $layout => $templates) {
            foreach ($templates as $template) {
                $templateName = $this->getTemplateFileName($template);

                if ($templateName !== $shortName) {
                    continue;
                }

                $className = $this->getLayoutClassName($layout, $templateName);
                $allHaveIcons = $allHaveIcons && !empty($icons[$className]);
                $options[$className] = $this->getLayoutOptionLabel($layout, $templateName);
            }
        }

        return [$options, $allHaveIcons];
    }

    protected function createTemplateOptionsField(array $options, array $icons, bool $allHaveIcons): FormField
    {
        $field = $allHaveIcons
            ? OptionsetField::create('Template', 'Layout', $icons, $this->Template)
            : DropdownField::create('Template', 'Layout', $options, $this->Template);

        return $field->addExtraClass('toast-block-layouts');
    }

    /**
     * Layouts available to this block, optionally limited to one template name. Extensions can adjust the
     * result with the `updateAvailableLayouts` hook.
     *
     * @return array<string, string[]> [layout name => [template file names]]
     */
    public function getAvailableLayouts(?string $className = null): array
    {
        $layouts = $this->discoverLayouts();

        if ($className) {
            $layouts = $this->filterLayoutsByClassName($layouts, $className);
        }

        $layouts = $this->filterDisabledLayouts($this->filterToConfiguredLayouts($layouts));

        $this->extend('updateAvailableLayouts', $layouts, $className);

        return $layouts;
    }

    /**
     * @return array<string, DBField> [template class => icon + label markup]
     */
    public function getOptionsForLayouts(array $layouts = []): array
    {
        $optionset = [];
        $iconSourcePath = $this->getLayoutIconSourcePath();

        foreach ($layouts as $layout => $templates) {
            foreach ($templates as $template) {
                if ($option = $this->buildLayoutOption($layout, $template, $iconSourcePath)) {
                    $optionset[$this->getLayoutClassName($layout, $this->getTemplateFileName($template))] = $option;
                }
            }
        }

        return $optionset;
    }

    /**
     * The layout picker: an icon optionset, a dropdown when some layouts have no icon, or a hidden field
     * when there is nothing to choose between.
     */
    public function getTemplateOptionsField(): FormField
    {
        $shortName = $this->getBlockTemplateName();
        $layouts = $this->getAvailableLayouts($shortName);
        $icons = $this->getOptionsForLayouts($layouts);
        [$options, $allHaveIcons] = $this->getTemplateFieldOptions($layouts, $shortName, $icons);

        $field = count($options) < 2
            ? HiddenField::create('Template', 'Layout', $this->Template)
            : $this->createTemplateOptionsField($options, $icons, $allHaveIcons);

        $this->extend('updateTemplateOptionsField', $field);

        return $field;
    }

    /* ------------------------------------------------------------------
     * Write lifecycle and defaults
     * ------------------------------------------------------------------ */

    public function onBeforeWrite(): void
    {
        $this->Template = $this->resolveTemplateWithFallback($this->Template);

        $this->CSSFile = $this->getCSSFile();

        parent::onBeforeWrite();
    }

    public function getTemplateClass(): string
    {
        return $this->getDefaultTemplateClass();
    }

    public function populateDefaults(): void
    {
        $this->Template = $this->resolveTemplateWithFallback($this->Template);
        parent::populateDefaults();
    }

    public function getTitle(): ?string
    {
        // If the block exists, return the Title field.
        if ($this->exists()) {
            return $this->getField('Title');
        }

        // Otherwise, return the singular name of the block as a default title for new blocks.
        return $this->getField('Title') ?: $this->i18n_singular_name() ?: 'Untitled Block';
    }

    public function getApiURL(): string
    {
        return Controller::join_links(Controller::curr()?->AbsoluteLink(), 'Block', $this->ID);
    }

    /* ------------------------------------------------------------------
     * Links and preview
     * ------------------------------------------------------------------ */

    protected function getCurrentCMSRecord(): ?DataObject
    {
        $controller = Controller::curr();

        return $controller instanceof CMSMain ? $controller->currentRecord() : null;
    }

    /**
     * The page to link through: the page being edited in the CMS, the page being viewed, or the first
     * page that uses this block.
     */
    protected function getLinkParentPage(): ?SiteTree
    {
        $record = $this->getCurrentCMSRecord();

        if ($record instanceof SiteTree) {
            return $record;
        }

        return $this->getParentPage() ?? ($this->getAllPages()[0] ?? null);
    }

    protected function buildBlockLinkFromParent($parent, $action = null): string
    {
        if (!$parent || !$parent->exists()) {
            return '';
        }

        return $parent->Link($action) . '#' . $this->getBlockID();
    }

    protected function splitLinkHash(string $link): array
    {
        $parts = explode('#', $link, 2);

        return [
            'location' => $parts[0],
            'hash' => $parts[1] ?? null,
        ];
    }

    protected function appendQueryParams(string $url, array $params): string
    {
        return $params ? Controller::join_links($url, '?' . http_build_query($params)) : $url;
    }

    protected function appendSubsitePreviewParam(string $url): string
    {
        if (!Helper::hasSubsites()) {
            return $url;
        }

        return $this->appendQueryParams($url, [
            'SubsiteID' => SubsiteState::singleton()->getSubsiteId(),
        ]);
    }

    /**
     * Replace the URL's fragment with the given hash (leading "#" optional). Empty hashes leave the URL as is.
     */
    protected function appendHash(string $url, ?string $hash): string
    {
        $hash = ltrim((string) $hash, '#');

        return $hash === '' ? $url : Controller::join_links($url, '#' . $hash);
    }

    /**
     * Relative URL to this block on its page, e.g. "/about-us/#OurTeam".
     */
    public function getLink($action = null): string
    {
        $link = $this->buildBlockLinkFromParent($this->getLinkParentPage(), $action);

        $this->extend('updateLink', $link, $action);

        return $link;
    }

    public function Link($action = null): string
    {
        return $this->getLink($action);
    }

    public function getBlockLink($parent): string
    {
        return $this->buildBlockLinkFromParent($parent);
    }

    /**
     * Absolute URL used by the CMS preview panel (see {@link CMSPreviewable}). The preview panel adds the
     * stage itself, so this is the plain page URL plus the block anchor.
     */
    public function PreviewLink($action = null): ?string
    {
        $link = $this->getLink($action);

        if (!$link) {
            return null;
        }

        $link = $this->appendSubsitePreviewParam((string) Director::absoluteURL($link));

        $this->extend('updatePreviewLink', $link, $action);

        return $link;
    }

    public function getMimeType(): ?string
    {
        return 'text/html';
    }

    /**
     * Draft-stage preview URL for this block, optionally scrolled to a different anchor.
     */
    public function getBlockPreviewURL($anchor = null): string
    {
        $link = $this->PreviewLink() ?? $this->appendSubsitePreviewParam(Director::absoluteBaseURL());
        $link = $this->appendQueryParams($link, ['stage' => 'Stage', 'CMSPreview' => 1]);

        return $this->appendHash($link, $anchor);
    }

    public function getAbsoluteLink($action = null): string
    {
        $record = $this->getCurrentCMSRecord();
        $link = $record ? $record->AbsoluteLink() : Director::absoluteBaseURL();

        return $this->appendHash(
            $this->appendQueryParams($link, ['stage' => 'Stage']),
            $this->getBlockID()
        );
    }

    public function AbsoluteLink($action = null): string
    {
        return $this->getAbsoluteLink($action);
    }

    /* ------------------------------------------------------------------
     * Pages using this block
     * ------------------------------------------------------------------ */

    /**
     * Comma-separated, alphabetical list of the titles of pages using this block.
     */
    public function getLinkedPagesList(): string
    {
        $titles = [];

        foreach ($this->getAllPages() as $page) {
            if ($page instanceof SiteTree && $page->exists()) {
                $titles[] = (string) $page->Title;
            }
        }

        sort($titles, SORT_STRING);

        return implode(', ', $titles);
    }

    /**
     * Every page using this block, across the main site and all subsites. Extensions can adjust the list
     * with the `updateAllPages` hook.
     *
     * @return SiteTree[]
     */
    public function getAllPages(): array
    {
        $pages = [];

        // Key by ID so pages found in more than one context are only listed once
        foreach (array_merge($this->getPagesFromMainSite(), $this->getPagesFromSubsites()) as $page) {
            $pages[$page->ID] ??= $page;
        }

        $pages = array_values($pages);

        $this->extend('updateAllPages', $pages);

        return $pages;
    }

    /**
     * Pages using this block in the current (subsite) context.
     *
     * @return SiteTree[]
     */
    public function getPagesFromSiteTree(): array
    {
        if (!$this->isInDB()) {
            return [];
        }

        return Page::get()->filter('ContentBlocks.ID', $this->ID)->toArray();
    }

    /**
     * @return SiteTree[]
     */
    public function getPagesFromMainSite(): array
    {
        return Helper::withSubsite(0, fn () => $this->getPagesFromSiteTree());
    }

    /**
     * @return SiteTree[]
     */
    public function getPagesFromSubsites(): array
    {
        if (!Helper::hasSubsites()) {
            return [];
        }

        $pages = [];

        foreach (Subsite::get()->column('ID') as $subsiteID) {
            $pages[] = Helper::withSubsite((int) $subsiteID, fn () => $this->getPagesFromSiteTree());
        }

        return array_merge([], ...$pages);
    }

    public function getBlockTemplateName(): string
    {
        return ClassInfo::shortName($this);
    }

    /**
     * Short layout name of the current template, e.g. "Default" or "Split".
     */
    public function getBlockLayoutName(): string
    {
        return $this->getTemplateLayoutName($this->Template) ?? 'Default';
    }

    public function getHtmlID(): string
    {
        return ($this->getBlockTemplateName() ?: $this->ClassName) . '_' . $this->ID;
    }

    public function getDisplayTitle(): string
    {
        $title = (string) $this->Title;
        $parent = $this->getParentPage();

        if ($parent && $parent->exists()) {
            $title .= ' (on page ' . $parent->Title . ')';
        }

        return $title;
    }

    /* ------------------------------------------------------------------
     * Permissions
     * ------------------------------------------------------------------ */

    /**
     * Extension vote for the given permission, falling back to CMS page access.
     */
    protected function checkCMSPermission(string $method, $member = null, array $context = []): bool
    {
        $extended = $this->extendedCan($method, $member, $context);

        return $extended ?? Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canView($member = null): bool
    {
        if ($member && Permission::checkMember($member, ['ADMIN', 'SITETREE_VIEW_ALL'])) {
            return true;
        }

        return $this->checkCMSPermission('canView', $member);
    }

    public function canEdit($member = null): bool
    {
        return $this->checkCMSPermission('canEdit', $member);
    }

    public function canDelete($member = null): bool
    {
        return $this->checkCMSPermission('canDelete', $member);
    }

    public function canCreate($member = null, $context = []): bool
    {
        return $this->checkCMSPermission('canCreate', $member, (array) $context);
    }

    public function canDeleteFromLive($member = null): bool
    {
        return $this->extendedCan('canDeleteFromLive', $member) ?? $this->canPublish($member);
    }

    public function canPublish($member = null): bool
    {
        if (is_numeric($member)) {
            $member = Member::get()->byID($member);
        }

        if (!$member instanceof Member) {
            $member = Security::getCurrentUser();
        }

        if ($member && Permission::checkMember($member, 'ADMIN')) {
            return true;
        }

        return $this->extendedCan('canPublish', $member) ?? $this->canEdit($member);
    }

    public function canArchive($member = null): bool
    {
        $member ??= Security::getCurrentUser();

        $extended = $this->extendedCan('canArchive', $member);

        if ($extended !== null) {
            return $extended;
        }

        if (!$this->canDelete($member)) {
            return false;
        }

        return !$this->isPublished() || $this->canDeleteFromLive($member);
    }

    /* ------------------------------------------------------------------
     * Versioning
     * ------------------------------------------------------------------ */

    /**
     * Call a Versioned method that this class overrides, so the override can build on the core behaviour.
     */
    protected function invokeVersioned(string $method, mixed ...$arguments): mixed
    {
        return $this->getExtensionInstance(Versioned::class)?->invokeExtension($this, $method, ...$arguments);
    }

    public function isPublished(): bool
    {
        return !$this->isNew() && (bool) $this->invokeVersioned('isPublished');
    }

    public function isNew(): bool
    {
        if (empty($this->ID)) {
            return true;
        }

        if (is_numeric($this->ID)) {
            return false;
        }

        return stripos($this->ID, 'new') === 0;
    }

    /**
     * Archive the block (unpublish, delete from draft, keep its history) and detach it from every page.
     */
    public function doArchive(): bool
    {
        $blockID = (int) $this->ID;

        if (!$this->invokeVersioned('doArchive')) {
            return false;
        }

        $this->detachFromAllPages($blockID);

        return true;
    }

    /**
     * Remove all page links to a block. The join table isn't versioned, so this covers draft and live.
     */
    protected function detachFromAllPages(int $blockID): void
    {
        $relation = DataObject::getSchema()->manyManyComponent(Page::class, 'ContentBlocks');

        if (!$relation) {
            return;
        }

        DB::prepared_query(
            sprintf('DELETE FROM "%s" WHERE "%s" = ?', $relation['join'], $relation['childField']),
            [$blockID]
        );
    }

    /* ------------------------------------------------------------------
     * Current page context
     * ------------------------------------------------------------------ */

    /**
     * The page being viewed on the front end (not the CMS page editor), if any.
     */
    public function getParentPage(): ?SiteTree
    {
        $controller = Controller::curr();

        if (!$controller || $controller instanceof CMSPageEditController || !$controller->hasMethod('data')) {
            return null;
        }

        try {
            $data = $controller->data();
        } catch (\Exception $e) {
            return null;
        }

        return $data instanceof SiteTree && $data->ID ? $data : null;
    }

    /**
     * The current page, but only if this block is actually linked to it.
     */
    public function getPage(): ?SiteTree
    {
        $pageID = (int) Controller::curr()?->ID;

        if (!$pageID || !$this->isInDB()) {
            return null;
        }

        return Page::get()->filter(['ID' => $pageID, 'ContentBlocks.ID' => $this->ID])->first();
    }

    /**
     * Anchor-friendly ID: the PascalCased AnchorName, or "<ShortClassName>_<ID>". Extensions can change it
     * with the `updateBlockID` hook.
     */
    public function getBlockID(): string
    {
        // Strip numbers, punctuation and special characters, then PascalCase what's left
        $words = trim((string) preg_replace('/[^a-zA-Z]+/', ' ', (string) $this->AnchorName));
        $id = str_replace(' ', '', ucwords($words));

        if ($id === '') {
            $id = $this->getHtmlID();
        }

        $this->extend('updateBlockID', $id);

        return $id;
    }

    public function getExtraRequirements(): mixed
    {
        $extraRequirements = null;

        $this->extend('updateExtraRequirements', $extraRequirements);

        return $extraRequirements;
    }

    /* ------------------------------------------------------------------
     * CMS navigation
     * ------------------------------------------------------------------ */

    /**
     * Link to edit this block in the CMS: through the page being edited when there is one, otherwise
     * through {@link BlocksAdmin} (see `cms_edit_owner`).
     */
    public function getCMSEditLink(): ?string
    {
        $link = null;

        if ($parent = $this->getCMSParentPage()) {
            $link = Director::absoluteURL(CMSPageEditController::singleton()->Link(Controller::join_links(
                'EditForm',
                $parent->ID,
                'field/ContentBlocks/item',
                $this->ID,
                'edit'
            )));
        }

        // Adds the BlocksAdmin link if none was set above (CMSEditLinkExtension)
        $this->extend('updateCMSEditLink', $link);

        return $link;
    }

    public function getCMSParentPage(): ?SiteTree
    {
        $parent = $this->getCurrentCMSRecord();

        return $parent instanceof SiteTree && $parent->exists() ? $parent : null;
    }

    public function getCMSSiblingBlocks(): ?DataList
    {
        return $this->getCMSParentPage()?->ContentBlocks()->sort('SortOrder');
    }

    /**
     * Icon links to every block on the page being edited, with the current block marked active.
     */
    public function getCMSSiblingBlocksLinks(): ?string
    {
        $blocks = $this->getCMSSiblingBlocks();

        if (!$blocks) {
            return null;
        }

        $items = [];

        foreach ($blocks as $block) {
            $items[] = ArrayData::create([
                'Active' => (int) $block->ID === (int) $this->ID,
                'Link' => $block->getCMSEditLink(),
                'BlockID' => $block->getBlockID(),
                'Icon' => $block->getIconForCMS(),
            ]);
        }

        return (string) Helper::renderTemplate('BlockSiblings', ['Blocks' => ArrayList::create($items)]);
    }

    /* ------------------------------------------------------------------
     * Position-based CSS classes
     * ------------------------------------------------------------------ */

    public function isFirstBlock(): bool
    {
        return $this->isBlockAtEdge('ASC');
    }

    public function isLastBlock(): bool
    {
        return $this->isBlockAtEdge('DESC');
    }

    /**
     * Whether this block is the first (ASC) or last (DESC) block on the current page.
     */
    protected function isBlockAtEdge(string $direction): bool
    {
        $edgeBlock = $this->getPage()?->ContentBlocks()->sort('SortOrder', $direction)->first();

        return $edgeBlock !== null && (int) $edgeBlock->ID === (int) $this->ID;
    }

    /**
     * Space-separated CSS classes for the block wrapper, based on the block's position on the page.
     */
    public function getExtraClasses(): string
    {
        $blockConfig = $this->config();

        $positionClasses = match (true) {
            $this->isFirstBlock() => $blockConfig->get('first_block_classes'),
            $this->isLastBlock() => $blockConfig->get('last_block_classes'),
            default => $blockConfig->get('other_block_classes'),
        };

        $extraClasses = array_merge(
            (array) ($positionClasses ?: []),
            (array) ($blockConfig->get('common_block_classes') ?: [])
        );

        // Allow extensions to modify the extra classes, then add any block specific classes
        $extraClasses = array_merge(
            $this->updateExtraClasses($extraClasses),
            $this->getBlockSpecificExtraClasses()
        );

        return implode(' ', $extraClasses);
    }

    public function updateExtraClasses(array $classes): array
    {
        // Allow extensions to modify the extra classes
        $this->extend('updateExtraClasses', $classes);

        return $classes;
    }

    public function getBlockSpecificExtraClasses(): array
    {
        $classes = [];

        return $this->updateBlockSpecificExtraClasses($classes);
    }

    public function updateBlockSpecificExtraClasses(array &$classes): array
    {
        // Allow extensions to add block specific extra classes
        $this->extend('updateBlockSpecificExtraClasses', $classes);

        return $classes;
    }
}
