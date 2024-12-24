<?php

namespace Toast\Blocks;

use SilverStripe\Forms\TextField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use Toast\Blocks\Items\TestimonialBlockItem;

class TestimonialBlock extends Block
{
    private static $table_name = 'Blocks_TestimonialBlock';

    private static $singular_name = 'Testimonial';

    private static $plural_name = 'Testimonial';

    private static $db = [
        'Heading' => 'Varchar(255)',
        'Content' => 'HTMLText'
    ];

    private static $has_many = [
        'Items' => TestimonialBlockItem::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {
            $fields->removeByName('Items');

            $fields->addFieldsToTab('Root.Main', [
                TextField::create('Heading', 'Heading'),
                HTMLEditorField::create('Content', 'Content')
            ]);

            if ($this->ID) {
                $testimonialConfig = GridFieldConfig_RelationEditor::create(10);
                $testimonialConfig->addComponent(GridFieldOrderableRows::create('SortOrder'))
                    ->removeComponentsByType(GridFieldDeleteAction::class)
                    ->addComponent(new GridFieldDeleteAction(false))
                    ->removeComponentsByType('GridFieldAddExistingAutoCompleter');

                $testimonialBlockGridField = GridField::create(
                    'Items',
                    'Testimonial Items',
                    $this->owner->Items(),
                    $testimonialConfig
                );
                $fields->addFieldToTab('Root.Main', $testimonialBlockGridField);
            }
        });

        return parent::getCMSFields();
    }

}
