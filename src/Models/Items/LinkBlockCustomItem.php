<?php

namespace Toast\Blocks\Items;

use SilverStripe\Assets\File;
use Sheadawson\Linkable\Models\Link;
use Toast\Blocks\Items\LinkBlockItem;
use SilverStripe\Forms\TextareaField;
use Sheadawson\Linkable\Forms\LinkField;
use SilverStripe\AssetAdmin\Forms\UploadField;

class LinkBlockCustomItem extends LinkBlockItem
{
    private static $table_name = 'Blocks_LinkBlockCustomItem';

    private static $singular_name = 'Custom Link';

    private static $plural_name = 'Custom Links';

    private static $db = [
        'Summary' => 'Text',
    ];

    private static $has_one = [
        'Link'   => Link::class,
        'Image'  => File::class,
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
                UploadField::create('Image', 'Image')
                    ->setFolderName('Uploads/Blocks')
                    ->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']),
                TextareaField::create('Summary', 'Summary'),
                LinkField::create('LinkID', 'Link'),
            ]);
        });

        return parent::getCMSFields();
    }
}
