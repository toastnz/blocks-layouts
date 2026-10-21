<?php

namespace Toast\Blocks\Items;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\File;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use Toast\Blocks\DownloadBlock;

class DownloadBlockItem extends BlockItem
{
    private static $table_name = 'Blocks_DownloadBlockItem';

    private static $singular_name = 'Download File Item';

    private static $plural_name = 'Download File Items';

    private static $default_sort = 'SortOrder';

    private static $db = [
        'Title' => 'Varchar(255)',
        'Summary' => 'Text',
    ];

    private static $has_one = [
        'Parent' => DownloadBlock::class,
        'File' => File::class
    ];

    private static $owns = [
        'File'
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'File.CMSThumbnail' => 'File'
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->addFieldsToTab('Root.Main', [
                TextField::create('Title', 'Title'),
                TextareaField::create('Summary', 'Summary'),
                UploadField::create('File', 'File')
                    ->setFolderName('Uploads/Blocks/Files')
            ]);
        });

        return parent::getCMSFields();
    }
}
