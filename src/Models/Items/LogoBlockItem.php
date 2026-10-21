<?php

namespace Toast\Blocks\Items;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use Toast\Blocks\Helpers\Helper;
use Toast\Blocks\LogoBlock;

class LogoBlockItem extends BlockItem
{
    private static $table_name = 'Blocks_LogoBlockItem';

    private static $db = [
        'Title' => 'Varchar(255)',
        'BrandLink' => 'Varchar(255)',
    ];

    private static $has_one = [
        'Image' => File::class,
        'Parent' => LogoBlock::class
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'ContentSummary' => 'Logo',
    ];

    private static $owns = [
        'Image',
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->addFieldsToTab('Root.Main', [
                TextField::create('Title', 'Title'),
                TextField::create('BrandLink', 'Brand Link'),
                UploadField::create('Image', 'Image')
                    ->setFolderName('Uploads/Blocks')
                    ->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']),
            ]);
        });

        return parent::getCMSFields();
    }

    /**
     * Thumbnail of the logo for CMS lists.
     *
     * @return DBHTMLText
     */
    public function getContentSummary()
    {
        return Helper::getImageThumbnailHTML($this->Image());
    }
}
