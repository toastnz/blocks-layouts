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

    protected static $icon_class = 'font-icon-block-link';

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
                // Set up the options array
                $options = [];

                // Check to see if there are any columns available in the config
                $columns = Config::inst()->get(LinkBlock::class, 'available_columns') ?? [2, 3, 4];

                foreach ($columns as $column) {
                    $options[$column] = $column;
                }

                // Add the dropdown field to the main tab
                if (count($options) > 0) {
                    $fields->addFieldsToTab('Root.Main', [
                        DropdownField::create('Columns', 'Columns', $options),
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
