<?php

namespace Toast\Blocks;

use Toast\Blocks\Block;
use SilverStripe\Blog\Model\Blog;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\Forms\DropdownField;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;

class BlogBlock extends Block
{
    private static $table_name = 'Blocks_BlogBlock';
    private static $singular_name = 'Blog Block';
    private static $plural_name = 'Blog Blocks';

    private static $db = [
        'Columns'  => 'Enum("2,3,4", "2")'
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
        $this->beforeUpdateCMSFields(function ($fields) {

            $config = GridFieldConfig_RelationEditor::create(4);
            $config->removeComponentsByType(GridFieldAddNewButton::class)
            ->addComponent(GridFieldOrderableRows::create('SortOrder'));

            $fields->removeByName(['BlogPosts', 'Columns', 'BlogID']);

            if ($this->exists()) {
                // Check to see if there are any columns available in the config
                if ($columns = Config::inst()->get(BlogBlock::class, 'available_columns')) {
                    // Make the column an array of of key => value pairs using the value as the key and the value as the value
                    $columns = array_combine($columns, $columns);

                    // Add the dropdown field to the main tab
                    $fields->addFieldsToTab('Root.Main', [
                        DropdownField::create('Columns', 'Columns', $columns),
                    ]);
                }

                $grid = GridField::create('BlogPosts', 'Blog Posts', $this->BlogPosts(), $config);

                $fields->addFieldsToTab('Root.Main', [
                    LiteralField::create('Notice', '<div class="message notice">Latest blog posts will be displayed if no blog posts are linked, Blog will need to be selected.</div>'),
                    $grid
                ]);
            }else{
                $fields->addFieldToTab('Root.Main', LiteralField::create('Notice', '<div class="message notice">Save this block and more options will become available.</div>'));
            }
        });

        return parent::getCMSFields();
    }

    public function getPosts($limit = 3)
    {

        if (!$this->BlogPosts()->exists()){
            if( $this->BlogID ){
                return BlogPost::get()->filter(["ParentID" => $this->BlogID ])->limit($limit);
            }
        }

        return $this->BlogPosts()->sort('SortOrder')->limit($limit);
    }

}
