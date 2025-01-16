<?php

namespace Toast\Blocks;

use Toast\Blocks\Block;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\DropdownField;
use Toast\Blocks\Items\GalleryBlockItem;
use SilverStripe\Forms\GridField\GridField;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;

class GalleryBlock extends Block
{
    private static $table_name = 'Blocks_GalleryBlock';

    private static $singular_name = 'Media Gallery';

    private static $plural_name = 'Media Gallery';

    private static $db = [
        'Columns' => 'Varchar(10)',
    ];

    private static $has_many = [
        'Items'    => GalleryBlockItem::class
    ];

    public function getCMSFields()
    {

        $this->beforeUpdateCMSFields(function ($fields) {
            $fields->removeByName('Items');

            if ($this->exists()) {
                $config = GridFieldConfig_RecordEditor::create();
                $config->addComponent(GridFieldOrderableRows::create('SortOrder'));
                $grid = GridField::create('Items', 'Items', $this->Items(), $config);

                // Check to see if there are any columns available in the config
                if ($columns = Config::inst()->get(GalleryBlock::class, 'available_columns')) {
                    // Make the column an array of of key => value pairs using the value as the key and the value as the value
                    $columns = array_combine($columns, $columns);

                    // Add the dropdown field to the main tab
                    $fields->addFieldsToTab('Root.Main', [
                        DropdownField::create('Columns', 'Columns', $columns),
                    ]);
                }

                $fields->addFieldToTab('Root.Main', $grid);
            } else {
                $fields->addFieldToTab('Root.Main', LiteralField::create('Notice', '<div class="message notice">Save this block and more options will become available.</div>'));
            }
        });
        return parent::getCMSFields();
    }
}
