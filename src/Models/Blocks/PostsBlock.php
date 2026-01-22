<?php

namespace Toast\Blocks;

use Toast\Blocks\Block;
use SilverStripe\ORM\DataList;
use SilverStripe\Blog\Model\Blog;
use SilverStripe\Blog\Model\BlogTag;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\ListboxField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Model\List\ArrayList;

class PostsBlock extends Block
{
    private static $singular_name = 'Posts Block';
    private static $plural_name = 'Posts Blocks';
    private static $description = 'Posts Block';
    private static $table_name = 'Blocks_PostsBlock';
    private static $target_blog_classname = 'SilverStripe\Blog\Model\Blog';

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
        $fields = parent::getCMSFields();

        // Get the relevant tags
        $tags = $this->getRelevantTags();

        // Get the min and max limit values
        $limit = $this->getLimitMinMax();
        $minPosts = $limit['min_posts'];
        $maxPosts = $limit['max_posts'];

        // Get the available columns
        $columns = $this->getAvailableColumns();

        $fields->addFieldsToTab('Root.Main', [
            ListboxField::create('Tags', 'Filter by Tags', $tags)
                ->setDescription('Select tags to filter the posts shown in this block. If no tags are selected, all posts will be shown.'),
            NumericField::create('Limit', 'Number of posts to show')
                ->setAttribute('min', $minPosts)
                ->setAttribute('max', $maxPosts)
                ->setAttribute('step', '1'),
            DropdownField::create('Columns', 'Columns', $columns),
        ]);

        return $fields;
    }

    /** Get available columns from config or default values
     * @return array
     */
    public function getAvailableColumns(): array
    {
        // Check to see if there are any columns available in the config
        $columns = Config::inst()->get(self::class, 'available_columns') ?? [2, 3, 4];
        // Return the available columns after allowing extensions to modify them
        return $this->updateAvailableColumns($columns);
    }

    /** Allow extensions to modify available columns
     * @param array $columns
     * @return array
     */
    public function updateAvailableColumns($columns): array
    {
        // Allow extensions to modify the available columns
        $this->extend('updateAvailableColumns', $columns);

        return array_combine($columns, $columns);
    }

    /** Get min and max limit values from config or defaults
     * @return array
     */
    public function getLimitMinMax(): array
    {
        // Get min and max from config, or use defaults
        $min = Config::inst()->get(self::class, 'min_posts') ?? 4;
        $max = Config::inst()->get(self::class, 'max_posts') ?? 12;

        // Return the min and max after allowing extensions to modify them
        return $this->updateLimitMinMax($min, $max);
    }

    /** Allow extensions to modify min and max limit values
     * @param int $min
     * @param int $max
     * @return array
     */
    public function updateLimitMinMax($min, $max): array
    {
        // Allow extensions to modify the min and max values
        $this->extend('updateLimitMinMax', $min, $max);

        return [
            'min_posts' => $min,
            'max_posts' => $max,
        ];
    }

    /** Get all the tags from the relevant blog class
     * @return array
     */
    public function getRelevantTags(): array
    {
        // Get all blog tags
        $tags = BlogTag::get();

        // Filter to only return tags related to the correct blog class name
        $tags = $tags->filter('Blog.ClassName', $this->Config()->get('target_blog_classname'));

        // Map to ID => Title array
        $tags = $tags->map('ID', 'Title')->toArray();

        // Allow extensions to modify the list of tags
        return $this->updateRelevantTags($tags);
    }

    /** Allow extensions to modify relevant tags
     * @param array $tags
     * @return array
     */
    public function updateRelevantTags($tags): array
    {
        // Allow extensions to modify the list of tags
        $this->extend('updateRelevantTags', $tags);

        return $tags;
    }

    /** Get all blog pages
     * @return DataList
     */
    public function getBlogPages(): DataList
    {
        $pages = Blog::get();

        // Filter to only return pages with the correct parent page class name
        $pages = $pages->filter('ClassName', $this->Config()->get('target_blog_classname'));

        // Return the pages after allowing extensions to modify them
        return $this->updateBlogPages($pages);
    }

    /** Allow extensions to modify the list of blog pages
     * @param DataList $pages
     * @return DataList
     */
    public function updateBlogPages($pages): DataList
    {
        // Allow extensions to modify the list of blog pages
        $this->extend('updateBlogPages', $pages);

        return $pages;
    }

    /** Get posts from the relevant blog pages
     * @param string $sort
     * @return ArrayList
     */
    public function getPosts($sort = 'PublishDate DESC'): ArrayList
    {
        $items = new ArrayList();

        // Get all the posts from all the blog pages
        foreach ($this->getBlogPages() as $blogPage) {
            foreach ($blogPage->getBlogPosts() as $post) {
                $items->push($post);
            }
        }

        // Allow extensions to modify the list of items
        $items = $this->updatePosts($items);

        // Sort the items
        $items = $items->sort($sort);

        // Filter by tags if any are selected
        if ($this->Tags()->exists()) {
            $tagIDs = $this->Tags()->column('ID');
            $items = $items->filterByCallback(function ($item) use ($tagIDs) {
                $postTagIDs = $item->Tags()->column('ID');
                return count(array_intersect($tagIDs, $postTagIDs)) > 0;
            });
        }

        // Limit the number of items if a limit is set
        $items = $items->limit($this->Limit ?: 4);

        return $items;
    }

    /** Allow extensions to modify the list of posts
     * @param ArrayList $items
     * @return ArrayList
     */
    public function updatePosts($items): ArrayList
    {
        // Allow extensions to modify the list of posts
        $this->extend('updatePosts', $items);

        return $items;
    }
}
