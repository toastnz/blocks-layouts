<?php

namespace Toast\Blocks\Extensions;

use SilverStripe\Core\Convert;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\LinkField\Models\Link;

/**
 * Accessibility fields and ready-to-use HTML attributes for links, e.g. `<a href="$URL" $LinkAttributes>`.
 *
 * @extends Extension<Link>
 */
class LinkExtension extends Extension
{
    private static $db = [
        'AriaLabel' => 'Varchar(255)',
        'DescriptiveTitle' => 'Varchar(255)',
    ];

    private static $casting = [
        'LinkAttributes' => 'HTMLFragment',
        'AccessibilityAttributes' => 'HTMLFragment',
        'DownloadAttribute' => 'HTMLFragment',
        'TargetAttribute' => 'HTMLFragment',
        'ExternalLinkAttributes' => 'HTMLFragment',
    ];

    protected function updateCMSFields(FieldList $fields): void
    {
        $fields->addFieldsToTab('Root.Main', [
            TextField::create('AriaLabel', 'Aria Label')
                ->setDescription('This is used to describe the link to screen readers, and should be used when the link text itself is not descriptive enough, or when the link is an image. It can also be used to warn the user if the link will open in a new window.'),
            TextField::create('DescriptiveTitle', 'Descriptive Title')
                ->setDescription('This is used to provide additional context to the link, and is displayed as a tooltip when the user hovers over the link.'),
        ]);
    }

    /**
     * ` download="filename.pdf"` for file links that don't open in a new window.
     */
    public function getDownloadAttribute(): string
    {
        $owner = $this->getOwner();

        if ($owner->OpenInNew || !$owner->FileID) {
            return '';
        }

        $file = $owner->File();

        if (!$file?->exists()) {
            return '';
        }

        return $this->attribute('download', (string) $file->getFilename());
    }

    public function getTargetAttribute(): string
    {
        return $this->getOwner()->OpenInNew ? ' target="_blank" rel="noopener noreferrer"' : '';
    }

    public function getExternalLinkAttributes(): string
    {
        return $this->getOwner()->ExternalUrl ? ' data-external-link' : '';
    }

    public function getAccessibilityAttributes(): string
    {
        $owner = $this->getOwner();
        $attributes = ' ';

        if ($owner->AriaLabel) {
            $attributes .= ltrim($this->attribute('aria-label', (string) $owner->AriaLabel));
        }

        if ($owner->DescriptiveTitle) {
            $attributes .= $this->attribute('title', (string) $owner->DescriptiveTitle);
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

        $html = implode(' ', array_filter(array_map('trim', $attributes)));

        $this->getOwner()->extend('updateLinkAttributes', $html);

        return $html;
    }

    /**
     * An escaped ` name="value"` attribute.
     */
    private function attribute(string $name, string $value): string
    {
        return ' ' . $name . '="' . Convert::raw2att($value) . '"';
    }
}
