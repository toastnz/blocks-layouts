<?php

namespace Toast\Blocks;


use Page;
use ReflectionClass;
use SilverStripe\ORM\DB;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use Toast\Blocks\Helpers\Helper;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Member;
use SilverStripe\Control\Director;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HiddenField;
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
use Toast\OpenCMSPreview\Fields\OpenCMSPreview;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\CMS\Controllers\CMSPageEditController;

class Block extends DataObject
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

    protected static $available_layouts_cache = [];

    /**
     * Per-layout configuration, keyed by short layout name.
     * Supports: label (string), layout_icon_path (path, supports [resources] token), layout_icon_class (string: CSS class), disabled (bool).
     *
     * Example YAML (in your project's _config/blocks.yml):
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
     * Full class-name keys are still supported for backward compatibility.
     *
     * To see available layout_icon_class options refer to client/fonts/layout-icons/font/preview.html
     */

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

    public function getIconForCMS(): ?DBField
    {
        if (self::config()->get('block-icon') == null) {
            return DBField::create_field('HTMLText', '
                <div data-block-id="' . $this->BlockID . '" class="toast-block-icon" style="text-align: center; margin: 0 auto; margin; padding: 10px;">
                    <span class="toast-block-icon__media ' . static::$icon_class . '" style="position: relative; font-size: 40px; line-height: 0;"></span>
                </div>
                <span class="toast-block-title" style="display: block; font-size: 10px; font-weight: bold; line-height: 10px; text-transform: uppercase; text-align: center; margin: 0; padding: 0;">' . $this->i18n_singular_name() . '</span>
            ');
        }

        $icon = $this->replaceResourcesToken(self::config()->get('block-icon'), TOAST_RESOURCES_DIR);

        return DBField::create_field('HTMLText', '
            <div data-block-id="' . $this->BlockID . '" title="' . $this->i18n_singular_name() . '" style="margin: 0 auto;width:50px; height:50px; white-space:nowrap; ">
                <img style="width:100%;height:100%;display:inline-block !important" src="' . $icon . '">
            </div>
            <span style="font-weight:bold;color:#377cff;display:block;line-height:10px;text-align:center;margin:0px 0 0;padding:0;font-size:10px;text-transform:uppercase;">' . $this->i18n_singular_name() . '</span>
        ');
    }

    public function IconForCMS(): ?DBField
    {
        return $this->getIconForCMS();
    }

    public function forTemplate(): string
    {
        $template = $this->resolveTemplateWithFallback($this->Template);

        $this->extend('updateBlockTemplate', $template);

        $defaultTemplate = $this->getDefaultTemplateClass();

        return $this->renderWith([$template, $defaultTemplate, 'Toast\Blocks\Default\Block']);
    }

    public function getCMSFields()
    {
        // Load the custom icons font for layout options
        Requirements::css('toastnz/blocks-layouts: client/fonts/layout-icons/font/layouticons.css');

        $this->beforeUpdateCMSFields(function ($fields) {
            // Start by removing fields we don't want to show
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
                TextField::create('Heading', 'Heading')
                    ->setDescription('&lt;h2&gt;'),
                HTMLEditorField::create('Content', 'Content')
            ]);

            if ($layoutOptionsField = $this->getTemplateOptionsField()) {
                $fields->insertAfter('AnchorName', $layoutOptionsField);
            }

            if ($notice = $this->getTemplateMissingNoticeField()) {
                $fields->insertAfter('AnchorName', $notice);
            }

            // Exit here if the block hasn't been saved yet
            if (!$this->exists()) return;

            $fields->addFieldsToTab('Root.More', [
                OpenCMSPreview::create($this->getBlockPreviewURL()),
                HeaderField::create('UsageHeading', 'Link to this block'),
                LiteralField::create('BlockLink', 'Block Link <br><a href="' . $this->AbsoluteLink() . '" target="_blank">' . $this->AbsoluteLink() . '</a><hr>'),
                ReadonlyField::create('Shortcode', 'Shortcode', '[block,id=' . $this->ID . ']'),
                ReadonlyField::create('BlockID', 'Block ID', $this->getBlockID()),
            ]);

            $fields->insertBefore('Title', HeaderField::create('PageLinksHeading', 'Pages using this block'));
            $fields->insertBefore('Title', LiteralField::create('PageLinks', Helper::getBlockPageLinksHTMLForCMS($this)));
            $fields->insertBefore('Title', HeaderField::create('BlockSettingsHeading', 'Block Settings'));
        });

        return parent::getCMSFields();
    }

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
        $layoutConfig = (array) ($this->config()->get('layout_config') ?: []);

        if (!empty($layoutConfig['exclude_layouts']) && is_array($layoutConfig['exclude_layouts'])) {
            $excludeLayouts = array_merge($excludeLayouts, $layoutConfig['exclude_layouts']);
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
        $className = $this->getLayoutClassName($layout, $templateName);

        if (!empty($layoutConfig[$layout]) && is_array($layoutConfig[$layout])) {
            return $layoutConfig[$layout];
        }

        if (!empty($layoutConfig[$className]) && is_array($layoutConfig[$className])) {
            return $layoutConfig[$className];
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
            TOAST_BLOCKS_DIR,
            TOAST_BLOCKS_TEMPLATE_DIR,
            Config::inst()->get('Toast\Blocks\Extensions\PageExtension', 'layout_src'),
        ]));
    }

    protected function replaceResourcesToken(?string $path, string $replacement): ?string
    {
        if (!$path) {
            return null;
        }

        return str_replace('[resources]', $replacement, $path);
    }

    protected function resolvePublicResourcePath(?string $path): ?string
    {
        $resolvedPath = $this->replaceResourcesToken($path, RESOURCES_DIR);

        if (!$resolvedPath) {
            return null;
        }

        return Director::publicFolder() . '/' . $resolvedPath;
    }

    protected function getTemplateFileName(string $template): string
    {
        return pathinfo($template, PATHINFO_FILENAME);
    }

    protected function getTemplateParts(?string $template): array
    {
        if (!$template) {
            return [];
        }

        return explode('\\', $template);
    }

    protected function getTemplateLayoutName(?string $template): ?string
    {
        $parts = $this->getTemplateParts($template);

        if (!isset($parts[2]) || !$parts[2]) {
            return null;
        }

        return $parts[2];
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

        $layoutName = htmlspecialchars($this->getTemplateLayoutName($this->Template) ?: $this->Template, ENT_QUOTES);
        $defaultLayoutName = htmlspecialchars($this->getTemplateLayoutName($this->getDefaultTemplateClass()) ?: 'Default', ENT_QUOTES);

        $html = '<div class="message warning" style="margin-bottom:1em">'
            . '<strong>Layout not available:</strong> The previously selected layout \'<em>' . $layoutName . '</em>\' '
            . 'is no longer available. This block is currently rendering with the <strong>' . $defaultLayoutName . '</strong> layout. '
            . 'Please choose a new layout below and save.'
            . '</div>';

        return LiteralField::create('TemplateMissingNotice', $html);
    }

    protected function getAvailableTemplateClasses(string $templateName): array
    {
        $classes = [];
        $layouts = $this->getAvailableLayouts($templateName);

        foreach ($layouts as $layout => $templates) {
            foreach ($templates as $template) {
                $fileTemplateName = $this->getTemplateFileName($template);
                $classes[] = $this->getLayoutClassName($layout, $fileTemplateName);
            }
        }

        return array_values(array_unique($classes));
    }

    protected function resolveTemplateWithFallback(?string $template): string
    {
        $defaultTemplate = $this->getDefaultTemplateClass();
        $templateName = $this->getBlockTemplateName();
        $availableTemplates = $this->getAvailableTemplateClasses($templateName);

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

    protected function getCSSDirectory(): ?string
    {
        $cssDir = Config::inst()->get('Toast\Blocks\Extensions\PageExtension', 'layout_dist_dir');

        return $cssDir ?: null;
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
        $cssDir = $this->getCSSDirectory();
        $cssFileName = $this->getCSSFileName($template);

        if (!$cssDir || !$cssFileName) {
            return null;
        }

        return BASE_PATH . '/' . $cssDir . '/' . $cssFileName;
    }

    protected function getCSSFileRelativePath(?string $template = null): ?string
    {
        $cssDir = $this->getCSSDirectory();
        $cssFileName = $this->getCSSFileName($template);

        if (!$cssDir || !$cssFileName) {
            return null;
        }

        return $cssDir . '/' . $cssFileName;
    }

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

        foreach ($layouts as $layout => $templates) {
            $layouts[$layout] = array_values(array_unique($templates));
        }

        $layouts = array_filter($layouts, function ($templates) {
            return !empty($templates);
        });

        static::$available_layouts_cache[$cacheKey] = $layouts;

        return $layouts;
    }

    protected function discoverModuleLayouts(): array
    {
        $directory = BASE_PATH . '/' . TOAST_BLOCKS_DIR . '/' . TOAST_BLOCKS_TEMPLATE_DIR;

        return [
            basename($directory) => $this->scanTemplateDirectory($directory),
        ];
    }

    protected function discoverAdditionalLayouts(): array
    {
        $layouts = [];
        $directory = BASE_PATH . '/' . Config::inst()->get('Toast\Blocks\Extensions\PageExtension', 'layout_src');

        if (!is_dir($directory)) {
            return $layouts;
        }

        foreach (glob($directory . '/*', GLOB_ONLYDIR) ?: [] as $folderPath) {
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

    protected function filterLayoutsByClassName(array $layouts, string $className): array
    {
        foreach ($layouts as $layout => $templates) {
            $layouts[$layout] = array_values(array_filter($templates, function ($template) use ($className) {
                return pathinfo($template, PATHINFO_FILENAME) === $className;
            }));
        }

        return array_filter($layouts, function ($templates) {
            return !empty($templates);
        });
    }

    protected function filterToConfiguredLayouts(array $layouts): array
    {
        $layoutConfig = $this->getLayoutConfig();

        if (empty($layoutConfig)) {
            return $layouts;
        }

        $configKeys = array_keys($layoutConfig);

        return array_filter($layouts, function ($templates, $layout) use ($configKeys) {
            // Match by short layout name key (preferred)
            if (in_array($layout, $configKeys, true)) {
                return true;
            }

            // Match by full class-name key prefix (backward compatibility)
            $prefix = 'Toast\\Blocks\\' . $layout . '\\';
            foreach ($configKeys as $key) {
                if (strpos($key, $prefix) === 0) {
                    return true;
                }
            }

            return false;
        }, ARRAY_FILTER_USE_BOTH);
    }

    protected function filterDisabledLayouts(array $layouts): array
    {
        if (!$this->getLayoutConfig() && !$this->getExcludedLayouts()) {
            return $layouts;
        }

        foreach ($layouts as $layout => $templates) {
            $layouts[$layout] = array_values(array_filter($templates, function ($template) use ($layout) {
                $name = pathinfo($template, PATHINFO_FILENAME);
                return !$this->isLayoutDisabled($layout, $name);
            }));
        }

        return array_filter($layouts, function ($templates) {
            return !empty($templates);
        });
    }

    protected function getLayoutIconSourcePath(): ?string
    {
        $iconSource = Config::inst()->get('Toast\Blocks\Extensions\PageExtension', 'layout_icon_src');

        if (!$iconSource) {
            return null;
        }

        $path = $this->resolvePublicResourcePath($iconSource);

        return $path && is_dir($path) ? $path : null;
    }

    protected function getDiscoveredLayoutIcon(string $layout, string $templateName, ?string $iconSourcePath): ?string
    {
        if (!$iconSourcePath) {
            return null;
        }

        $iconPath = $iconSourcePath . '/' . strtolower($layout) . '/' . strtolower($templateName) . '.svg';

        if (!is_file($iconPath)) {
            return null;
        }

        $icon = file_get_contents($iconPath);

        return $icon === false ? null : $icon;
    }

    protected function getConfiguredLayoutIcon(string $layout, string $templateName): ?string
    {
        $config = $this->getLayoutConfigEntry($layout, $templateName);

        if (empty($config['layout_icon_path'])) {
            return null;
        }

        $iconPath = $this->resolvePublicResourcePath($config['layout_icon_path']);

        if (!$iconPath || !is_file($iconPath)) {
            return null;
        }

        $icon = file_get_contents($iconPath);

        return $icon === false ? null : $icon;
    }

    protected function getLayoutOptionLabel(string $layout, string $templateName): string
    {
        $config = $this->getLayoutConfigEntry($layout, $templateName);

        return !empty($config['label']) ? $config['label'] : $layout;
    }

    protected function getLayoutIconClass(string $layout, string $templateName): ?string
    {
        $config = $this->getLayoutConfigEntry($layout, $templateName);

        return !empty($config['layout_icon_class']) ? (string) $config['layout_icon_class'] : null;
    }

    protected function buildLayoutOption(string $layout, string $template, ?string $iconSourcePath): ?DBField
    {
        $templateName = $this->getTemplateFileName($template);

        // Priority: 1) explicit SVG path (icon:), 2) icon class (layout_icon_class:) — both from YAML config
        // and both override directory discovery. 3) auto-discovered SVG from layout_icon_src directory.
        $icon = $this->getConfiguredLayoutIcon($layout, $templateName);

        if (!$icon) {
            $iconClass = $this->getLayoutIconClass($layout, $templateName);
            if ($iconClass) {
                $icon = '<span class="layout-icons ' . htmlspecialchars($iconClass, ENT_QUOTES) . '"></span>';
            }
        }

        if (!$icon) {
            $icon = $this->getDiscoveredLayoutIcon($layout, $templateName, $iconSourcePath);
        }

        if (!$icon) {
            return null;
        }

        $html = '<div class="blockThumbnail">' . $icon . '</div><strong class="title" title="Template file: ' . $template . '">' . $this->getLayoutOptionLabel($layout, $templateName) . '</strong>';

        return DBField::create_field(DBHTMLText::class, $html);
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

                if (empty($icons[$className])) {
                    $allHaveIcons = false;
                }

                $options[$className] = $this->getLayoutOptionLabel($layout, $templateName);
            }
        }

        return [$options, $allHaveIcons];
    }

    protected function createTemplateOptionsField(array $options, array $icons, bool $allHaveIcons)
    {
        if ($allHaveIcons) {
            return OptionsetField::create('Template', 'Layout', $icons, $this->Template)
                ->addExtraClass('toast-block-layouts');
        }

        return DropdownField::create('Template', 'Layout', $options, $this->Template)
            ->addExtraClass('toast-block-layouts');
    }

    public function getAvailableLayouts($className = null)
    {
        $layouts = $this->discoverLayouts();

        if ($className) {
            $layouts = $this->filterLayoutsByClassName($layouts, $className);
        }

        $layouts = $this->filterToConfiguredLayouts($layouts);

        return $this->filterDisabledLayouts($layouts);
    }

    public function getOptionsForLayouts($layouts = [])
    {
        $optionset = [];
        $iconSourcePath = $this->getLayoutIconSourcePath();

        foreach ($layouts as $layout => $templates) {
            foreach ($templates as $template) {
                $templateName = $this->getTemplateFileName($template);
                $option = $this->buildLayoutOption($layout, $template, $iconSourcePath);

                if (!$option) {
                    continue;
                }

                $optionset[$this->getLayoutClassName($layout, $templateName)] = $option;
            }
        }

        return $optionset;
    }

    public function getTemplateOptionsField()
    {
        $shortName = (new ReflectionClass($this))->getShortName();
        $layouts = $this->getAvailableLayouts($shortName);
        $icons = $this->getOptionsForLayouts($layouts);
        $field = HiddenField::create('Template', 'Layout', $this->Template);
        [$options, $allHaveIcons] = $this->getTemplateFieldOptions($layouts, $shortName, $icons);

        if (count($options) < 2) return $field;

        return $this->createTemplateOptionsField($options, $icons, $allHaveIcons);
    }

    public function getCSSFile(): ?string
    {
        $absolutePath = $this->getCSSFileAbsolutePath();

        if (!$absolutePath || !is_file($absolutePath)) {
            return null;
        }

        return $this->getCSSFileRelativePath();
    }

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
        if ($this->exists()) return $this->getField('Title');
        // Otherwise, return the singular name of the block as a default title for new blocks.
        return $this->getField('Title') ?: $this->i18n_singular_name()?: 'Untitled Block';
    }

    public function getApiURL(): string
    {
        return Controller::join_links(Controller::curr()->AbsoluteLink(), 'Block', $this->ID);
    }

    protected function getCurrentCMSRecord()
    {
        $controller = Controller::curr();

        if ($controller instanceof CMSMain) {
            return $controller->currentRecord();
        }

        return null;
    }

    protected function getLinkParentPage(): ?SiteTree
    {
        if ($parent = $this->getCurrentCMSRecord()) {
            return $parent;
        }

        if ($parent = $this->getParentPage()) {
            return $parent;
        }

        $pages = $this->getAllPages();
        if(count($pages) === 0) {
            return null;
        }
        return SiteTree::get()->byID($pages[0]);
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
        $separator = strpos($url, '?') !== false ? '&' : '?';

        return $url . $separator . http_build_query($params);
    }

    protected function appendSubsitePreviewParam(string $url): string
    {
        if (!class_exists(Subsite::class)) {
            return $url;
        }

        return $this->appendQueryParams($url, [
            'SubsiteID' => SubsiteState::singleton()->getSubsiteId(),
        ]);
    }

    protected function appendHash(string $url, ?string $hash): string
    {
        if (!$hash) {
            return $url;
        }

        $normalizedHash = ltrim($hash, '#');

        if ($normalizedHash === '') {
            return $url;
        }

        $urlWithoutHash = explode('#', $url, 2)[0];

        return $urlWithoutHash . '#' . $normalizedHash;
    }

    public function getLink($action = null): string
    {
        return $this->buildBlockLinkFromParent($this->getLinkParentPage(), $action);
    }

    public function Link($action = null): string
    {
        return $this->getLink($action);
    }

    public function getBlockLink($parent): string
    {
        return $this->buildBlockLinkFromParent($parent);
    }

    public function getBlockPreviewURL($anchor = null): string
    {
        $splitLink = $this->splitLinkHash($this->getLink());
        $link = Controller::join_links(Director::absoluteBaseURL(), $splitLink['location']);
        $link = $this->appendQueryParams($link, ['stage' => 'Stage', 'CMSPreview' => 1]);
        $link = $this->appendSubsitePreviewParam($link);

        return $this->appendHash($link, $anchor ?: $splitLink['hash']);
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
    //         $path = $controller->currentRecord()->Link();
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

    public function getAbsoluteLink($action = null): string
    {
        $link = Director::absoluteBaseURL();

        if ($record = $this->getCurrentCMSRecord()) {
            $link = $record->AbsoluteLink();
        }

        return $this->appendHash(
            $this->appendQueryParams($link, ['stage' => 'Stage']),
            $this->owner->getBlockID()
        );
    }

    public function AbsoluteLink($action = null): string
    {
        return $this->getAbsoluteLink($action);
    }

    public function getLinkedPagesList(): string
    {
        $pagesWithBlock = $this->getAllPages();
        // only show pages
        $pages = [];
        foreach ($pagesWithBlock as $page) {
            if ($page instanceof SiteTree && $page->exists()) {
                $pages[] = $page;
            }
        }
        // Sort the pages by title
        usort($pages, function ($a, $b) {
            return strcmp($a->Title, $b->Title);
        });
        // Return the sorted pages in implode format
        return implode(', ', array_map(function ($page) {
            return $page->Title;
        }, $pages));
    }

    public function getAllPages(): array
    {
        $pages = array_merge($this->getPagesFromMainSite(), $this->getPagesFromSubsites());

        // make the array unique
        return array_unique($pages, SORT_REGULAR);
    }

    public function getPagesFromSiteTree(): array
    {
        $pages = SiteTree::get()
            ->leftJoin('Page_ContentBlocks', '"Page_ContentBlocks"."PageID" = "SiteTree"."ID"')
            ->where('"Page_ContentBlocks"."Blocks_BlockID" = ' . $this->ID)
            ->distinct(true, ['"SiteTree"."ID"']);

        return $pages->toArray();
    }

    public function getPagesFromMainSite(): array
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

    public function getPagesFromSubsites(): array
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

    public function getBlockTemplateName(): string
    {
        $reflect = new ReflectionClass($this);

        $templateName = $reflect->getShortName() ?: '';

        return $templateName;
    }

    public function getBlockLayoutName(): string
    {
        $templateParts = explode('\\', $this->Template);
        if (count($templateParts) >= 3) {
            return $templateParts[2];
        }

        return 'Default';
    }

    public function getHtmlID(): string
    {
        $templateName = $this->getBlockTemplateName() ?: $this->ClassName;

        return $templateName . '_' . $this->ID;
    }

    public function getDisplayTitle(): string
    {
        $title = $this->Title;

        $parent = $this->getParentPage();

        if ($parent && $parent->exists()) {
            $title .= ' (on page ' . $parent->Title . ')';
        }

        return $title;
    }

    public function canView($member = null): bool
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

    public function canEdit($member = null): bool
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canDelete($member = null): bool
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canCreate($member = null, $context = []): bool
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canDeleteFromLive($member = null): bool
    {
        $extended = $this->extendedCan('canDeleteFromLive', $member);

        if ($extended !== null) {
            return $extended;
        }

        return $this->canPublish($member);
    }

    public function canPublish($member = null): bool
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

    public function isPublished(): bool
    {
        if ($this->isNew()) {
            return false;
        }

        return (DB::prepared_query("SELECT \"ID\" FROM \"Blocks_Block_Live\" WHERE \"ID\" = ?", [$this->ID])->value())
            ? true
            : false;
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

    public function getParentPage(): ?SiteTree
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

        return null;
    }

    public function doArchive(): bool
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

    public function canArchive($member = null): bool
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

    public function getPage(): ?SiteTree
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

        return null;
    }

    public function getBlockID(): string
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

    public function getExtraRequirements(): mixed
    {
        $extraRequirements = null;

        $this->extend('updateExtraRequirements', $extraRequirements);

        return $extraRequirements;
    }

    public function getCMSEditLink(): ?string
    {
        if ($parent = $this->getCMSParentPage()) {
            $parentID = $parent->ID;
            $parentEditLink = $this->getCMSParentPage()->CMSEditLink();
            // Replace /show/$ID with /EditForm/$ID
            $parentEditFormLink = str_replace("/show/$parentID", "/EditForm/$parentID", $parentEditLink);

            return $parentEditFormLink . '/field/ContentBlocks/item/' . $this->ID . '/edit';
        }

        return null;
    }

    public function getCMSParentPage(): ?SiteTree
    {
        // Get the current controller
        $controller = Controller::curr();

        $parent = null;

        if ($controller instanceof CMSMain) {
            // Call the currentRecord() method on the controller instance
            $parent = $controller->currentRecord();
        }

        if ($parent && $parent->exists()) {
            return $parent;
        }

        return null;
    }

    public function getCMSSiblingBlocks(): ?DataList
    {
        $parent = $this->getCMSParentPage();

        if ($parent && $parent->exists()) {
            return $parent->ContentBlocks()->sort('SortOrder');
        }

        return null;
    }

    public function getCMSSiblingBlocksLinks(): ?string
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

    public function isFirstBlock(): bool
    {
        if ($page = $this->getPage()) {
            if ($firstBlock = $page->ContentBlocks()->Sort('SortOrder')->first()) {
                return $this->ID === $firstBlock->ID;
            }
        }

        return false;
    }

    public function isLastBlock(): bool
    {
        if ($page = $this->getPage()) {
            if ($lastBlock = $page->ContentBlocks()->Sort('SortOrder', 'DESC')->first()) {
                return $this->ID === $lastBlock->ID;
            }
        }

        return false;
    }

    public function getExtraClasses(): string
    {
        // extra classes as array of strings
        $extraClasses = [];

        // Read the block's config to get first_block_classes, last_block_classes and block_classes
        $blockConfig = Config::forClass(get_class($this));

        if ($this->isFirstBlock()) {
            if ($firstBlockClasses = $blockConfig->get('first_block_classes')) {
                $extraClasses = array_merge($extraClasses, $firstBlockClasses);
            }
        } elseif ($this->isLastBlock()) {
            if ($lastBlockClasses = $blockConfig->get('last_block_classes')) {
                $extraClasses = array_merge($extraClasses, $lastBlockClasses);
            }
        } else {
            if ($otherBlockClasses = $blockConfig->get('other_block_classes')) {
                $extraClasses = array_merge($extraClasses, $otherBlockClasses);
            }
        }

        if ($commonBlockClasses = $blockConfig->get('common_block_classes')) {
            $extraClasses = array_merge($extraClasses, $commonBlockClasses);
        }

        // Allow extensions to modify the extra classes
        $extraClasses = $this->updateExtraClasses($extraClasses);
        // Get any block specific extra classes from extensions / other blocks
        $blockSpecificClasses = $this->getBlockSpecificExtraClasses();

        // Merge the block specific classes with the other extra classes
        $extraClasses = array_merge($extraClasses, $blockSpecificClasses);

        // Return the array as a string
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
        // Set up an empty array for the classes
        $classes = [];

        // Return the classes as an array
        return $this->updateBlockSpecificExtraClasses($classes);
    }

    public function updateBlockSpecificExtraClasses(array &$classes): array
    {
        // Allow extensions to add block specific extra classes
        $this->extend('updateBlockSpecificExtraClasses', $classes);
        return $classes;
    }
}
