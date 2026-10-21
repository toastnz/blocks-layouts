<?php

namespace Toast\Blocks;

use SilverStripe\Forms\FieldList;
use Toast\Blocks\Items\DownloadBlockItem;

class DownloadBlock extends Block
{
    private static $table_name = 'Blocks_DownloadBlock';

    private static $singular_name = 'Download';

    private static $plural_name = 'Downloads';

    protected static $icon_class = 'font-icon-block-attachment';

    private static $has_many = [
        'Items' => DownloadBlockItem::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $this->addItemsGridField($fields, 'Items', 'Files');
        });

        return parent::getCMSFields();
    }
}
