<?php

namespace Toast\Blocks\Items;

use Toast\Blocks\LogoBlock;
use SilverStripe\Assets\File;
use SilverStripe\Forms\TextField;
use Toast\Blocks\Items\BlockItem;
use SilverStripe\ORM\FieldType\DBField;
use Sheadawson\Linkable\Forms\LinkField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\AssetAdmin\Forms\UploadField;

class LogoBlockItem extends BlockItem
{
    private static $table_name = 'Blocks_LogoBlockItem';

    private static $db = [
        'Title' => 'Varchar(255)',
        'BrandLink' => 'Varchar(255)',
    ];

    private static $has_one = [
        'Image'  => File::class,
        'Parent' => LogoBlock::class
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'ContentSummary' => 'Logo',
    ];
    private static $owns = [
        'Image',
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {
            $fields->addFieldsToTab('Root.Main', [
                TextField::create('Title', 'Title'),
                TextField::create('BrandLink', 'Brand Link'),
                UploadField::create('Image', 'Image')
                    ->setFolderName('Uploads/Blocks')
                    ->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']),
            ]);
        });

        return parent::getCMSFields();
    }

    public function getContentSummary()
    {
        $content = '';

        if ($this->Image() && $this->Image()->exists()) {
            $imageURL = $this->Image()->getAbsoluteURL();
            $content = "<img src='{$imageURL}' alt='{$this->Image()->Title}' width='80' height='80' style='object-fit: contain' />";
        }

        return DBField::create_field(DBHTMLText::class, $content);
    }

    public function canCreate($member = null, $context = [])
    {
        return parent::canCreate($member);
    }
}
