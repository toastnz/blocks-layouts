<?php

namespace Toast\Blocks;

use SilverStripe\Assets\File;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\RequiredFields;
use Axllent\FormFields\FieldType\VideoLink;
use Axllent\FormFields\Forms\VideoLinkField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\LinkField\Models\Link;
use SilverStripe\LinkField\Form\LinkField;

class MediaTextBlock extends Block
{
    private static $table_name = 'Blocks_MediaTextBlock';

    private static $singular_name = 'Media & Text';

    private static $plural_name = 'Media & Text';

    protected static $icon_class = 'font-icon-block-promo-3';

    private static $db = [
        'Video' => VideoLink::class,
        'MediaAlignment'    => 'Enum("left,right", "left")',
    ];

    private static $has_one = [
        'Image' => File::class,
        'CTALink' => Link::class
    ];

    private static $owns = [
        'Image',
        'CTALink',
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            $fields->addFieldsToTab('Root.Main', [
                DropdownField::create('MediaAlignment', 'Media Position', $this->owner->dbObject('MediaAlignment')->enumValues()),
                VideoLinkField::create('Video')
                    ->showPreview(500)
                    ->setDescription('This will replace the image on this block'),
                UploadField::create('Image', 'Image')
                    ->setFolderName('Uploads/Blocks')
                    ->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']),
                LinkField::create('CTALink', 'Link'),
            ]);

        });

        return parent::getCMSFields();
    }

    public function getCMSValidator()
    {
        $required = new RequiredFields([Image::class, 'Content']);
        $this->extend('updateCMSValidator', $required);
        return $required;

    }
}
