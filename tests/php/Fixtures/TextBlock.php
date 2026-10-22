<?php

namespace Toast\Blocks\Tests\Fixtures;

use SilverStripe\Dev\TestOnly;
use Toast\Blocks\TextBlock as ModuleTextBlock;

/**
 * Overrides the module's TextBlock under the same short name.
 */
class TextBlock extends ModuleTextBlock implements TestOnly
{
    private static $singular_name = 'Text Block (Custom)';
}
