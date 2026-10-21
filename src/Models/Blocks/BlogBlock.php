<?php

namespace Toast\Blocks;

use SilverStripe\Blog\Model\Blog;
use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\Model\List\SS_List;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Toast\Blocks\Traits\HasColumns;

class BlogBlock extends Block
{
    use HasColumns;

    private static $table_name = 'Blocks_BlogBlock';

    private static $singular_name = 'Blog Posts';

    private static $plural_name = 'Blog Posts';

    protected static $icon_class = 'font-icon-block-blog-post';

    private static $db = [
        'Columns' => 'Varchar(10)',
    ];

    private static $has_one = [
        'Blog' => Blog::class
    ];

    private static $many_many = [
        'BlogPosts' => BlogPost::class
    ];

    private static $many_many_extraFields = [
        'BlogPosts' => [
            'SortOrder' => 'Int'
        ]
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName(['BlogPosts', 'BlogID']);
            $this->addColumnsField($fields);

            if (!$this->exists()) {
                $fields->addFieldToTab('Root.Main', $this->getSaveFirstNoticeField());
                return;
            }

            $config = GridFieldConfig_RelationEditor::create(4)
                ->removeComponentsByType(GridFieldAddNewButton::class)
                ->addComponent(GridFieldOrderableRows::create('SortOrder'));

            $fields->addFieldsToTab('Root.Main', [
                LiteralField::create('Notice', '<div class="message notice">Select a blog page to automatically pull through the latest blog posts. Alternatively, select the specific posts you wish to display.</div>'),
                TreeDropdownField::create('BlogID', 'Parent Page', SiteTree::class)
                    ->setFilterFunction(fn ($node) => $node instanceof Blog),
                GridField::create('BlogPosts', 'Blog Posts', $this->BlogPosts(), $config),
            ]);
        });

        return parent::getCMSFields();
    }

    /**
     * The hand-picked posts, or the latest posts from the selected blog, excluding the current page.
     *
     * @param int|string|null $limit Defaults to the number of columns
     * @return SS_List
     */
    public function getPosts($limit = null)
    {
        $limit = is_numeric($limit) ? (int) $limit : (int) $this->Columns;
        $excludeID = (int) $this->getPage()?->ID;

        if (!$this->BlogPosts()->exists() && $this->BlogID) {
            $posts = BlogPost::get()->filter('ParentID', $this->BlogID);
        } else {
            $posts = $this->BlogPosts()->sort('SortOrder');
        }

        // A limit of 0 means "no results" in Silverstripe 6, so treat it as "no limit"
        $posts = $posts->exclude('ID', $excludeID)->limit($limit > 0 ? $limit : null);

        $this->extend('updatePosts', $posts);

        return $posts;
    }
}
