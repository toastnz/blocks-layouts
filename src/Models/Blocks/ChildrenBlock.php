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
        'ChildPages' => \Page::class
    ];

    private static $many_many_extraFields = [
        'ChildPages' => [
            'Sort' => 'Int'
        ]
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            $config = GridFieldConfig_RelationEditor::create(4);
            $config->removeComponentsByType(GridFieldAddNewButton::class)
            ->addComponent(GridFieldOrderableRows::create('Sort'));
            
            $fields->removeByName(['Children']);
            
            if ($this->exists()) {
                $grid = GridField::create('ChildPages', 'Child Pages', $this->ChildPages(), $config);

                $fields->addFieldsToTab('Root.Main', [
                    DropdownField::create('Columns', 'Columns', $this->dbObject('Columns')->enumValues()),
                    HTMLEditorField::create('Content', 'Content'),
                    $grid
                ]);
            }else{
                $fields->addFieldToTab('Root.Main', LiteralField::create('Notice', '<div class="message notice">Save this block and more options will become available.</div>'));
            }
        });
        return parent::getCMSFields();
    }

    public function getItems()
    {
      
        if ($this->ChildPages()->count() == 0 ){
            if( $parentPage = $this->getParentPage()){
                // get all the pages under this parent page
                return SiteTree::get()->filter(["ParentID" => $parentPage->ID ]);
            }
        }
        
        return $this->ChildPages();
    }
 
}


