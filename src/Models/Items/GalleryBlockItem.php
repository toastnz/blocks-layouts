<?php

namespace Toast\Blocks\Items;

use Toast\Blocks\GalleryBlock;
use SilverStripe\Assets\File;
use Axllent\FormFields\FieldType\VideoLink;
use Axllent\FormFields\Forms\VideoLinkField;
use SilverStripe\AssetAdmin\Forms\UploadField;

class GalleryBlockItem extends BlockItem
{
    private static $table_name = 'GalleryBlockItem';

    private static $db = [
        'Video'         => VideoLink::class,
    ];

    private static $has_one = [
        'Image'  => File::class,
        'Parent' => GalleryBlock::class
    ];

    private static $owns = [
        'Image'
    ];

    private static $summary_fields = [
        'Image.CMSThumbnail' => 'Image'
    ];
    private static $default_sort = 'SortOrder ASC';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main',
        [
            UploadField::create('Image', 'Image')
                ->setFolderName('Uploads/Blocks')
                ->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']),
            VideoLinkField::create('Video', 'Video')
                ->showPreview('100%')
        ]);

        return $fields;
    }


}
