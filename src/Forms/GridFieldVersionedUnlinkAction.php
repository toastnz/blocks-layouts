<?php

namespace Toast\Blocks;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\Debug;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\GridField\GridField_ColumnProvider;
use SilverStripe\Forms\GridField\GridField_ActionProvider;

class GridFieldVersionedUnlinkAction implements GridField_ColumnProvider, GridField_ActionProvider
{
    /**
     * Add a column 'Delete'
     *
     * @param GridField $gridField
     * @param array $columns
     */
    public function augmentColumns($gridField, &$columns)
    {
        if (!in_array('Actions', $columns)) {
            $columns[] = 'Actions';
        }
    }

    /**
     * Return any special attributes that will be used for FormField::create_tag()
     *
     * @param GridField $gridField
     * @param DataObject $record
     * @param string $columnName
     * @return array
     */
    public function getColumnAttributes($gridField, $record, $columnName)
    {
        return ['class' => 'grid-field__col-compact'];
    }

    /**
     * Add the title
     *
     * @param GridField $gridField
     * @param string $columnName
     * @return array
     */
    public function getColumnMetadata($gridField, $columnName)
    {
        if ($columnName == 'Actions') {
            return ['title' => ''];
        }
    }

    /**
     * Which columns are handled by this component
     *
     * @param GridField $gridField
     * @return array
     */
    public function getColumnsHandled($gridField)
    {
        return ['Actions'];
    }

    /**
     *
     * @param GridField $gridField
     * @param DataObject $record
     * @param string $columnName
     * @return string|null the HTML for the column
     */
    public function getColumnContent($gridField, $record, $columnName)
    {
        $field = $this->getUnlinkAction($gridField, $record, $columnName);

        if ($field) {
            return $field->Field();
        }

        return null;
    }

    /**
     * @param GridField  $gridField
     * @param DataObject $record
     * @param string     $columnName
     * @return GridField_FormAction
     */
    public function getUnlinkAction($gridField, $record, $columnName)
    {
        if (!$record->canEdit()) {
            return;
        }

        $field = GridField_FormAction::create(
            $gridField,
            'UnlinkAction' . $record->ID,
            false,
            "unlink",
            ['RecordID' => $record->ID]
        )
            ->addExtraClass('btn btn--no-text btn--icon-md font-icon-link-broken grid-field__icon-action gridfield-button-unlink action-menu--handled')
            ->setAttribute('classNames', 'gridfield-button-unlink font-icon-link-broken')
            ->setAttribute('title', 'Unlink')
            ->setDescription('Unlink')
            ->setAttribute('aria-label', 'Unlink');

        return $field;
    }

    public function getActions($gridField)
    {
        return ['unlink'];
    }

    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName == 'unlink') {
            /** @var Block|SiteTree $item */
            $item = $gridField->getList()->byID($arguments['RecordID']);
            if (!$item) {
                return;
            }

            if (!$item->canEdit()) {
                throw new ValidationException(
                    _t('GridFieldAction_Edit.EditPermissionsFailure', "No permission to unlink record"), 0);
            }

            // RecordID = CB ID

            // Page = $data['ID']

            if (isset($data['ID'])) {
                // Perform a deletion
                $page = SiteTree::get()->byID($data['ID']);

                if ($page && $page->exists()) {
                    $page->ContentBlocks()->remove($item);
                }
            }

            Controller::curr()->getResponse()->setStatusCode(
                200,
                'Record unlinked.'
            );
        }
    }

    /**
     * Gets the title for this menu item
     *
     * @see {@link GridField_ActionMenu->getColumnContent()}
     *
     * @param GridField  $gridField
     * @param DataObject $record
     *
     * @return string $title
     */
    public function getTitle($gridField, $record, $columnName)
    {
        return 'Unlink';
    }

    /**
     * Gets any extra data that could go in to the schema that the menu generates
     *
     * @see {@link GridField_ActionMenu->getColumnContent()}
     *
     * @param GridField  $gridField
     * @param DataObject $record
     *
     * @return array $data
     */
    public function getExtraData($gridField, $record, $columnName)
    {
        $field = $this->getUnlinkAction($gridField, $record, $columnName);

        if ($field) {
            return $field->getAttributes();
        }

        return null;
    }
}