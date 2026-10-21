<?php

namespace Toast\Blocks\Extensions;

use Page;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Extension;
use SilverStripe\View\SSViewer;
use Toast\Blocks\PageContentBlock;

/**
 * Lets a page type position its own Layout template among its content blocks, using the shared
 * {@link PageContentBlock}. The block is linked to the page automatically on save.
 *
 * ```yaml
 * Your\Page\Class:
 *   extensions:
 *     - Toast\Blocks\Extensions\PageContentBlockPageExtension
 * ```
 *
 * @extends Extension<Page>
 */
class PageContentBlockPageExtension extends Extension
{
    private static $pageContentBlockLinked = true;

    /**
     * The shared PageContentBlock, created and published if it doesn't exist yet.
     */
    public function getOrCreatePageContentBlock(): PageContentBlock
    {
        $block = PageContentBlock::get()->first();

        if (!$block) {
            $block = PageContentBlock::create();
            $block->Title = 'Page Content Block';
            $block->write();
            $block->publishSingle();
        }

        return $block;
    }

    public function getPageContentBlock(): PageContentBlock
    {
        return $this->getOrCreatePageContentBlock();
    }

    /**
     * Link the shared PageContentBlock to this page if it isn't already.
     */
    public function addPageContentBlock(): void
    {
        try {
            $block = $this->getOrCreatePageContentBlock();
            $blocks = $this->getOwner()->ContentBlocks();

            if (!$blocks->filter('ID', $block->ID)->exists()) {
                $blocks->add($block);
            }
        } catch (\Exception $e) {
            // Never block a page save because the content block couldn't be linked
            return;
        }
    }

    public function getHasPageContentBlock(): bool
    {
        $owner = $this->getOwner();

        if (!$owner->config()->get('pageContentBlockLinked') || !$owner->isInDB()) {
            return false;
        }

        return $owner->ContentBlocks()->filter('ClassName', PageContentBlock::class)->exists();
    }

    protected function onBeforeWrite(): void
    {
        // Called through the owner so page classes can override it
        $this->getOwner()->addPageContentBlock();
    }

    public function getViewerTemplate(): array
    {
        return SSViewer::get_templates_by_class($this->getOwner()->ClassName);
    }

    /**
     * Namespace of the page class, e.g. "Toast\Pages"
     */
    public function getNamespace(): string
    {
        $className = get_class($this->getOwner());
        $separator = strrpos($className, '\\');

        return $separator === false ? '' : substr($className, 0, $separator);
    }

    /**
     * Short class name of the page, e.g. "GeneralHolderPage"
     */
    public function getShortName(): string
    {
        return ClassInfo::shortName($this->getOwner());
    }
}
