<?php

namespace Toast\Blocks\Items;

use SilverStripe\Forms\TabSet;
use Toast\Blocks\AccordionBlock;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Permission;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

class AccordionItem extends BlockItem
{
    private static $table_name = 'Blocks_AccordionItem';

    private static $singular_name = 'Item';

    private static $plural_name = 'Items';

    private static $default_sort = 'SortOrder';

    private static $db = [
        'Title' => 'Varchar(255)',
        'Content' => 'HTMLText',
    ];

    private static $has_one = [
        'Parent' => AccordionBlock::class
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'Content.Summary' => 'Content'
    ];


    public function DisplayTitle()
    {
        $title = strlen($this->Title) > 20 ? '<span>' . $this->Title . '</span>' : $this->Title;
        return DBField::create_field(DBHTMLText::class, $title);
    }

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            $fields->addFieldsToTab('Root.Main', [
                TextField::create('Title', 'Title'),
                HTMLEditorField::create('Content', 'Content')
            ]);

        });

        return parent::getCMSFields();
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if (!$this->SortOrder) {
            $max = (int)AccordionItem::get()->filter(['ParentID' => $this->ParentID])->max('SortOrder');
            $this->setField('SortOrder', $max + 1);
        }
    }

    public function getCMSValidator()
    {
        $required = new RequiredFields(['Heading', 'Content']);
        $this->extend('updateCMSValidator', $required);
        return $required;

    }

    public function GroupNumber()
    {
        return 'group' . ceil($this->SortOrder / 2);
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
