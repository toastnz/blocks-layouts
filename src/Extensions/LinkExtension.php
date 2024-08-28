<?php

namespace Toast\Blocks\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

class LinkExtension extends DataExtension
{
    private static $db = [
        'AriaLabel' => 'Varchar(255)',
        'DescriptiveTitle' => 'Varchar(255)',
    ];

    private static $casting = [
        'LinkAttributes' => 'HTMLFragment',
        'AccessibilityAttributes' => 'HTMLFragment',
    ];

    // Add the AriaLabel and DescriptiveTitle fields to the CMS
    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab(
            'Root.Main',
            [
                TextField::create('AriaLabel', 'Aria Label')
                    ->setDescription('This is used to describe the link to screen readers, and should be used when the link text itself is not descriptive enough, or when the link is an image. It can also be used to warn the user if the link will open in a new window.'),
                TextField::create('DescriptiveTitle', 'Descriptive Title')
                    ->setDescription('This is used to provide additional context to the link, and is displayed as a tooltip when the user hovers over the link.'),
            ]
        );
    }

    // Override the default TargetAttr method to also return rel="noopener noreferrer" if the link is set to open in a new window
    public function getTargetAttr()
    {
        return $this->owner->OpenInNewWindow ? " target='_blank' rel='noopener noreferrer'" : '';
    }

    public function getAccessibilityAttributes()
    {
        $attributes = ' ';

        if ($this->owner->AriaLabel) {
            $attributes .= "aria-label='" . $this->owner->AriaLabel . "'";
        }

        if ($this->owner->DescriptiveTitle) {
            $attributes .= " title='" . $this->owner->DescriptiveTitle . "'";
        }

        return $attributes;
    }

    public function getLinkAttributes()
    {
        return $this->getAccessibilityAttributes() . $this->getTargetAttr();
    }
}
