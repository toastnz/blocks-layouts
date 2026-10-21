<?php

namespace Toast\Blocks;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\Model\List\ArrayList;
use Toast\Blocks\Traits\HasColumns;

class CollectionBlock extends Block
{
    use HasColumns;

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
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->addFieldsToTab('Root.Main', [
                TreeDropdownField::create('RelatedPageID', 'Display pages related to:', SiteTree::class)
                    ->setDescription('Select the target page to get the relevant content from. If no page is selected, the current page will be used.'),
                DropdownField::create('Type', 'Collection of pages to display', [
                    'siblings' => 'Sibling Pages',
                    'children' => 'Child Pages',
                    'same'     => 'Same Type'
                ])->setDescription('if "Same Type" is selected, all pages of the same page type will be displayed'),
            ]);

            $this->addColumnsField($fields);
        });

        return parent::getCMSFields();
    }

    /**
     * The page the collection is relative to: the selected page, otherwise the current page.
     */
    public function getCollectionTargetPage(): ?SiteTree
    {
        $page = $this->RelatedPageID ? $this->RelatedPage() : $this->getPage();

        return $page && $page->exists() ? $page : null;
    }

    /**
     * Sibling pages, child pages or pages of the same type, depending on the chosen Type.
     */
    public function getRelevantPages(): ArrayList
    {
        $page = $this->getCollectionTargetPage();

        if (!$page) {
            return $this->updateRelevantPages(ArrayList::create());
        }

        $pages = match ($this->Type ?: 'siblings') {
            'children' => $page->Children(),
            'same'     => SiteTree::get()->filter('ClassName', $page->ClassName),
            // Top-level pages have an empty Parent(), whose Children() are the other top-level pages
            default    => $page->Parent()->Children()->exclude('ID', $page->ID),
        };

        return $this->updateRelevantPages(ArrayList::create($pages->toArray()));
    }

    /**
     * Allow extensions to modify the list of relevant pages
     */
    public function updateRelevantPages($pages): ArrayList
    {
        $this->extend('updateRelevantPages', $pages);

        return $pages;
    }
}
