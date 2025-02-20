<?php

namespace Toast\Blocks;


use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextareaField;

class CodeBlock extends Block
{
    private static $table_name = 'Blocks_CodeBlock';

    private static $singular_name = 'Code';

    private static $plural_name = 'Codes';

    protected static $icon_class = 'font-icon-block-embed';

    private static $db = [
        'Content' => 'HTMLText'
    ];

    public function getCMSFields()
    {

        $this->beforeUpdateCMSFields(function ($fields) {
            // Remove fields
            $fields->removeByName('Heading', 'Content');

            $fields->addFieldsToTab('Root.Main', [
                TextareaField::create('Content', 'Code')
            ]);
        });

        return parent::getCMSFields();
    }

    public function getCMSValidator()
    {
        $required = new RequiredFields(['Content']);

        $this->extend('updateCMSValidator', $required);

        return $required;
    }
}
