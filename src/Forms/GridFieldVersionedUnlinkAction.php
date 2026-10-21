<?php

namespace Toast\Blocks;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Validation\ValidationException;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Forms\GridField\GridField_ColumnProvider;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\ORM\DataObject;

/**
 * GridField action that unlinks a block from the page being edited, without deleting or unpublishing it.
 */
class GridFieldVersionedUnlinkAction implements GridField_ColumnProvider, GridField_ActionProvider
{
    /**
     * Add an 'Actions' column
     */
    public function augmentColumns($gridField, &$columns): void
    {
        if (!in_array('Actions', $columns)) {
            $columns[] = 'Actions';
        }
    }

    public function getColumnAttributes($gridField, $record, $columnName): array
    {
        return ['class' => 'grid-field__col-compact'];
    }

    public function getColumnMetadata($gridField, $columnName): ?array
    {
        return $columnName === 'Actions' ? ['title' => ''] : null;
    }

    public function getColumnsHandled($gridField): array
    {
        return ['Actions'];
    }

    /**
     * @return \SilverStripe\ORM\FieldType\DBHTMLText|null the HTML for the column
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        return $this->getUnlinkAction($gridField, $record, $columnName)?->Field();
    }

    /**
     * @param GridField $gridField
     * @param DataObject $record
     */
    public function getUnlinkAction($gridField, $record, $columnName): ?GridField_FormAction
    {
        if (!$record->canEdit()) {
            return null;
        }

        return GridField_FormAction::create(
            $gridField,
            'UnlinkAction' . $record->ID,
            false,
            'unlink',
            ['RecordID' => $record->ID]
        )
            ->addExtraClass('btn btn--no-text btn--icon-md font-icon-link-broken grid-field__icon-action gridfield-button-unlink action-menu--handled')
            ->setAttribute('classNames', 'gridfield-button-unlink font-icon-link-broken')
            ->setAttribute('title', 'Unlink')
            ->setDescription('Unlink')
            ->setAttribute('aria-label', 'Unlink');
    }

    public function getActions($gridField): array
    {
        return ['unlink'];
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data): void
    {
        if ($actionName !== 'unlink') {
            return;
        }

        $item = $gridField->getList()->byID($arguments['RecordID']);

        if (!$item) {
            return;
        }

        if (!$item->canEdit()) {
            throw new ValidationException(
                _t('GridFieldAction_Edit.EditPermissionsFailure', 'No permission to unlink record')
            );
        }

        // $data['ID'] is the page being edited
        $page = isset($data['ID']) ? SiteTree::get()->byID($data['ID']) : null;

        if ($page && $page->exists()) {
            $page->ContentBlocks()->remove($item);
        }

        Controller::curr()->getResponse()->setStatusCode(200, 'Record unlinked.');
    }

    /**
     * Title for this action in the GridField action menu.
     */
    public function getTitle($gridField, $record, $columnName): string
    {
        return 'Unlink';
    }

    /**
     * Extra data for the GridField action menu schema.
     */
    public function getExtraData($gridField, $record, $columnName): ?array
    {
        return $this->getUnlinkAction($gridField, $record, $columnName)?->getAttributes();
    }
}
