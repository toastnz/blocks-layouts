<?php

namespace Toast\Blocks;

use Toast\Blocks\Block;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;


class HeroBlock extends Block
{
    private static $table_name = 'Blocks_HeroBlock';

    private static $singular_name = 'Hero block';

    private static $plural_name = 'Hero blocks';

    private static $db = [
        'Content' => 'HTMLText',
        'FullWidth' => 'Boolean'
    ];

    private static $has_one = [
        'BackgroundImage' => Image::class
    ];

    private static $owns = [
        'BackgroundImage'
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            $fields->addFieldsToTab('Root.Main', [
                HTMLEditorField::create('Content', 'Content')->setRows(5),
                CheckboxField::create('FullWidth', 'Extend content to use full width'),
                UploadField::create('BackgroundImage', 'Background Image')
                    ->setFolderName('Uploads/Blocks')
            ]);

        });

        return parent::getCMSFields();
    }

}
