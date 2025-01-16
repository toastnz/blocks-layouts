<?php

namespace Toast\Blocks\Items;

use Toast\Blocks\LinkBlock;
use SilverStripe\Forms\TextField;

class LinkBlockItem extends BlockItem
{
    private static $table_name = 'Blocks_LinkBlockItem';

    private static $db = [
        'Title' => 'Varchar(255)',
    ];

    private static $has_one = [
        'Parent' => LinkBlock::class
    ];

    private static $summary_fields = [
        'Title' => 'Title',
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            $fields->addFieldsToTab('Root.Main', [
                TextField::create('Title', 'Title'),
            ]);
        });

        return parent::getCMSFields();
    }
}
