<?php

namespace Toast\Blocks;

use Toast\Blocks\Block;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\LiteralField;
use Toast\Blocks\Items\SliderBlockItem;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;

class SliderBlock extends Block
{
    private static $table_name = 'Blocks_SliderBlock';

    private static $singular_name = 'Media Slider';

    private static $plural_name = 'Media Slider';

    private static $has_many = [
        'Items'    => SliderBlockItem::class
    ];

    public function getCMSFields()
    {

        $this->beforeUpdateCMSFields(function ($fields) {
            $fields->removeByName('Items');

            if ($this->exists()) {
                $config = GridFieldConfig_RecordEditor::create();
                $config->addComponent(GridFieldOrderableRows::create('SortOrder'));
                $grid = GridField::create('Items', 'Items', $this->Items(), $config);
                $fields->addFieldsToTab('Root.Main', [
                    $grid
                ]);
            }else{
                $fields->addFieldToTab('Root.Main', LiteralField::create('Notice', '<div class="message notice">Save this block and more options will become available.</div>'));
            }
        });
        return parent::getCMSFields();
    }

}
