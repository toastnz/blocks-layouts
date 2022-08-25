<?php

namespace Toast\Blocks;

use SilverStripe\Assets\Image;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Forms\DropdownField;

class ImageTextBlock extends Block
{
    private static $table_name = 'Blocks_ImageTextBlock';

    private static $singular_name = 'Image & Text';

    private static $plural_name = 'Image & Text';

    private static $db = [
        'Content' => 'HTMLText',
        'Alignment' => 'Enum("standard,reversed", "standard")'
    ];

    private static $has_one = [
        'Image' => Image::class
    ];

    private static $owns = [
        'Image'
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            $fields->addFieldsToTab('Root.Main', [
                UploadField::create('Image', 'Image')
                    ->setFolderName('Uploads/Blocks')
            ]);

        });

        return parent::getCMSFields();
    }

    public function getContentSummary()
    {
        return DBField::create_field(DBHTMLText::class, $this->Content)->Summary();
    }

    public function getCMSValidator()
    {
        $required = new RequiredFields([Image::class, 'Content']);
        $this->extend('updateCMSValidator', $required);
        return $required;

    }
}
