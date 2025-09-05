<?php

namespace Toast\Blocks\Extensions;

use SilverStripe\Core\Extension;
use Toast\Blocks\PageContentBlock;

class PageContentBlockPageExtension extends Extension
{
    private static $pageContentBlockLinked = true;

    public function getOrCreatePageContentBlock()
    {
        // Find the first existing block, or create one if none exist
        $block = PageContentBlock::get()->first();

        if (!$block) {
            $block = PageContentBlock::create();
            $block->Title = 'Page Content Block';
            $block->write();
        }

        $block->publishSingle();

        return $block;
    }

    public function addPageContentBlock()
    {

        try {
            // Grab the page content block
            $block = $this->getOrCreatePageContentBlock();

            // Grab the content blocks
            $blocks = $this->owner->ContentBlocks();

            // Link the block if not already linked
            if (!$blocks->filter('ID', $block->ID)->exists()) {
                $blocks->add($block);
            }
        } catch (\Exception $e) {
            return;
        }
    }

    public function getHasPageContentBlock()
    {
        if (self::$pageContentBlockLinked) return true;

        return false;
    }


    public function onBeforeWrite()
    {
        // Check if this is a new record (not yet in database before write)
        // if (!$this->owner->isInDB()) {
        $this->owner->addPageContentBlock();

        // }
    }


    public function getViewerTemplate()
    {
        return SSViewer::get_templates_by_class($this->owner->ClassName);
    }

    public function getNamespace()
    {
        // Build layout templates in a namespace-aware way
        $reflection = new \ReflectionClass($this->owner);
        return  $reflection->getNamespaceName(); // e.g. Toast\Pages

    }

    public function getShortName()
    {
        $reflection = new \ReflectionClass($this->owner);
        return  $reflection->getShortName(); // e.g. GeneralHolderPage
    }
}
