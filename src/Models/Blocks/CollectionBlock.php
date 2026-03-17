<?php

namespace Toast\Blocks;

use Toast\Blocks\Block;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\Forms\TreeDropdownField;

class CollectionBlock extends Block
{
    private static $singular_name = 'Collection Block';
    private static $plural_name = 'Collection Blocks';
    private static $description = 'Collection Block';
    private static $table_name = 'Blocks_CollectionBlock';

    protected static $icon_class = 'font-icon-block-tabs';

    private static $db = [
        'Type' => 'Varchar(10)',
        'Columns' => 'Varchar(10)',
    ];

    private static $has_one = [
        'TargetPage' => SiteTree::class,
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // Get the available columns
        $columns = $this->getAvailableColumns();

        $fields->addFieldsToTab('Root.Main', [
            TreeDropdownField::create('TargetPageID', 'Target Page', SiteTree::class)
                ->setDescription('Select the target page to get the relevant content from. If no target page is selected, the current page will be used.'),
            DropdownField::create('Columns', 'Columns', $columns),
            DropdownField::create('Type', 'Type', [
                'siblings' => 'Sibling Pages',
                'children' => 'Child Pages',
            ]),
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

    /** Get all relevant pages
     * @return ArrayList
     */
    public function getRelevantPages(): ArrayList
    {
        // Get the type of pages to return (siblings or children)
        $type = $this->Type ?: 'siblings';
        // Get the target page, or the page that the block is rendering on
        $page = $this->TargetPageID ? $this->TargetPage() : $this->getPage();
        // Get the parent page if there is one, otherwise use the current page
        $parent = $page->Parent() ?? $page;
        // If the type is children, return the children of the current page, otherwise return the children of the parent page excluding the current page (siblings)
        $pages = ($type === 'children') ? $page->Children() : $parent->Children()->exclude('ID', $page->ID);

        // Convert the DataList to an ArrayList
        $pages = ArrayList::create($pages->toArray());

        // Return the pages after allowing extensions to modify them
        return $this->updateRelevantPages($pages);
    }

    /** Allow extensions to modify the list of relevant pages
     * @param ArrayList $pages
     * @return ArrayList
     */
    public function updateRelevantPages($pages): ArrayList
    {
        // Allow extensions to modify the list of relevant pages
        $this->extend('updateRelevantPages', $pages);

        return $pages;
    }
}
