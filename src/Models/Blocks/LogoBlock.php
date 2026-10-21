<?php

namespace Toast\Blocks;

use SilverStripe\Forms\FieldList;
use Toast\Blocks\Items\LogoBlockItem;
use Toast\Blocks\Traits\HasColumns;

class LogoBlock extends Block
{
    use HasColumns;

    private static $singular_name = 'Logo Block';

    private static $plural_name = 'Logo Blocks';

    private static $description = 'Logo Block';

    // Note: no "Blocks_" prefix. Kept as-is so existing data stays in place.
    private static $table_name = 'LogoBlock';

    protected static $icon_class = 'font-icon-block-custom';

    private static $db = [
        'Columns' => 'Varchar(10)',
    ];

    private static $has_many = [
        'Items' => LogoBlockItem::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $this->addColumnsField($fields);
            $this->addItemsGridField($fields, 'Items', 'Logo Items', 10);
        });

        return parent::getCMSFields();
    }
}
