<?php

namespace Toast\Blocks;

use SilverStripe\ORM\ArrayList;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use Toast\Blocks\Block;

class ChildrenBlock extends Block
{
    private static $table_name = 'Blocks_ChildrenBlock';
    private static $singular_name = 'Children Block';
    private static $plural_name = 'Children Blocks';

    private static $db = [
        'Content' => 'HTMLText',
        'Columns'  => 'Enum("2,3,4", "2")'
    ];

    private static $has_one = [
        'ParentPage' => SiteTree::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {
            if ($this->exists()) {

                $fields->addFieldsToTab('Root.Main', [
                    TreeDropdownField::create('ParentPageID', 'Parent Page', SiteTree::class),
                    DropdownField::create('Columns', 'Columns', $this->dbObject('Columns')->enumValues()),
                    HTMLEditorField::create('Content', 'Content'),
                ]);
            } else {
                $fields->addFieldToTab('Root.Main', LiteralField::create('Notice', '<div class="message notice">Save this block and more options will become available.</div>'));
            }
        });
        return parent::getCMSFields();
    }

    public function getItems()
    {
        $items = new ArrayList();

        if ($parent = $this->ParentPage()) {
            if ($parent->exists()) {
                if ($children = $parent->Children()) {
                    foreach ($children as $child) {
                        $items->push($child);
                    }
                }
            }
        }

        var_dump($items->count());

        return $items;
    }
}
