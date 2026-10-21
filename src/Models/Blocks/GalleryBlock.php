<?php

namespace Toast\Blocks;

use SilverStripe\Forms\FieldList;
use Toast\Blocks\Items\GalleryBlockItem;
use Toast\Blocks\Traits\HasColumns;

class GalleryBlock extends Block
{
    use HasColumns;

    private static $table_name = 'Blocks_GalleryBlock';

    private static $singular_name = 'Media Gallery';

    private static $plural_name = 'Media Gallery';

    protected static $icon_class = 'font-icon-block-carousel';

    private static $db = [
        'Columns' => 'Varchar(10)',
    ];

    private static $has_many = [
        'Items' => GalleryBlockItem::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $this->addColumnsField($fields);
            $this->addItemsGridField($fields, 'Items', 'Items', null);
        });

        return parent::getCMSFields();
    }
}
