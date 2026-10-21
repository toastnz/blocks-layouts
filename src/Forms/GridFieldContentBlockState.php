<?php

namespace Toast\Blocks;

use SilverStripe\Forms\GridField\GridField_ColumnProvider;

/**
 * GridField column showing whether a block is a draft or published (and modified since).
 *
 * Based on the Lumberjack module's GridFieldSiteTreeState by Michael Strong.
 */
class GridFieldContentBlockState implements GridField_ColumnProvider
{
    public function augmentColumns($gridField, &$columns): void
    {
        // Ensure Actions always appears as the last column.
        $columns = array_values(array_diff($columns, ['Actions']));
        array_push($columns, 'State', 'Actions');
    }

    public function getColumnsHandled($gridField): array
    {
        return ['State'];
    }

    public function getColumnContent($gridField, $record, $columnName): ?string
    {
        if ($columnName !== 'State' || !$record->hasMethod('isPublished')) {
            return null;
        }

        $date = $record->dbObject('LastEdited')->Nice();

        if (!$record->isPublished()) {
            return _t(
                'GridFieldContentBlockState.Draft',
                '<i class="btn-icon gridfield-icon btn-icon-pencil"></i> Saved as Draft on {date}',
                'State for when a block is saved.',
                ['date' => $date]
            );
        }

        $modifiedLabel = $record->hasMethod('isModifiedOnDraft') && $record->isModifiedOnDraft()
            ? "<span class='modified'>" . _t('GridFieldContentBlockState.Modified', 'Modified') . '</span>'
            : '';

        return _t(
            'GridFieldContentBlockState.Published',
            '<i class="btn-icon gridfield-icon btn-icon-accept"></i> Published on {date}',
            'State for when a block is published.',
            ['date' => $date]
        ) . $modifiedLabel;
    }

    public function getColumnAttributes($gridField, $record, $columnName): array
    {
        if ($columnName !== 'State' || !$record->hasMethod('isPublished')) {
            return [];
        }

        return ['class' => 'gridfield-icon ' . ($record->isPublished() ? 'published' : 'draft')];
    }

    public function getColumnMetadata($gridField, $columnName): ?array
    {
        if ($columnName !== 'State') {
            return null;
        }

        return ['title' => _t('GridFieldContentBlockState.StateTitle', 'State', 'Column title for state')];
    }
}
