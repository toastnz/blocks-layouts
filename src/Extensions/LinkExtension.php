<?php

namespace Toast\Blocks\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;

class LinkExtension extends Extension
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

    public function getDownloadAttribute(): string
    {
        if ($this->owner->OpenInNew) {
            return '';
        }

        if (!$this->owner->FileID) {
            return '';
        }

        $file = $this->owner->File();

        if (!$file?->exists()) {
            return '';
        }

        return " download='" . $file->getFilename() . "'";
    }

    public function getTargetAttribute(): string
    {
        return $this->owner->OpenInNew ? " target='_blank' rel='noopener noreferrer'" : '';
    }

    public function getExternalLinkAttributes(): string
    {
        if (!$this->owner->ExternalUrl) {
            return '';
        }

        return ' data-external-link';
    }

    public function getAccessibilityAttributes(): string
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

    public function getLinkAttributes(): string
    {
        $attributes = [
            $this->getDownloadAttribute(),
            $this->getTargetAttribute(),
            $this->getAccessibilityAttributes(),
            $this->getExternalLinkAttributes(),
        ];

        return implode(' ', array_filter($attributes));
    }
}
