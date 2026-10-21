<?php

namespace Toast\Blocks;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\FieldType\DBHTMLText;
use Toast\Blocks\Helpers\Helper;

class ImageBlock extends Block
{
    private static $table_name = 'Blocks_ImageBlock';

    private static $singular_name = 'Image';

    private static $plural_name = 'Images';

    protected static $icon_class = 'font-icon-block-file';

    private static $has_one = [
        'Image' => File::class
    ];

    private static $owns = [
        'Image'
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->addFieldToTab(
                'Root.Main',
                UploadField::create('Image', 'Image')
                    ->setFolderName('Uploads/Blocks')
                    ->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])
            );
        });

        return parent::getCMSFields();
    }

    /**
     * Thumbnail of the image for CMS lists.
     *
     * @return DBHTMLText
     */
    public function getContentSummary()
    {
        $summary = Helper::getImageThumbnailHTML($this->Image());

        $this->extend('updateContentSummary', $summary);

        return $summary;
    }
}
