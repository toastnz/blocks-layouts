<?php

namespace Toast\Blocks;

use Toast\Blocks\Block;
use SilverStripe\ORM\GroupedList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\DropdownField;
use Toast\Blocks\Items\AccordionItem;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;

class AccordionBlock extends Block
{
    private static $table_name = 'Blocks_AccordionBlock';

    private static $singular_name = 'Accordion';

    private static $plural_name = 'Accordions';

    private static $db = [
        'AccordionDisplay' => 'Enum("all-closed, all-open, first-open", "all-closed")',
    ];

    private static $has_many = [
        'Items' => AccordionItem::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            $fields->removeByName('Items');
            $fields->removeByName('AccordionDisplay');

            $array = [
                'all-closed' => 'All Closed',
                'all-open' => 'All Open',
                'first-open' => 'First Open'
            ];

            $fields->insertAfter('Content', DropdownField::create('AccordionDisplay', 'Accordion display', $array));

            if ($this->ID) {
                $config = GridFieldConfig_RelationEditor::create(50)
                    ->removeComponentsByType(GridFieldAddExistingAutoCompleter::class)
                    ->removeComponentsByType(GridFieldDeleteAction::class)
                    ->addComponents(new GridFieldDeleteAction())
                    ->addComponents(GridFieldOrderableRows::create('SortOrder'));

                $grid = GridField::create('Items', 'Accordion Items', $this->Items(), $config);

                $fields->addFieldToTab('Root.Main', $grid);
            } else {
                $fields->insertAfter('AccordionDisplay', LiteralField::create('', '<div class="message notice">Save this block to show additional options.</div>'));
            }
        });

        return parent::getCMSFields();
    }

    public function getGroupedItems()
    {
        return GroupedList::create($this->Items());
    }
}
