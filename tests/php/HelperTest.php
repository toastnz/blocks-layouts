<?php

namespace Toast\Blocks\Tests;

use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use Toast\Blocks\Extensions\PageExtension;
use Toast\Blocks\Helpers\Helper;
use Toast\Blocks\ImageBlock;
use Toast\Blocks\PostsBlock;
use Toast\Blocks\TextBlock;
use Toast\Blocks\Tests\Fixtures\NewsBlock;
use Toast\Blocks\Tests\Fixtures\TextBlock as CustomTextBlock;

class HelperTest extends SapphireTest
{
    protected $usesDatabase = false;

    /**
     * @param string[] $classes
     */
    private function setAvailableBlocks(array $classes): void
    {
        Config::modify()->set(PageExtension::class, 'available_blocks', $classes);
    }

    public function testPlainListKeepsOrderAndRemovesDuplicates(): void
    {
        $this->setAvailableBlocks([ImageBlock::class, TextBlock::class, ImageBlock::class, TextBlock::class]);

        $this->assertSame([ImageBlock::class, TextBlock::class], Helper::getAvailableBlockClasses());
    }

    public function testParentIsHiddenWhenSameNamedSubclassIsListed(): void
    {
        $this->setAvailableBlocks([TextBlock::class, ImageBlock::class, CustomTextBlock::class]);

        $this->assertSame([ImageBlock::class, CustomTextBlock::class], Helper::getAvailableBlockClasses());
    }

    public function testParentIsKeptWhenSubclassIsNotListed(): void
    {
        $this->setAvailableBlocks([TextBlock::class]);

        $this->assertSame([TextBlock::class], Helper::getAvailableBlockClasses());
    }

    public function testParentIsKeptWhenSubclassHasADifferentShortName(): void
    {
        $this->setAvailableBlocks([PostsBlock::class, NewsBlock::class]);

        $this->assertSame([PostsBlock::class, NewsBlock::class], Helper::getAvailableBlockClasses());
    }

    public function testMissingClassesStayInTheList(): void
    {
        $this->setAvailableBlocks(['App\\Blocks\\DoesNotExist', TextBlock::class]);

        $this->assertSame(['App\\Blocks\\DoesNotExist', TextBlock::class], Helper::getAvailableBlockClasses());
    }

    public function testEmptyConfigReturnsEmptyList(): void
    {
        $this->setAvailableBlocks([]);

        $this->assertSame([], Helper::getAvailableBlockClasses());
    }
}
