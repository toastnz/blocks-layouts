<?php

namespace Toast\Blocks\Items;

use SilverStripe\Assets\File;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
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
        'Image'  => File::class,
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
                UploadField::create('Image', 'Image')
                    ->setFolderName('Uploads/Blocks')
                    ->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']),
                TextField::create('Name', 'Name'),
                TextField::create('Position', 'Position'),
                TextareaField::create('Summary', 'Summary')
            ]);

        });

        return parent::getCMSFields();
    }
}
