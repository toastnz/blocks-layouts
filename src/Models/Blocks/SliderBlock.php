<?php

namespace Toast\Blocks;

use Toast\Blocks\Block;
use SilverStripe\Assets\Image;
use Bummzack\SortableFile\Forms\SortableUploadField;

class SliderBlock extends Block
{
    private static $table_name = 'Blocks_SliderBlock';

    private static $singular_name = 'Media Slider';

    private static $plural_name = 'Media Slider';

    protected static $icon_class = 'font-icon-block-carousel';

    private static $many_many = [
        'Images' => Image::class,
    ];

    private static $owns = [
        'Images'
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {
            $fields->addFieldsToTab('Root.Main', [
                SortableUploadField::create('Images', 'Images')
            ]);
        });

        return parent::getCMSFields();
    }

}
