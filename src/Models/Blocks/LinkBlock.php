<?php

namespace Toast\Blocks;

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

    // TODO: update this so it is a number value that gets it's values from a dropdown which is populated by a yml file so it is easy to change from project to project
    private static $db = [
        'Columns' => 'Enum("2, 3, 4", "2")'
    ];

    // TODO: update this so it includes a link block item, but each link block item has more items that extend it like LinkBlockPageItem for page only links, and LinkBlockCustomItem for custom links
    // TODO: update the LinkBlockItem to just have the necessary relationships and fields that every link block item needs, and then have the LinkBlockPageItem and LinkBlockCustomItem extend it and add the extra fields they need
    private static $has_many = [
        'Items' => LinkBlockItem::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            $fields->removeByName('Items');

            // TODO: make this dropdown get it's values from a yml file so it is easy to change from project to project
            $fields->addFieldsToTab('Root.Main', [
                DropdownField::create('Columns', 'Number of columns', singleton('Toast\Blocks\LinkBlock')->dbObject('Columns')->enumValues()),
            ]);

            if ($this->ID) {
                // TODO: update this to a multi class grid field so it can have different types of link block items, also update it to the inline grid field so it is faster to add items without having to go to a new page
                $linkConfig = GridFieldConfig_RelationEditor::create(10);
                $linkConfig->addComponent(GridFieldOrderableRows::create('SortOrder'))
                    ->removeComponentsByType(GridFieldDeleteAction::class)
                    ->addComponent(new GridFieldDeleteAction(false))
                    ->removeComponentsByType('GridFieldAddExistingAutoCompleter');

                $linkBlockGridField = GridField::create(
                    'Items',
                    'Link Block Items',
                    $this->owner->Items(),
                    $linkConfig
                );
                $fields->addFieldToTab('Root.Main', $linkBlockGridField);
            }

        });

        return parent::getCMSFields();
    }
}
