<?php

namespace Toast\Blocks\Admin;

use Toast\Blocks\Block;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Core\Config\Config;
use Toast\Blocks\Extensions\PageExtension;
use Toast\Blocks\GridFieldContentBlockState;
use Toast\Blocks\GridFieldVersionedUnlinkAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use SilverStripe\Forms\GridField\GridFieldImportButton;
use SilverStripe\Versioned\VersionedGridFieldItemRequest;
use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;

class BlocksAdmin extends ModelAdmin
{
    private static $url_segment = 'blocks';

    private static $menu_title = 'Blocks';

    private static $menu_icon_class = 'font-icon-list';

    private static $managed_models = [
        Block::class => [
            'title' => 'Blocks'
        ]
    ];

    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);
        $gridFieldName = $this->sanitiseClassName($this->modelClass);
        $gridField = $form->Fields()->fieldByName($gridFieldName);

        $multiClass = new GridFieldAddNewMultiClass();
        $multiClass->setClasses(Config::inst()->get(PageExtension::class, 'available_blocks'));

        $gridField->getConfig()
            ->removeComponentsByType(GridFieldAddNewButton::class)
            ->removeComponentsByType(GridField_ActionMenu::class)
            ->addComponent($multiClass)
            ->removeComponentsByType(GridFieldExportButton::class)
            ->removeComponentsByType(GridFieldImportButton::class)
            ->removeComponentsByType(GridFieldPrintButton::class)
            ->getComponentByType(GridFieldDetailForm::class)
            ->setItemRequestClass(VersionedGridFieldItemRequest::class);

        return $form;
    }
}
