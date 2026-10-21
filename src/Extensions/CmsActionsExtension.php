<?php

namespace Toast\Blocks\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldDetailForm_ItemRequest;
use Toast\Blocks\Block;

/**
 * Removes the "Add new" action from saved block edit forms.
 *
 * @extends Extension<GridFieldDetailForm_ItemRequest>
 */
class CmsActionsExtension extends Extension
{
    protected function updateFormActions(FieldList $actions): void
    {
        $record = $this->getOwner()->getRecord();

        if ($record instanceof Block && $record->exists()) {
            $actions->removeByName('new-record');
        }
    }
}
