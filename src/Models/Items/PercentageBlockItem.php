<?php

namespace Toast\Blocks\Items;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\LinkField\Form\LinkField;
use SilverStripe\LinkField\Models\Link;
use Toast\Blocks\PercentageBlock;

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
        'Link' => Link::class,
        'Image' => File::class,
        'Parent' => PercentageBlock::class
    ];

    private static $summary_fields = [
        'Name' => 'Name',
        'Title' => 'Title',
    ];

    private static $owns = [
        'Image',
        'Link'
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName('Width');

            $fields->addFieldsToTab('Root.Main', [
                UploadField::create('Image', 'Image')
                    ->setFolderName('Uploads/Blocks')
                    ->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']),
                TextField::create('Name', 'Name')
                    ->setDescription('This is for internal use only and will not be displayed on the website'),
                TextField::create('Title', 'Title'),
                TextareaField::create('Summary', 'Summary'),
                LinkField::create('Link', 'Link'),
            ]);

            $widths = $this->getWidthOptions();

            if ($widths) {
                $fields->addFieldToTab('Root.Main', DropdownField::create('Width', 'Width %', $widths));
            }
        });

        return parent::getCMSFields();
    }

    /**
     * Width percentages that fit the parent block's column options, e.g. 2 columns gives 50, and 3 columns
     * gives 33 and 66. Columns come from PercentageBlock.available_columns (default 1–4).
     *
     * @return array<int, int> [percentage => percentage]
     */
    public function getWidthOptions(): array
    {
        $columns = PercentageBlock::config()->get('available_columns') ?? [1, 2, 3, 4];
        $options = [];

        foreach ((array) $columns as $column) {
            $column = (int) $column;

            if ($column <= 1) {
                $options[] = 100;
                continue;
            }

            $percentage = 100 / $column;

            for ($i = 1; $i < $column; $i++) {
                $options[] = (int) floor($percentage * $i);
                $options[] = (int) floor(100 - ($percentage * $i));
            }
        }

        $options = array_unique($options);
        sort($options);

        $options = $options ? array_combine($options, $options) : [];

        $this->extend('updateWidthOptions', $options);

        return $options;
    }
}
