<?php

namespace Toast\Blocks\Items;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\CMSPreviewable;
use SilverStripe\ORM\DataObject;

/**
 * Base class for items that belong to a block through a `Parent` has_one.
 *
 * Permissions and the CMS preview follow the parent block.
 */
class BlockItem extends DataObject implements CMSPreviewable
{
    private static $table_name = 'Blocks_BlockItem';

    private static $db = [
        'SortOrder' => 'Int'
    ];

    /**
     * Items aren't versioned, so show a single "Preview" switch in the CMS preview panel.
     *
     * @config
     */
    private static $show_unversioned_preview_link = true;

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $fields->removeByName([
                'ParentID',
                'SortOrder',
                'FileTracking',
                'LinkTracking'
            ]);

            if ($this->exists()) {
                $fields->addFieldToTab(
                    'Root.More',
                    ReadonlyField::create('BlockID', 'Item ID', $this->getBlockItemID())
                );
            }
        });

        return parent::getCMSFields();
    }

    /**
     * New items go to the end of their parent's list.
     */
    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if (!$this->SortOrder && $this->hasField('ParentID')) {
            $max = (int) static::get()->filter('ParentID', $this->ParentID)->max('SortOrder');
            $this->SortOrder = $max + 1;
        }
    }

    /**
     * Whether this item belongs to a parent record through a `Parent` relation.
     */
    protected function hasParentRelation(): bool
    {
        return $this->hasMethod('Parent');
    }

    /**
     * Anchor ID for the item, e.g. "OurTeam_12".
     */
    public function getBlockItemID(): string
    {
        $parent = $this->hasParentRelation() ? $this->Parent() : null;

        if (!$parent || !$parent->hasMethod('getBlockID')) {
            return (string) $this->ID;
        }

        return $parent->getBlockID() . '_' . $this->ID;
    }

    /**
     * Draft-stage preview URL of the parent block, scrolled to this item.
     */
    public function getBlockPreviewURL(): ?string
    {
        $parent = $this->hasParentRelation() ? $this->Parent() : null;

        if (!$parent || !$parent->hasMethod('getBlockPreviewURL')) {
            return null;
        }

        return $parent->getBlockPreviewURL($this->getBlockItemID());
    }

    /**
     * Used by the CMS preview panel (see {@link CMSPreviewable}).
     */
    public function PreviewLink($action = null): ?string
    {
        $link = $this->exists() ? $this->getBlockPreviewURL() : null;

        $this->extend('updatePreviewLink', $link, $action);

        return $link;
    }

    public function getMimeType(): ?string
    {
        return 'text/html';
    }

    public function canView($member = null)
    {
        return $this->hasParentRelation() ? $this->Parent()->canView($member) : parent::canView($member);
    }

    public function canEdit($member = null)
    {
        return $this->hasParentRelation() ? $this->Parent()->canEdit($member) : parent::canEdit($member);
    }

    public function canDelete($member = null)
    {
        return $this->hasParentRelation() ? $this->Parent()->canDelete($member) : parent::canDelete($member);
    }

    public function canCreate($member = null, $context = [])
    {
        return $this->hasParentRelation()
            ? $this->Parent()->canCreate($member, $context)
            : parent::canCreate($member, $context);
    }
}
