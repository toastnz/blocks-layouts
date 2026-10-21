<?php

namespace Toast\Blocks;

use SilverStripe\Forms\FieldList;
use Toast\Blocks\Items\PercentageBlockItem;

class PercentageBlock extends Block
{
    private static $table_name = 'Blocks_PercentageBlock';

    private static $singular_name = 'Percentage Block';

    private static $plural_name = 'Percentage Blocks';

    protected static $icon_class = 'font-icon-block-layout-5';

    private static $has_many = [
        'Items' => PercentageBlockItem::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $this->addItemsGridField($fields, 'Items', 'Percentage Block Items', 10);
        });

        return parent::getCMSFields();
    }
}
