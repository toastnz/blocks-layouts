<?php

namespace Toast\Blocks\Items;

use SilverStripe\Assets\File;
use Toast\Blocks\PercentageBlock;
use SilverStripe\Forms\TextField;
use Sheadawson\Linkable\Models\Link;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\DropdownField;
use Sheadawson\Linkable\Forms\LinkField;
use SilverStripe\AssetAdmin\Forms\UploadField;

class PercentageBlockItem extends BlockItem
{
    private static $table_name = 'Blocks_PercentageBlockItem';

    private static $db = [
        'Name' => 'Varchar(255)',
        'Title' => 'Varchar(255)',
        'Summary' => 'Text',
        'Width' => 'Enum("25, 33, 50, 66, 75, 100", "50")'
    ];

    private static $has_one = [
        'Link'   => Link::class,
        'Image'  => File::class,
        'Parent' => PercentageBlock::class
    ];

    private static $summary_fields = [
        'Name' => 'Name',
        'Title' => 'Title',
    ];
    private static $owns = [
        'Image',
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {

            $fields->addFieldsToTab('Root.Main', [
                UploadField::create('Image', 'Image')
                    ->setFolderName('Uploads/Blocks')
                    ->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']),
                TextField::create('Name', 'Name')
                    ->setDescription('This is for internal use only and will not be displayed on the website'),
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
