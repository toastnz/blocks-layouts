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

class ChildrenBlock extends Block
{
    private static $table_name = 'Blocks_ChildrenBlock';
    private static $singular_name = 'Children Block';
    private static $plural_name = 'Children Blocks';

    private static $db = [
        'Content' => 'HTMLText',
        'Columns'  => 'Enum("2,3,4", "2")'
    ];

    private static $many_many = [
        'Children' => \Page::class
    ];

    private static $many_many_extraFields = [
        'Children' => [
            'SortOrder' => 'Int'
        ]
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $config = GridFieldConfig_RelationEditor::create(4);
        $config->removeComponentsByType(GridFieldAddNewButton::class)
         ->addComponent(GridFieldOrderableRows::create('SortOrder'));
         
        $fields->removeByName(['Children']);
        
        if ($this->exists()) {
            $grid = GridField::create('Children', 'Child Pages', $this->Children(), $config);

            $fields->addFieldsToTab('Root.Main', [
                DropdownField::create('Columns', 'Columns', $this->dbObject('Columns')->enumValues()),
                HTMLEditorField::create('Content', 'Content'),
                $grid
            ]);
        }else{
            $fields->addFieldToTab('Root.Main', LiteralField::create('Notice', '<div class="message notice">Save this block and more options will become available.</div>'));
        }

        return $fields;
    }

    public function getChildPages()
    {
       
        if (!$this->Children()->exists()){
            if( $this->ParentID ){
                return SiteTree::get()->filter(["ID" => $this->ParentID ]);
            }
        }
        
        return $this->Children();
    }
 
}


