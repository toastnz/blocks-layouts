<?php

namespace Toast\Blocks;

use Toast\Blocks\Block;
use Toast\Blocks\LinkBlock;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\DropdownField;
use Toast\Blocks\Items\LogoBlockItem;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;

class LogoBlock extends Block
{
    private static $singular_name = 'Logo Block';

    private static $plural_name = 'Logo Blocks';

    private static $description = 'Logo Block';

    private static $table_name = 'LogoBlock';

    protected static $icon_class = 'font-icon-block-custom';

    private static $db = [
        'Columns' => 'Varchar(10)',
    ];

    private static $has_many = [
        'Items' => LogoBlockItem::class
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // Remove the default Items tab
        $fields->removeByName('Items');

        // Set up the options array
        $options = [];

        // Check to see if there are any columns available in the config
        $columns = Config::inst()->get(LogoBlock::class, 'available_columns') ?? [2, 3, 4];

        foreach ($columns as $column) {
            $options[$column] = $column;
        }

        // Add the dropdown field to the main tab
        if (count($options) > 0) {
            $fields->addFieldsToTab('Root.Main', [
                DropdownField::create('Columns', 'Columns', $options),
            ]);
        }

        if ($this->ID) {
            $percentageConfig = GridFieldConfig_RecordEditor::create(10);

            $percentageConfig->addComponent(GridFieldOrderableRows::create('SortOrder'))
                ->removeComponentsByType(GridFieldDeleteAction::class)
                ->addComponent(new GridFieldDeleteAction(false))
                ->removeComponentsByType('GridFieldAddExistingAutoCompleter');

            $percentageBlockGridField = GridField::create(
                'Items',
                'Percentage Block Items',
                $this->owner->Items(),
                $percentageConfig
            );
            $fields->addFieldToTab('Root.Main', $percentageBlockGridField);
        }

        return $fields;
    }
}
