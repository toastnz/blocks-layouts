<?php

namespace Toast\Blocks;

use SilverStripe\Forms\TextField;
use Toast\Blocks\Items\PercentageBlockItem;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;

class PercentageBlock extends Block
{
    private static $table_name = 'Blocks_PercentageBlock';

    private static $singular_name = 'Percentage Block';

    private static $plural_name = 'Percentage Blocks';

    private static $db = [
        'Heading' => 'Varchar(255)',
        'Content' => 'HTMLText',
    ];

    private static $has_many = [
        'Items' => PercentageBlockItem::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            $fields->removeByName('Items');

            $fields->addFieldsToTab('Root.Main', [
                TextField::create('Heading', 'Heading'),
                HTMLEditorField::create('Content', 'Content'),
            ]);

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

        });

        return parent::getCMSFields();
    }


}
