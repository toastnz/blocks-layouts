<?php

namespace Toast\Blocks\Items;

use SilverStripe\Assets\File;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use Toast\Blocks\TabbedContentBlock;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Security\Permission;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

class ContentTabBlockItem extends BlockItem
{
    private static $table_name = 'Blocks_ContentTab';

    private static $singular_name = 'Tab';

    private static $plural_name = 'Tabs';

    private static $default_sort = 'SortOrder';

    private static $db = [
        'Title' => 'Varchar(255)',
        'Content' => 'HTMLText',
        'SortOrder' => 'Int'
    ];

    private static $has_one = [
        'Parent' => TabbedContentBlock::class
    ];

    private static $summary_fields = [
        'Title' => 'Heading',
        'ContentSummary' => 'Content'
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            $fields->addFieldsToTab('Root.Main', [
                TextField::create('Title', 'Title'),
                HTMLEditorField::create('Content', 'Content')
                    ->setRows(15)
            ]);

        });

        return parent::getCMSFields();
    }

    public function getContentSummary()
    {
        return $this->dbObject('Content')->LimitCharacters(100);
    }

    public function canView($member = null)
    {
        if ($this->Parent()) {
            return $this->Parent()->canView($member);
        }
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canEdit($member = null)
    {
        if ($this->Parent()) {
            return $this->Parent()->canEdit($member);
        }
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canDelete($member = null)
    {
        if ($this->Parent()) {
            return $this->Parent()->canDelete($member);
        }
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canCreate($member = null, $context = [])
    {
        if ($this->Parent()) {
            return $this->Parent()->canCreate($member, $context);
        }
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }
}