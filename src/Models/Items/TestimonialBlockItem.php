<?php

namespace Toast\Blocks\Items;

use Toast\Blocks\LinkBlock;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\TextField;
use Sheadawson\Linkable\Models\Link;
use SilverStripe\Forms\TextareaField;
use Sheadawson\Linkable\Forms\LinkField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use Toast\Blocks\TestimonialBlock;

class TestimonialBlockItem extends BlockItem
{
    private static $table_name = 'Blocks_TestimonialBlockItem';

    private static $db = [
        'Name'      => 'Varchar(255)',
        'Position'  => 'Varchar(255)',
        'Summary'   => 'Text'
    ];

    private static $has_one = [
        'Image'  => Image::class,
        'Parent' => TestimonialBlock::class
    ];

    private static $summary_fields = [
        'Name'      => 'Name',
        'Position'  => 'Position',
        'Summary'   => 'Summary'
    ];

    private static $searchable_fields = [
        'Name',
        'Position',
        'Summary'
    ];

    private static $owns = [
        'Image',
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            $fields->addFieldsToTab('Root.Main', [
                UploadField::create('Image', 'Thumbnail')
                    ->setFolderName('Uploads/Blocks'),
                TextField::create('Name', 'Name'),
                TextField::create('Position', 'Position'),
                TextareaField::create('Summary', 'Summary')
            ]);

        });

        return parent::getCMSFields();
    }
}
