<?php

namespace Toast\Blocks\Items;

use SilverStripe\Assets\File;
use Toast\Blocks\DownloadBlock;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Security\Permission;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\AssetAdmin\Forms\UploadField;

class DownloadBlockItem extends BlockItem
{
    private static $table_name  = 'Blocks_DownloadBlockItem';

    private static $singular_name = 'Download File Item';

    private static $plural_name = 'Download File Items';

    private static $default_sort  = 'SortOrder';

    private static $db = [
        'Title' => 'Varchar(255)',
        'Summary' => 'Text',
    ];

    private static $has_one = [
        'Parent' => DownloadBlock::class,
        'File' => File::class
    ];

    private static $owns = [
        'File'
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'File.CMSThumbnail' => 'File'
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            $fields->addFieldsToTab('Root.Main', [
                TextField::create('Title', 'Title'),
                TextareaField::create('Summary', 'Summary'),
                UploadField::create('File', 'File')
                    ->setFolderName('Uploads/Blocks/Files')
            ]);

        });

        return parent::getCMSFields();
    }

    public function getCMSValidator()
    {
        $required = new RequiredFields([
            'File'
        ]);
        $this->extend('updateCMSValidator', $required);
        return $required;

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
