<?php

namespace Toast\Blocks;

use SilverStripe\Assets\File;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use Axllent\FormFields\FieldType\VideoLink;
use Axllent\FormFields\Forms\VideoLinkField;
use SilverStripe\AssetAdmin\Forms\UploadField;

class VideoBlock extends Block
{
    private static $table_name = 'Blocks_VideoBlock';

    private static $singular_name = 'Video';

    private static $plural_name = 'Videos';

    protected static $icon_class = 'font-icon-block-video';

    private static $db = [
        'OpenInModal' => 'Boolean',
        'Video' => VideoLink::class,
    ];

    private static $has_one = [
        'Thumbnail' => File::class
    ];

    private static $owns = [
        'Thumbnail'
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {
            $fields->addFieldsToTab('Root.Main', [
                CheckboxField::create('OpenInModal', 'Open Video In Modal'),
                VideoLinkField::create('Video')
                ->showPreview(500),
                UploadField::create('Thumbnail', 'Thumbnail Image')
                    ->setFolderName('Uploads/Blocks')
                    ->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']),
            ]);
        });

        return parent::getCMSFields();
    }

    public function getCMSValidator()
    {
        $required = new RequiredFields(['Title', 'Video']);

        $this->extend('updateCMSValidator', $required);

        return $required;
    }
}
