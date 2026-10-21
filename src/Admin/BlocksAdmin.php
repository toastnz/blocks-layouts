<?php

namespace Toast\Blocks\Admin;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldImportButton;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use SilverStripe\Versioned\VersionedGridFieldItemRequest;
use Toast\Blocks\Block;
use Toast\Blocks\Helpers\Helper;

/**
 * Central "Blocks" section for managing every block, regardless of the page it is on.
 */
class BlocksAdmin extends ModelAdmin
{
    private static $url_segment = 'blocks';

    private static $menu_title = 'Blocks';

    private static $menu_icon_class = 'font-icon-clone';

    private static $managed_models = [
        Block::class => [
            'title' => 'Blocks'
        ]
    ];

    protected function getGridFieldConfig(): GridFieldConfig
    {
        $config = parent::getGridFieldConfig()
            ->removeComponentsByType([
                GridFieldAddNewButton::class,
                GridField_ActionMenu::class,
                GridFieldExportButton::class,
                GridFieldImportButton::class,
                GridFieldPrintButton::class,
            ])
            ->addComponent(Helper::createAddNewMultiClass());

        $config->getComponentByType(GridFieldDetailForm::class)
            ->setItemRequestClass(VersionedGridFieldItemRequest::class)
            // ModelAdmin validates with the base Block class; use each block type's own rules instead
            ->setItemEditFormCallback(function (Form $form, GridFieldDetailForm_ItemRequest $itemRequest) {
                $form->setValidator($itemRequest->getRecord()->getCMSCompositeValidator());
            });

        return $config;
    }
}
