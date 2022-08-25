<?php

namespace Toast\Blocks\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use Toast\Blocks\Block;

class CmsActionsExtension extends DataExtension
{
    public function updateFormActions(FieldList $actions)
    {
        $record = $this->owner->getRecord();

        if (!$record instanceof Block || !$record->exists()) {
            return;
        }

        $actions->removeByName('new-record');
    }
}
