<?php

namespace Toast\Blocks\Extensions;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Extension;

/**
 * Adds every block's anchor (plus any `custom_anchors` from config) to the anchors offered by the CMS
 * link picker.
 *
 * ```yaml
 * Toast\Blocks\Extensions\SiteTreeExtension:
 *   custom_anchors:
 *     - top
 * ```
 *
 * @extends Extension<SiteTree>
 */
class SiteTreeExtension extends Extension
{
    protected function updateAnchorsOnPage(array &$anchors): void
    {
        $owner = $this->getOwner();
        $customAnchors = $owner->config()->get('custom_anchors');

        if (is_array($customAnchors)) {
            $anchors = array_merge($customAnchors, $anchors);
        }

        if ($owner->hasMethod('ContentBlocks')) {
            foreach ($owner->ContentBlocks() as $block) {
                $anchors[] = $block->getBlockID();
            }
        }

        $anchors = array_values(array_unique($anchors));
    }
}
