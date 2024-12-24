<?php

namespace Toast\Blocks;

use Axllent\FormFields\FieldType\VideoLink;
use Axllent\FormFields\Forms\VideoLinkField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;

class VideoBlock extends Block
{
    private static $table_name = 'Blocks_VideoBlock';

    private static $singular_name = 'Video';

    private static $plural_name = 'Videos';

    private static $db = [
        'OpenInModal' => 'Boolean',
        'Video' => VideoLink::class,
    ];

    private static $has_one = [
        'Thumbnail' => Image::class
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
                UploadField::create('Thumbnail', 'Override default thumbnail')
                ->setFolderName('Uploads/Blocks')
                ->setDescription('Will automatically use YouTube / Vimeo thumbnail if this image is not uploaded. Ideal size: 960x540'),
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

    public function getContentSummary()
    {
        return DBField::create_field(DBHTMLText::class, $this->Video);
    }
}
