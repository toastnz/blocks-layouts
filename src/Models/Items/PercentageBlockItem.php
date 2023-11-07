<?php

namespace Toast\Blocks\Items;

use Toast\Blocks\PercentageBlock;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\TextField;
use Sheadawson\Linkable\Models\Link;
use SilverStripe\Forms\TextareaField;
use Sheadawson\Linkable\Forms\LinkField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

class PercentageBlockItem extends BlockItem
{
    private static $table_name = 'Blocks_PercentageBlockItem';

    private static $db = [
        'SortOrder' => 'Int',
        'Title' => 'Varchar(255)',
        'Summary' => 'Text',
        'Width' => 'Enum("25, 33, 50, 66, 75, 100", "50")'
    ];

    private static $has_one = [
        'Link'   => Link::class,
        'Image'  => Image::class,
        'Parent' => PercentageBlock::class
    ];

    private static $summary_fields = [
        'Title' => 'Title',
    ];
    private static $owns = [
        'Image',
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            $fields->addFieldsToTab('Root.Main', [
                UploadField::create('Image', 'Thumbnail')
                    ->setFolderName('Uploads/Blocks'),
                TextField::create('Title', 'Title'),
                TextareaField::create('Summary', 'Summary'),
                DropdownField::create('Width', 'Width', $this->dbObject('Width')->enumValues())->setEmptyString('--- Please select ---'),
                LinkField::create('LinkID', 'Link'),
            ]);

        });

        return parent::getCMSFields();
    }

    public function canCreate($member = null, $context = []) {
        // if there are more than 4 items in the block, don't allow new items to be created
        //if ($this->Parent()->Items()->Count() >= 4) {
        //   return false;
        //}
        return parent::canCreate($member);
    }
}
