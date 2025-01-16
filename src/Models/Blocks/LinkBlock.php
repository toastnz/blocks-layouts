<?php

namespace Toast\Blocks;

use Toast\Blocks\Block;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\DropdownField;
use Toast\Blocks\Items\LinkBlockItem;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Versioned\VersionedGridFieldItemRequest;
use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;

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

            if ($this->exists()) {
                // Check to see if there are any columns available in the config
                if ($columns = Config::inst()->get(LinkBlock::class, 'available_columns')) {
                    // Make the column an array of of key => value pairs using the value as the key and the value as the value
                    $columns = array_combine($columns, $columns);

                    // Add the dropdown field to the main tab
                    $fields->addFieldsToTab('Root.Main', [
                        DropdownField::create('Columns', 'Columns', $columns),
                    ]);
                }

                $config = GridFieldConfig_RelationEditor::create(50);
                $config->removeComponentsByType(GridFieldAddNewButton::class)
                    ->removeComponentsByType(GridFieldFilterHeader::class)
                    ->removeComponentsByType(GridField_ActionMenu::class);

                $config->getComponentByType(GridFieldDetailForm::class)
                    ->setItemRequestClass(VersionedGridFieldItemRequest::class)
                    ->setItemEditFormCallback(function ($form, $itemRequest) {
                        if (!$itemRequest->record->exists()) {
                            $nextSortOrder = $this->Items()->max('SortOrder') + 1;
                            $form->Fields()->add(HiddenField::create('ManyMany[SortOrder]', 'Sort Order', $nextSortOrder));
                        }
                    });

                $multiClass = new GridFieldAddNewMultiClass();

                $multiClass->setClasses(Config::inst()->get(LinkBlock::class, 'available_items'));

                $config->addComponent($multiClass);

                $config->addComponent(new GridFieldOrderableRows('SortOrder'));

                $gridField = GridField::create(
                    'Items',
                    'Items',
                    $this->owner->Items(),
                    $config
                );

                $fields->addFieldToTab('Root.Main', $gridField);
            }
        });

        return parent::getCMSFields();
    }
}
