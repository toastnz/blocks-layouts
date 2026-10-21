<?php

namespace Toast\Blocks;

use SilverStripe\Forms\FieldList;
use Toast\Blocks\Items\TestimonialBlockItem;

class TestimonialBlock extends Block
{
    private static $table_name = 'Blocks_TestimonialBlock';

    private static $singular_name = 'Testimonial';

    private static $plural_name = 'Testimonial';

    protected static $icon_class = 'font-icon-block-quote';

    private static $has_many = [
        'Items' => TestimonialBlockItem::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $this->addItemsGridField($fields, 'Items', 'Testimonial Items', 10);
        });

        return parent::getCMSFields();
    }
}
