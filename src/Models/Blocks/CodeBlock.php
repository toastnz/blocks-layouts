<?php

namespace Toast\Blocks;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;

class CodeBlock extends Block
{
    private static $table_name = 'Blocks_CodeBlock';

    private static $singular_name = 'Code';

    private static $plural_name = 'Codes';

    protected static $icon_class = 'font-icon-block-embed';

    /**
     * Content is also defined on Block. It is redeclared here because existing sites store CodeBlock
     * content in the Blocks_CodeBlock table; removing it would orphan that data.
     */
    private static $db = [
        'Content' => 'HTMLText'
    ];

    private static $required_fields = [
        'Content',
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName('Heading');

            $fields->addFieldToTab(
                'Root.Main',
                TextareaField::create('Content', 'Code')
                    ->setAttribute('data-language', 'html')
                    ->setAttribute('spellcheck', 'false')
            );
        });

        return parent::getCMSFields();
    }
}
