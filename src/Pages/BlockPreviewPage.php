<?php

namespace Toast\Pages;

use Page;
use Toast\Blocks\Block;
use SilverStripe\Security\Security;
use SilverStripe\View\Requirements;
use SilverStripe\CMS\Model\SiteTree;

class BlockPreviewPage extends Page
{
    private static $singular_name = 'Block Preview Page';

    private static $plural_name = 'Block Preview Pages';

    private static $description = 'A page to render a preview of a block while editing';

    private static $table_name = 'BlockPreviewPage';

    // Hide this page type from the CMS site tree
    private static $show_in_sitetree = false;
}

class BlockPreviewPageController extends \PageController
{
    private static $allowed_actions = [
        'preview'
    ];

    public function preview()
    {
        // Check if the user is logged in
        if (!Security::getCurrentUser()) {
            return 'You must be logged in to preview this block';
        }

        $block = $this->getRequest()->getVar('block');
        $block = Block::get()->byID($block);


        if (!$block) {
            return 'Block not found';
        }

        Requirements::css($block->getCSSFile());

        return $block->forTemplate();
    }
}
