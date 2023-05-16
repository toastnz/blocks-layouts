<?php

namespace Toast\Blocks;

use Sheadawson\Linkable\Forms\LinkField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Blog\Model\Blog;
use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\SiteConfig\SiteConfig;
use Toast\Helpers\Helper;

class BlogBlock extends Block
{
    private static $table_name = 'Blocks_BlogBlock';
    private static $singular_name = 'Blog Block';
    private static $plural_name = 'Blog Blocks';

    private static $db = [
        'Content' => 'HTMLText',
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
        $fields = parent::getCMSFields();

        $config = GridFieldConfig_RelationEditor::create(4);
        $config->removeComponentsByType(GridFieldAddNewButton::class)
         ->addComponent(GridFieldOrderableRows::create('SortOrder'));
         
        $fields->removeByName(['BlogPosts']);
        
        if ($this->exists()) {

            $siteConfig = SiteConfig::current_site_config();

            $grid = GridField::create('BlogPosts', 'Blog Posts', $this->BlogPosts(), $config);

            $fields->addFieldsToTab('Root.Main', [
                DropdownField::create('Columns', 'Columns', $this->dbObject('Columns')->enumValues()),
                DropdownField::create('BlogID', 'Blog', Blog::get()->map('ID', 'Title'))->setEmptyString('--Please select a blog--'),
                LiteralField::create('Notice', '<div class="message notice">Latest blog posts will be displayed if no blog posts are linked, Blog will need to be selected.</div>'),
                HTMLEditorField::create('Content', 'Content'),
                $grid
            ]);
            
        }else{
            $fields->addFieldToTab('Root.Main', LiteralField::create('Notice', '<div class="message notice">Save this block and more options will become available.</div>'));
        }

        return $fields;
    }

    public function getPosts($limit = 3)
    {
       
        if (!$this->BlogPosts()->exists()){
            if( $this->BlogID ){
                return BlogPost::get()->filter(["ParentID" => $this->BlogID ])->limit($limit);
            }
        }
        
        return $this->BlogPosts()->limit($limit);
    }
 
}


