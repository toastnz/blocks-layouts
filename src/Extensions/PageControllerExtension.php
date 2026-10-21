<?php

namespace Toast\Blocks\Extensions;

use PageController;
use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * Optional: renders a single block of the current page over AJAX, e.g. /about-us/contentblock/12.
 *
 * Not applied by default. To use it, apply it to PageController and allow the action:
 *
 * ```yaml
 * PageController:
 *   extensions:
 *     - Toast\Blocks\Extensions\PageControllerExtension
 *   allowed_actions:
 *     - contentblock
 * ```
 *
 * @extends Extension<PageController>
 */
class PageControllerExtension extends Extension
{
    /**
     * The rendered block, or a redirect back to the page for non-AJAX requests or unknown blocks.
     */
    public function contentblock(): string|DBHTMLText|HTTPResponse
    {
        $owner = $this->getOwner();

        if (Director::is_ajax()) {
            $blockID = (int) $owner->getRequest()->param('ID');
            $page = $owner->data();

            // Only blocks that are actually on this page can be rendered
            $block = $page && $page->hasMethod('ContentBlocks') ? $page->ContentBlocks()->byID($blockID) : null;

            if ($block && $block->exists()) {
                return $block->forTemplate();
            }
        }

        return $owner->redirect($owner->Link());
    }
}
