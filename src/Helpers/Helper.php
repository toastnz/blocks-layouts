<?php

namespace Toast\Blocks\Helpers;

use SilverStripe\Assets\File;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Convert;
use SilverStripe\Core\Manifest\ModuleLoader;
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
     * @return string[]
     */
    public static function getAvailableBlockClasses(): array
    {
        return (array) (Config::inst()->get(PageExtension::class, 'available_blocks') ?: []);
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
     * Resolve a configured resource path to a URL. Supports the "[resources]" token, absolute URLs and
     * root-relative paths.
     */
    public static function resolveResourceURL(string $path): string
    {
        $path = str_replace('[resources]', TOAST_RESOURCES_DIR, $path);

        if (preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, '/')) {
            return $path;
        }

        return Controller::join_links(Director::baseURL(), $path);
    }

    /**
     * Small image preview used in CMS summary columns.
     */
    public static function getImageThumbnailHTML(?File $image, int $size = 80): DBHTMLText
    {
        $html = '';

        if ($image && $image->exists()) {
            $html = sprintf(
                '<img src="%s" alt="%s" width="%d" height="%d" style="object-fit: contain" />',
                Convert::raw2att($image->getAbsoluteURL()),
                Convert::raw2att((string) $image->Title),
                $size,
                $size
            );
        }

        return DBField::create_field(DBHTMLText::class, $html);
    }

    /**
     * HTML listing every page (grouped by subsite when subsites are installed) that uses the given block.
     */
    public static function getBlockPageLinksHTMLForCMS(?Block $block = null): string
    {
        $pages = $block ? $block->getAllPages() : [];

        if (!$pages) {
            $html = '<p class="message warning" style="margin-bottom: 0;">No pages are using this block.</p>';
        } elseif (self::hasSubsites()) {
            $html = self::renderPageLinksBySubsite($pages);
        } else {
            $html = implode('', array_map([self::class, 'renderPageLink'], $pages));
        }

        $html = '<div class="blocks-layouts-page-links">' . $html . '</div>';

        $block?->extend('updateBlockPageLinksHTMLForCMS', $html, $pages);

        return $html;
    }

    /**
     * @param SiteTree[] $pages
     */
    private static function renderPageLinksBySubsite(array $pages): string
    {
        $grouped = [];

        foreach ($pages as $page) {
            $grouped[(int) $page->SubsiteID][] = $page;
        }

        $html = '';

        foreach ($grouped as $subsiteID => $subsitePages) {
            $html .= '<h4>' . Convert::raw2xml(self::getSubsiteTitle($subsiteID)) . '</h4>';
            $html .= implode('', array_map([self::class, 'renderPageLink'], $subsitePages));
        }

        return $html;
    }

    private static function getSubsiteTitle(int $subsiteID): string
    {
        if ($subsiteID === 0) {
            return 'Main Site';
        }

        return Subsite::get()->byID($subsiteID)?->Title ?: 'Unknown Subsite';
    }

    private static function renderPageLink(SiteTree $page): string
    {
        return sprintf(
            '<div class="blocks-layouts-page-links__item"><i class="%s"></i><a href="%s">%s</a></div>',
            Convert::raw2att((string) $page->config()->get('cms_icon_class')),
            Convert::raw2att((string) $page->getCMSEditLink()),
            Convert::raw2xml((string) $page->Title)
        );
    }
}
