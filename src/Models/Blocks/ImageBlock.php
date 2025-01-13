<?php

namespace Toast\Blocks;

use SilverStripe\Assets\File;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\FieldType\DBHTMLText;

class ImageBlock extends Block
{
    private static $table_name = 'Blocks_ImageBlock';

    private static $singular_name = 'Image';

    private static $plural_name = 'Images';

    private static $has_one = [
        'Image' => File::class
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
                    ->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']),
            ]);

        });

        return parent::getCMSFields();

    }

    public function getContentSummary()
    {
        $content = '';

        if ($this->Image() && $this->Image()->exists()) {
            // If the image is not svg
            if ($this->Image()->getExtension() !== 'svg') {
                $content = $this->Image()->Fit(300, 150)->forTemplate();
            } else {
                $content = $this->Image()->forTemplate();
            }
        }
        return DBField::create_field(DBHTMLText::class, $content);
    }

    public function getCMSValidator()
    {
        $required = new RequiredFields([Image::class]);
        $this->extend('updateCMSValidator', $required);
        return $required;
    }
}
