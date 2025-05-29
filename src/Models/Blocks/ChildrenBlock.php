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
    protected static $icon_class = 'font-icon-block-layout-2';

    private static $db = [
        'Columns' => 'Varchar(10)',
    ];

    private static $has_one = [
        'ParentPage' => SiteTree::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {
            $fields->removeByName(['ParentPageID', 'Columns']);

            if ($this->exists()) {
                // Set up the options array
                $options = [];

                // Check to see if there are any columns available in the config
                $columns = Config::inst()->get(ChildrenBlock::class, 'available_columns') ?? [2, 3, 4];

                foreach ($columns as $column) {
                    $options[$column] = $column;
                }

                // Add the dropdown field to the main tab
                if (count($options) > 0) {
                    $fields->addFieldsToTab('Root.Main', [
                        DropdownField::create('Columns', 'Columns', $options),
                    ]);
                }

                $fields->addFieldsToTab('Root.Main', [
                    TreeDropdownField::create('ParentPageID', 'Parent Page', SiteTree::class)
                        ->setDescription('Select the parent page to get the children from. If no parent is selected, the current page will be used.')
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

        $parent = null;
        $selectedParent = $this->ParentPage();
        $page = $this->getPage();

        if ($selectedParent && $selectedParent->exists()) {
            // If a parent page is selected, use that
            $parent = $selectedParent;
        } elseif ($page && $page->exists()) {
            // Otherwise, use the current page as the parent
            $parent = $page;
        } else {
            // If no parent page is selected and the current page doesn't exist, return empty items
            return $items;
        }

        if ($children = $parent->Children()) {
            foreach ($children as $child) {
                $items->push($child);
            }
        }

        return $items;
    }
}
