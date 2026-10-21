<?php

namespace Toast\Blocks\Items;

use SilverStripe\Core\Convert;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use Toast\Blocks\AccordionBlock;

class AccordionItem extends BlockItem
{
    private static $table_name = 'Blocks_AccordionItem';

    private static $singular_name = 'Item';

    private static $plural_name = 'Items';

    private static $default_sort = 'SortOrder';

    private static $db = [
        'Title' => 'Varchar(255)',
        'Content' => 'HTMLText',
    ];

    private static $has_one = [
        'Parent' => AccordionBlock::class
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'Content.Summary' => 'Content'
    ];

    /**
     * The title, wrapped in a span when it is long (over 20 characters) so it can be styled differently.
     *
     * @return DBHTMLText
     */
    public function DisplayTitle()
    {
        $title = Convert::raw2xml((string) $this->Title);

        if (strlen((string) $this->Title) > 20) {
            $title = '<span>' . $title . '</span>';
        }

        return DBField::create_field(DBHTMLText::class, $title);
    }

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->addFieldsToTab('Root.Main', [
                TextField::create('Title', 'Title'),
                HTMLEditorField::create('Content', 'Content')
            ]);
        });

        return parent::getCMSFields();
    }
}
