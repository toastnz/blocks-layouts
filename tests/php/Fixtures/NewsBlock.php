<?php

namespace Toast\Blocks\Tests\Fixtures;

use SilverStripe\Dev\TestOnly;
use Toast\Blocks\PostsBlock;

/**
 * Extends a module block under a different short name, so it doesn't replace it.
 */
class NewsBlock extends PostsBlock implements TestOnly
{
}
