<?php

namespace Toast\Blocks\Extensions;

use SilverStripe\Core\Extension;
use Toast\Blocks\PageContentBlock;

class PageContentBlockPageExtension extends Extension
{
    private $pageContentBlockLinked = false;

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
        // Avoid running multiple times
        if ($this->pageContentBlockLinked) return;

        $this->pageContentBlockLinked = true;

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

    public function onBeforeWrite()
    {
        $this->owner->addPageContentBlock();
    }
}
