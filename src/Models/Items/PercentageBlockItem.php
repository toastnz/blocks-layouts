<?php

namespace Toast\Blocks\Items;

use SilverStripe\Assets\File;
use Toast\Blocks\PercentageBlock;
use SilverStripe\Forms\TextField;
use SilverStripe\Core\Config\Config;
use Sheadawson\Linkable\Models\Link;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\DropdownField;
use Sheadawson\Linkable\Forms\LinkField;
use SilverStripe\AssetAdmin\Forms\UploadField;

class PercentageBlockItem extends BlockItem
{
    private static $table_name = 'Blocks_PercentageBlockItem';

    private static $db = [
        'Name' => 'Varchar(255)',
        'Title' => 'Varchar(255)',
        'Summary' => 'Text',
        'Width' => 'Varchar(10)',
    ];

    private static $has_one = [
        'Link'   => Link::class,
        'Image'  => File::class,
        'Parent' => PercentageBlock::class
    ];

    private static $summary_fields = [
        'Name' => 'Name',
        'Title' => 'Title',
    ];
    private static $owns = [
        'Image',
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            $fields->removeByName(['Width']);

            // Set up the options array
            $options = [];

            // Check to see if there are any columns available in the config
            $columns = Config::inst()->get(PercentageBlock::class, 'available_columns') ?? [1, 2, 3, 4];

            // Loop through the available columns to output the options as percentages
            foreach ($columns as $column) {
                if ($column == 1) {
                    $options[] = '100';
                } else {
                    $percentage = 100 / $column;
                    for ($i = 1; $i < $column; $i++) {
                        $options[] = floor($percentage * $i);
                        $options[] = floor(100 - ($percentage * $i));
                    }
                }
            }

            // Remove duplicates and sort options
            $options = array_unique($options);
            sort($options);

            $fields->addFieldsToTab('Root.Main', [
                UploadField::create('Image', 'Image')
                    ->setFolderName('Uploads/Blocks')
                    ->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']),
                TextField::create('Name', 'Name')
                    ->setDescription('This is for internal use only and will not be displayed on the website'),
                TextField::create('Title', 'Title'),
                TextareaField::create('Summary', 'Summary'),
                // DropdownField::create('Width', 'Width', $this->dbObject('Width')->enumValues())->setEmptyString('--- Please select ---'),
                LinkField::create('LinkID', 'Link'),
            ]);

            // Add the dropdown field to the main tab
            if (count($options) > 0) {
                $fields->addFieldsToTab('Root.Main', [
                    DropdownField::create('Width', 'Width %', array_combine($options, $options)),
                ]);
            }
        });

        return parent::getCMSFields();
    }

    public function canCreate($member = null, $context = [])
    {
        return parent::canCreate($member);
    }
}
