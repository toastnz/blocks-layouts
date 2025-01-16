<?php

namespace Toast\Blocks;

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\DropdownField;
use Toast\Blocks\Items\LinkBlockItem;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;

class LinkBlock extends Block
{
    private static $table_name = 'Blocks_LinkBlock';

    private static $singular_name = 'Link';

    private static $plural_name = 'Links';

    private static $db = [
        'Columns' => 'Varchar(10)',
    ];

    private static $has_many = [
        'Items' => LinkBlockItem::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            $fields->removeByName(['Items', 'Columns']);

            if ($this->ID) {
                // Check to see if there are any columns available in the config
                if ($columns = Config::inst()->get(LinkBlock::class, 'available_columns')) {
                    // Make the column an array of of key => value pairs using the value as the key and the value as the value
                    $columns = array_combine($columns, $columns);

                    // Add the dropdown field to the main tab
                    $fields->addFieldsToTab('Root.Main', [
                        DropdownField::create('Columns', 'Columns', $columns),
                    ]);
                }

                $linkConfig = GridFieldConfig_RelationEditor::create(10);
                $linkConfig->addComponent(GridFieldOrderableRows::create('SortOrder'))
                    ->removeComponentsByType(GridFieldDeleteAction::class)
                    ->addComponent(new GridFieldDeleteAction(false))
                    ->removeComponentsByType('GridFieldAddExistingAutoCompleter');

                $linkBlockGridField = GridField::create(
                    'Items',
                    'Link Block Items',
                    $this->Items(),
                    $linkConfig
                );
                $fields->addFieldToTab('Root.Main', $linkBlockGridField);
            }

        });

        return parent::getCMSFields();
    }
}
