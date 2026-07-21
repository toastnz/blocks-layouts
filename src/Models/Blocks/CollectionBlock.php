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
        'RelatedPage' => SiteTree::class,
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {
            // Get the available columns
            $columns = $this->getAvailableColumns();

            $fields->addFieldsToTab('Root.Main', [
                TreeDropdownField::create('RelatedPageID', 'Display pages related to:', SiteTree::class)
                    ->setDescription('Select the target page to get the relevant content from. If no page is selected, the current page will be used.'),
                DropdownField::create('Type', 'Collection of pages to display', [
                    'siblings' => 'Sibling Pages',
                    'children' => 'Child Pages',
                    'same'     => 'Same Type'
                ])->setDescription('if "Same Type" is selected, all pages of the same page type will be displayed'),
            ]);
        });
        
        return parent::getCMSFields();
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
        // Get the type of pages to return (siblings, children or same)
        $type = $this->Type ?: 'siblings';
        // Get the target page, or the page that the block is rendering on
        $page = $this->RelatedPageID ? $this->RelatedPage() : $this->getPage();
        // Get the parent page if there is one, otherwise use the current page
        $parent = $page->Parent() ?? $page;
        // Get the relevant pages based on the type: children of the current page, all pages of the same page type as the current page, or the children of the parent page excluding the current page (siblings)
        $pages = match ($type) {
            'children' => $page->Children(),
            'same'     => SiteTree::get()->filter('ClassName', $page->ClassName),
            default    => $parent->Children()->exclude('ID', $page->ID),
        };

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
