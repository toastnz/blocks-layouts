<?php

namespace Toast\Blocks;

use SilverStripe\Forms\FieldList;
use Toast\Blocks\Items\LinkBlockItem;
use Toast\Blocks\Traits\HasColumns;

class LinkBlock extends Block
{
    use HasColumns;

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
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $this->addColumnsField($fields);
            $this->addItemsGridField($fields, 'Items', 'Link Block Items', 10);
        });

        return parent::getCMSFields();
    }
}
