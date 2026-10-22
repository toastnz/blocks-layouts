<?php

namespace Toast\Blocks\Helpers;

use SilverStripe\Assets\File;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Model\ArrayData;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Subsites\Model\Subsite;
use SilverStripe\Subsites\State\SubsiteState;
use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass;
use Toast\Blocks\Block;
use Toast\Blocks\Extensions\PageExtension;

/**
 * Shared, stateless helpers used across the blocks module.
 */
class Helper
{
    /**
     * Composer name of this module.
     */
    public const MODULE_NAME = 'toastnz/blocks-layouts';

    /**
     * Absolute filesystem path of this module, resolved through the module manifest.
     */
    public static function getModulePath(): string
    {
        $module = ModuleLoader::getModule(self::MODULE_NAME);

        return $module ? $module->getPath() : BASE_PATH . '/' . TOAST_BLOCKS_DIR;
    }

    /**
     * Whether the subsites module is installed.
     */
    public static function hasSubsites(): bool
    {
        return class_exists(Subsite::class);
    }

    /**
     * Run a callback in the context of another subsite, always restoring the original subsite afterwards.
     * Without the subsites module the callback is simply run.
     */
    public static function withSubsite(int $subsiteID, callable $callback): mixed
    {
        if (!self::hasSubsites()) {
            return $callback();
        }

        $originalSubsiteID = SubsiteState::singleton()->getSubsiteId();
        Subsite::changeSubsite($subsiteID);

        try {
            return $callback();
        } finally {
            Subsite::changeSubsite($originalSubsiteID);
        }
    }

    /**
     * The block classes editors may add, from PageExtension.available_blocks.
     *
     * A listed block that extends another listed block under the same short name replaces it: both render
     * with the same templates and layouts, so only the replacement is offered. Existing blocks of the hidden
     * class are not affected.
     *
     * @return string[]
     */
    public static function getAvailableBlockClasses(): array
    {
        $classes = array_values(array_unique(
            (array) (Config::inst()->get(PageExtension::class, 'available_blocks') ?: [])
        ));

        return array_values(array_filter(
            $classes,
            fn (string $class) => !self::isReplacedByListedSubclass($class, $classes)
        ));
    }

    /**
     * @param string[] $classes
     */
    private static function isReplacedByListedSubclass(string $class, array $classes): bool
    {
        if (!class_exists($class)) {
            return false;
        }

        foreach ($classes as $other) {
            if ($other !== $class
                && class_exists($other)
                && is_subclass_of($other, $class)
                && ClassInfo::shortName($other) === ClassInfo::shortName($class)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * An "add new block" dropdown limited to the available block classes.
     */
    public static function createAddNewMultiClass(?array $classes = null): GridFieldAddNewMultiClass
    {
        return GridFieldAddNewMultiClass::create()
            ->setClasses($classes ?? self::getAvailableBlockClasses());
    }

    /**
     * Resolve a configured resource path to a URL. Supports module resource syntax
     * ("vendor/module: path/to/file.svg"), the legacy "[resources]" token, absolute URLs and
     * root-relative paths.
     */
    public static function resolveResourceURL(string $path): string
    {
        if (self::isModuleResource($path)) {
            return (string) ModuleResourceLoader::resourceURL($path);
        }

        $path = str_replace('[resources]', TOAST_RESOURCES_DIR, $path);

        if (preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, '/')) {
            return $path;
        }

        return Controller::join_links(Director::baseURL(), $path);
    }

    /**
     * Whether the path uses module resource syntax, e.g. "toastnz/blocks-layouts: client/images/icon.svg".
     */
    public static function isModuleResource(string $path): bool
    {
        return (bool) preg_match('#^[\w.-]+/[\w.-]+:\s*\S#', $path);
    }

    /**
     * Render one of the module's CMS templates (templates/Toast/Blocks/Includes/<name>.ss). Projects can
     * override any of them from their own theme or app templates folder.
     */
    public static function renderTemplate(string $name, array $data = []): DBHTMLText
    {
        return ArrayData::create($data)->renderWith(['type' => 'Includes', 'Toast\\Blocks\\' . $name]);
    }

    /**
     * A notice for the CMS edit form. Plain strings are escaped; pass a DBHTMLText for markup.
     *
     * @param string $type Bootstrap alert type, e.g. "info" or "warning"
     */
    public static function createCMSNoticeField(
        string $name,
        string|DBHTMLText $message,
        string $type = 'info',
        ?string $heading = null
    ): LiteralField {
        return LiteralField::create($name, self::renderTemplate('CMSNotice', [
            'Type' => $type,
            'Heading' => $heading,
            'Message' => $message,
        ])->forTemplate());
    }

    /**
     * Small image preview used in CMS summary columns.
     */
    public static function getImageThumbnailHTML(?File $image, int $size = 80): DBHTMLText
    {
        return self::renderTemplate('ImageThumbnail', [
            'Image' => $image && $image->exists() ? $image : null,
            'Size' => $size,
        ]);
    }

    /**
     * HTML listing every page (grouped by subsite when subsites are installed) that uses the given block.
     */
    public static function getBlockPageLinksHTMLForCMS(?Block $block = null): string
    {
        $pages = $block ? $block->getAllPages() : [];

        $html = (string) self::renderTemplate('BlockPageLinks', [
            'Groups' => ArrayList::create(self::groupPagesForCMS($pages)),
        ]);

        $block?->extend('updateBlockPageLinksHTMLForCMS', $html, $pages);

        return $html;
    }

    /**
     * Pages as template-ready groups: one per subsite when subsites are installed, otherwise a single untitled group.
     *
     * @param SiteTree[] $pages
     * @return ArrayData[]
     */
    private static function groupPagesForCMS(array $pages): array
    {
        if (!$pages) {
            return [];
        }

        if (!self::hasSubsites()) {
            return [ArrayData::create(['Title' => null, 'Pages' => self::pageLinksForCMS($pages)])];
        }

        $grouped = [];

        foreach ($pages as $page) {
            $grouped[(int) $page->SubsiteID][] = $page;
        }

        $groups = [];

        foreach ($grouped as $subsiteID => $subsitePages) {
            $groups[] = ArrayData::create([
                'Title' => self::getSubsiteTitle($subsiteID),
                'Pages' => self::pageLinksForCMS($subsitePages),
            ]);
        }

        return $groups;
    }

    /**
     * @param SiteTree[] $pages
     */
    private static function pageLinksForCMS(array $pages): ArrayList
    {
        return ArrayList::create(array_map(
            fn (SiteTree $page) => ArrayData::create([
                'Title' => $page->Title,
                'Link' => $page->getCMSEditLink(),
                'IconClass' => $page->config()->get('cms_icon_class'),
            ]),
            $pages
        ));
    }

    private static function getSubsiteTitle(int $subsiteID): string
    {
        if ($subsiteID === 0) {
            return 'Main Site';
        }

        return Subsite::get()->byID($subsiteID)?->Title ?: 'Unknown Subsite';
    }
}
