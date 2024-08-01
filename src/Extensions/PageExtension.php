<?php

namespace Toast\Blocks\Extensions;

use Toast\Blocks\Block;
use SilverStripe\Forms\Tab;
use SilverStripe\Assets\File;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Control\Director;
use SilverStripe\Forms\HiddenField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\View\Requirements;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Forms\GridField\GridField;
use Toast\Blocks\GridFieldContentBlockState;
use Toast\Blocks\GridFieldVersionedUnlinkAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\GridField\GridFieldSortableHeader;
use SilverStripe\Versioned\VersionedGridFieldItemRequest;
use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;

class PageExtension extends DataExtension
{
    private static $many_many = [
        'ContentBlocks' => Block::class
    ];

    private static $many_many_extraFields = [
        'ContentBlocks' => [
            'SortOrder' => 'Int'
        ]
    ];

    public function updateCMSFields(FieldList $fields)
    {
        if ($this->owner->exists()) {
            $config = GridFieldConfig_RelationEditor::create(50);
            $config->removeComponentsByType(GridFieldAddNewButton::class)
                ->removeComponentsByType(GridFieldFilterHeader::class)
                ->removeComponentsByType(GridField_ActionMenu::class);

            $self = $this->owner;

            $config->getComponentByType(GridFieldDetailForm::class)
                ->setItemRequestClass(VersionedGridFieldItemRequest::class)
                ->setItemEditFormCallback(function ($form, $itemRequest) use ($self) {
                    if (!$itemRequest->record->exists()) {
                        $nextSortOrder = $self->ContentBlocks()->max('SortOrder') + 1;
                        $form->Fields()->add(HiddenField::create('ManyMany[SortOrder]', 'Sort Order', $nextSortOrder));
                    }
                });

            $multiClass = new GridFieldAddNewMultiClass();

            $multiClass->setClasses(Config::inst()->get(PageExtension::class, 'available_blocks'));

            $config->addComponent($multiClass);

            $addExisting = $config->getComponentByType(GridFieldAddExistingAutocompleter::class);
            $addExisting->setSearchFields(['Title:PartialMatch' ]);

            $config->addComponent(new GridFieldOrderableRows('SortOrder'));


            $gridField = GridField::create(
                'ContentBlocks',
                'Blocks',
                $this->owner->ContentBlocks(),
                $config
            );

            $fields->addFieldToTab('Root.Blocks', $gridField);
        }
    }

    public function getBlockStyles()
    {
        $baseFolder = Director::baseFolder();
        $blocks = $this->owner->ContentBlocks();

        $components = new ArrayList();
        $components->merge($blocks);
        $this->owner->extend('updateCombinedBlocks', $components);

        $styles = [];

        foreach ($components as $block) {
            // Check if the CSSFile has been stored in the DB, otherwise find it
            $cachedCSSFile = $block->owner->CSSFile;
            $cssFile = $cachedCSSFile ? $cachedCSSFile : $block->getCSSFile();

            // Check if the file exists before adding it to the styles array
            if ($cssFile && file_exists($baseFolder . '/' . $cssFile) && !in_array($cssFile, $styles)) {
                $styles[] = $cssFile;
            }
        }

        if (!empty($styles)) {
            // With the first cssFile, we want to load it directly to the page as critical css
            $firstCssFile = array_shift($styles);
            Requirements::customCSS(file_get_contents($baseFolder . '/' . $firstCssFile));
        }

        if (!empty($styles)) {
            // Now load the rest of the css files as a single file
            Requirements::combine_files('blocks.css', $styles);
        }
    }
}

class PageControllerExtension extends Extension
{

    public function contentblock()
    {
        if (Director::is_ajax()) {
            $id = $this->owner->getRequest()->param('ID');
            $contentBlock = Block::get()->byID($id);

            if ($contentBlock && $contentBlock->exists()) {
                return $contentBlock->forTemplate();
            }
        }

        return $this->owner->redirect($this->owner->Link());
    }

}
