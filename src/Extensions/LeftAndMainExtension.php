<?php

namespace Toast\Blocks\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

class LeftAndMainExtension extends Extension
{

    public function onInit()
    {
        Requirements::css('toastnz/blocks-layouts: client/dist/styles/blocks.css');
        Requirements::javascript('toastnz/blocks-layouts: client/dist/scripts/blocks.js');
    }
}
