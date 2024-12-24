<?php

namespace Toast\Blocks;

use SilverStripe\Assets\Image;
use Sheadawson\Linkable\Models\Link;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\FieldType\DBField;
use Sheadawson\Linkable\Forms\LinkField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use Axllent\FormFields\FieldType\VideoLink;
use Axllent\FormFields\Forms\VideoLinkField;
use SilverStripe\AssetAdmin\Forms\UploadField;

class MediaTextBlock extends Block
{
    private static $table_name = 'Blocks_MediaTextBlock';

    private static $singular_name = 'Media & Text';

    private static $plural_name = 'Media & Text';

    private static $db = [
        'Video' => VideoLink::class,
        'MediaAlignment'    => 'Enum("left,right", "left")',
    ];

    private static $has_one = [
        'Image' => Image::class,
        'CTALink' => Link::class
    ];

    private static $owns = [
        'Image'
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
                    ->setFolderName('Uploads/Blocks'),
                LinkField::create('CTALinkID', 'Link'),
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
