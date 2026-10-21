<?php

namespace Toast\Blocks\Extensions;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

/**
 * Loads the block layout picker and preview scripts in the CMS.
 *
 * @extends Extension<LeftAndMain>
 */
class LeftAndMainExtension extends Extension
{
    protected function onInit(): void
    {
        Requirements::css('toastnz/blocks-layouts: client/dist/styles/blocks.css');
        Requirements::javascript('toastnz/blocks-layouts: client/dist/scripts/blocks.js');
    }
}
