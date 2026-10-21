<?php

namespace Toast\Blocks;

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\Blog\Model\BlogTag;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ListboxField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\DataList;
use Toast\Blocks\Traits\HasColumns;

class PostsBlock extends Block
{
    use HasColumns;

    private static $singular_name = 'Posts Block';

    private static $plural_name = 'Posts Blocks';

    private static $description = 'Posts Block';

    private static $table_name = 'Blocks_PostsBlock';

    private static $target_blog_classname = Blog::class;

    protected static $icon_class = 'font-icon-block-blog-post';

    private static $db = [
        'Limit' => 'Int',
        'Columns' => 'Varchar(10)',
    ];

    private static $many_many = [
        'Tags' => BlogTag::class,
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $limit = $this->getLimitMinMax();

            // Remove the scaffolded Tags tab/GridField; tags are picked from a listbox instead
            $fields->removeByName('Tags');

            $fields->addFieldsToTab('Root.Main', [
                ListboxField::create('Tags', 'Filter by Tags', $this->getRelevantTags())
                    ->setDescription('Select tags to filter the posts shown in this block. If no tags are selected, all posts will be shown.'),
                NumericField::create('Limit', 'Number of posts to show')
                    ->setAttribute('min', $limit['min_posts'])
                    ->setAttribute('max', $limit['max_posts'])
                    ->setAttribute('step', '1'),
            ]);

            $this->addColumnsField($fields);
        });

        return parent::getCMSFields();
    }

    /**
     * Min and max limit values from config or defaults
     */
    public function getLimitMinMax(): array
    {
        return $this->updateLimitMinMax(
            $this->config()->get('min_posts') ?? 4,
            $this->config()->get('max_posts') ?? 12
        );
    }

    /**
     * Allow extensions to modify min and max limit values
     */
    public function updateLimitMinMax($min, $max): array
    {
        $this->extend('updateLimitMinMax', $min, $max);

        return [
            'min_posts' => $min,
            'max_posts' => $max,
        ];
    }

    /**
     * Tags belonging to the target blog class, as [ID => Title]
     */
    public function getRelevantTags(): array
    {
        $tags = BlogTag::get()
            ->filter('Blog.ClassName', $this->config()->get('target_blog_classname'))
            ->map('ID', 'Title')
            ->toArray();

        return $this->updateRelevantTags($tags);
    }

    /**
     * Allow extensions to modify relevant tags
     */
    public function updateRelevantTags($tags): array
    {
        $this->extend('updateRelevantTags', $tags);

        return $tags;
    }

    /**
     * All blog pages of the target blog class
     */
    public function getBlogPages(): DataList
    {
        $pages = Blog::get()->filter('ClassName', $this->config()->get('target_blog_classname'));

        return $this->updateBlogPages($pages);
    }

    /**
     * Allow extensions to modify the list of blog pages
     */
    public function updateBlogPages($pages): DataList
    {
        $this->extend('updateBlogPages', $pages);

        return $pages;
    }

    /**
     * Posts from the relevant blog pages, filtered by the selected tags and limited to Limit (default 4).
     */
    public function getPosts($sort = 'PublishDate DESC'): ArrayList
    {
        $items = ArrayList::create();

        foreach ($this->getBlogPages() as $blogPage) {
            $items->merge($blogPage->getBlogPosts());
        }

        $items = $this->updatePosts($items)->sort($sort);

        $tagIDs = $this->Tags()->column('ID');

        if ($tagIDs) {
            // One query for every tagged post, rather than one per post
            $taggedPostIDs = BlogPost::get()->filter('Tags.ID', $tagIDs)->column('ID');
            $items = $items->filterByCallback(fn ($item) => in_array($item->ID, $taggedPostIDs));
        }

        return $items->limit($this->Limit ?: 4);
    }

    /**
     * Allow extensions to modify the list of posts
     */
    public function updatePosts($items): ArrayList
    {
        $this->extend('updatePosts', $items);

        return $items;
    }
}
