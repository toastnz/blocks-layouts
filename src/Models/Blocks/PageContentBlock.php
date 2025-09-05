<?php

namespace Toast\Blocks;

use SilverStripe\Forms\LiteralField;
use Toast\OpenCMSPreview\Fields\OpenCMSPreview;

class PageContentBlock extends Block
{
    private static $singular_name = 'Page Content Block';
    private static $plural_name = 'Page Content Blocks';
    private static $description = 'Used to position page template content within a flexible content area';
    private static $table_name = 'PageContentBlock';
    protected static $icon_class = 'font-icon-p-alt';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fieldNames = [
            'BlockSettingsHeading',
            'PageLinksHeading',
            'PageLinks',
            'More'
        ];

        foreach ($fields->dataFields() as $field) {
            $fieldNames[] = $field->getName();
        }

        // Remove all fields
        $fields->removeByName($fieldNames);

        // Add a literal field to explain the purpose of this block, as well as a preview
        $fields->addFieldsToTab('Root.Main', [
            OpenCMSPreview::create($this->getBlockPreviewURL()),
            LiteralField::create('Info', '<p class="message">This block is used to position the main page content within the flexible content area. It does not have its own editable content.</p>'),
        ]);

        return $fields;
    }

    // Helper to identify this block type
    public function IsPageContentBlock()
    {
        return true;
    }

    // Override this to prevent "Linked Pages" from showing because the list could be massive
    public function getLinkedPagesList()
    {
        return '';
    }
}
