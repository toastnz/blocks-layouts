<?php

namespace Toast\Blocks;

use SilverStripe\Forms\TextField;
use SilverStripe\Forms\DropdownField;
use Toast\Blocks\Items\PercentageBlockItem;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;

class PercentageBlock extends Block
{
    private static $table_name = 'Blocks_PercentageBlock';

    private static $singular_name = 'Percentage Block';

    private static $plural_name = 'Percentage Blocks';

    private static $has_many = [
        'Items' => PercentageBlockItem::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            $fields->removeByName('Items');
            // set default to always have limit
            $limit = true;
            $limitCount = 4;

            if ($this->ID) {
                $percentageConfig = GridFieldConfig_RecordEditor::create(10);

                $percentageConfig->addComponent(GridFieldOrderableRows::create('SortOrder'))
                    ->removeComponentsByType(GridFieldDeleteAction::class)
                    ->addComponent(new GridFieldDeleteAction(false))
                    ->removeComponentsByType('GridFieldAddExistingAutoCompleter');
                // allow to disable the limit and update limitCount
                $this->extend('updateLimitItems', $limit, $limitCount);
                // if limit is set to true, Limit to 4
                if ($limit && $this->Items()->Count() >= $limitCount) {
                    // remove the buttons if we don't want to allow more records to be added/created
                    $percentageConfig->removeComponentsByType(GridFieldAddNewButton::class);
                }
        
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