<?php

namespace Toast\Blocks;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\Model\List\ArrayList;
use Toast\Blocks\Traits\HasColumns;

class ChildrenBlock extends Block
{
    use HasColumns;

    private static $table_name = 'Blocks_ChildrenBlock';

    private static $singular_name = 'Page Links';

    private static $plural_name = 'Page Links';

    protected static $icon_class = 'font-icon-block-layout-2';

    private static $db = [
        'Columns' => 'Varchar(10)',
        'ExcludeMenuHiddenItems' => 'Boolean',
    ];

    private static $has_one = [
        'ParentPage' => SiteTree::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName('ParentPageID');
            $this->addColumnsField($fields);

            $fields->addFieldsToTab('Root.Main', [
                CheckboxField::create('ExcludeMenuHiddenItems', 'Exclude child pages that are hidden from menus (Show in menus: false)'),
                TreeDropdownField::create('ParentPageID', 'Parent Page', SiteTree::class)
                    ->setDescription('Select the parent page to get the children from. If no parent is selected, the current page will be used.'),
            ]);
        });

        return parent::getCMSFields();
    }

    /**
     * The page whose children are listed: the selected parent page, otherwise the current page.
     */
    public function getChildrenParentPage(): ?SiteTree
    {
        $selectedParent = $this->ParentPage();

        if ($selectedParent && $selectedParent->exists()) {
            return $selectedParent;
        }

        $page = $this->getPage();

        return $page && $page->exists() ? $page : null;
    }

    /**
     * Child pages to display. Extensions can change the list with the `updateItems` hook.
     *
     * @return ArrayList
     */
    public function getItems()
    {
        $items = ArrayList::create();
        $parent = $this->getChildrenParentPage();

        if (!$parent) {
            return $items;
        }

        $children = $this->ExcludeMenuHiddenItems ? $parent->Children() : $parent->AllChildren();
        $items->merge($children);

        $this->extend('updateItems', $items, $parent);

        return $items->removeDuplicates();
    }
}
