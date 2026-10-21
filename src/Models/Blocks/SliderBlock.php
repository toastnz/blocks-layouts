<?php

namespace Toast\Blocks;

use Bummzack\SortableFile\Forms\SortableUploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;

class SliderBlock extends Block
{
    private static $table_name = 'Blocks_SliderBlock';

    private static $singular_name = 'Image Slider';

    private static $plural_name = 'Image Slider';

    protected static $icon_class = 'font-icon-block-carousel';

    private static $many_many = [
        'Images' => Image::class,
    ];

    private static $many_many_extraFields = [
        'Images' => [
            'Sort' => 'Int'
        ]
    ];

    private static $owns = [
        'Images'
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->addFieldToTab(
                'Root.Main',
                SortableUploadField::create('Images', 'Images')->setSortColumn('Sort')
            );
        });

        return parent::getCMSFields();
    }
}
