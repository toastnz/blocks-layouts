<?php

namespace Toast\Blocks;

use SilverStripe\ORM\ArrayList;
use SilverStripe\Core\Config\Config;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TreeDropdownField;
use Toast\Blocks\Block;

class ChildrenBlock extends Block
{
    private static $table_name = 'Blocks_ChildrenBlock';
    private static $singular_name = 'Children Block';
    private static $plural_name = 'Children Blocks';

    private static $db = [
        'Columns' => 'Varchar(10)',
    ];

    private static $has_one = [
        'ParentPage' => SiteTree::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {
            if ($this->exists()) {
                // Check to see if there are any columns available in the config
                if ($columns = Config::inst()->get(ChildrenBlock::class, 'available_columns')) {
                    // Make the column an array of of key => value pairs using the value as the key and the value as the value
                    $columns = array_combine($columns, $columns);

                    // Add the dropdown field to the main tab
                    $fields->addFieldsToTab('Root.Main', [
                        DropdownField::create('Columns', 'Columns', $columns),
                    ]);
                }

                $fields->addFieldsToTab('Root.Main', [
                    TreeDropdownField::create('ParentPageID', 'Parent Page', SiteTree::class),
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

        return $items;
    }
}
