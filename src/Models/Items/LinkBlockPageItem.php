<?php

namespace Toast\Blocks\Items;

use Sheadawson\Linkable\Models\Link;
use SilverStripe\CMS\Model\SiteTree;
use Toast\Blocks\Items\LinkBlockItem;
use SilverStripe\Forms\TreeDropdownField;

class LinkBlockPageItem extends LinkBlockItem
{
    private static $table_name = 'Blocks_LinkBlockPageItem';

    private static $singular_name = 'Page Link';

    private static $plural_name = 'Page Links';

    private static $has_one = [
        'Page' => SiteTree::class
    ];

    private static $summary_fields = [
        'Title' => 'Title',
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            $fields->addFieldsToTab('Root.Main', [
                TreeDropdownField::create('PageID', 'Page', SiteTree::class),
            ]);
        });

        return parent::getCMSFields();
    }

    public function getTitle()
    {
        if ($page = $this->Page()) {
            if ($page->exists() && $title = $page->Title) {
                return $page->Title;
            }
        }

        return null;
    }

    public function getSummary()
    {
        if ($page = $this->Page()) {
            if ($page->exists() && $summary = $page->PageSummary()) {
                return $summary;
            }
        }

        return null;
    }

    public function getLink()
    {
        if ($page = $this->Page()) {
            if ($page->exists()) {
                $link = Link::create();
                $link->Title = $page->Title;
                $link->Type = 'SiteTree';
                $link->URL = $page->Link();
                $link->write();

                return $link;
            }
        }

        return null;
    }

    public function getImage()
    {
        if ($page = $this->Page()) {
            if ($page->exists() && $image = $page->FeaturedImage()) {
                return $image;
            }
        }

        return null;
    }
}
