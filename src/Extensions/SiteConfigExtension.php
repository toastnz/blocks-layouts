<?php

namespace Toast\Blocks\Extensions;

use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\DropdownField;
use Toast\Blocks\Helpers\Helper;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\CheckboxSetField;
use Toast\Blocks\Block;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;

class SiteConfigExtension extends DataExtension
{
  
    public function updateCMSFields(FieldList $fields)
    {
    }

}