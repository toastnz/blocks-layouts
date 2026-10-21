<?php

namespace Toast\Blocks;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Model\List\GroupedList;
use Toast\Blocks\Items\AccordionItem;

class AccordionBlock extends Block
{
    private static $table_name = 'Blocks_AccordionBlock';

    private static $singular_name = 'Accordion';

    private static $plural_name = 'Accordions';

    protected static $icon_class = 'font-icon-block-accordion';

    private static $db = [
        'AccordionDisplay' => 'Enum("all-closed, all-open, first-open", "all-closed")',
    ];

    private static $has_many = [
        'Items' => AccordionItem::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName('AccordionDisplay');

            $fields->insertAfter('Content', DropdownField::create('AccordionDisplay', 'Accordion display', [
                'all-closed' => 'All Closed',
                'all-open' => 'All Open',
                'first-open' => 'First Open',
            ]));

            $this->addItemsGridField($fields, 'Items', 'Accordion Items');
        });

        return parent::getCMSFields();
    }

    /**
     * @return GroupedList
     */
    public function getGroupedItems()
    {
        return GroupedList::create($this->Items());
    }
}
