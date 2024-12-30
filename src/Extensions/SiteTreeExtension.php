<?php

namespace Toast\Blocks\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\Core\Config\Config;

class SiteTreeExtension extends Extension
{
    public function updateAnchorsOnPage(&$anchors)
    {
        // Get custom anchors from the configuration
        $customAnchors = Config::inst()->get(__CLASS__, 'custom_anchors');
        if (is_array($customAnchors)) {
            $anchors = array_merge($customAnchors, $anchors);
        }

        // Get the ContentBlocks for the current page
        $contentBlocks = $this->owner->ContentBlocks();

        // Loop through each ContentBlock and add its BlockID to the anchors array
        foreach ($contentBlocks as $block) {
            $anchors[] = $block->BlockID;
        }

        // Remove duplicates and reindex the array
        $anchors = array_values(array_unique($anchors));
    }
}
