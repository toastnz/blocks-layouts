<?php

namespace Toast\Blocks\Traits;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;

/**
 * Shared "Columns" option for blocks that lay their items out in a grid.
 *
 * The options come from the `available_columns` config on the block class (falling back to 2, 3 and 4)
 * and can be changed by extensions through the `updateAvailableColumns` hook.
 *
 * ```yaml
 * Toast\Blocks\LinkBlock:
 *   available_columns: [2, 3]
 * ```
 */
trait HasColumns
{
    /**
     * Column options as a value => label map, e.g. [2 => 2, 3 => 3, 4 => 4].
     */
    public function getAvailableColumns(): array
    {
        $columns = $this->config()->get('available_columns') ?? [2, 3, 4];

        return $this->updateAvailableColumns((array) $columns);
    }

    /**
     * Lets extensions change the column options, then returns them as a value => label map.
     */
    public function updateAvailableColumns($columns): array
    {
        $this->extend('updateAvailableColumns', $columns);

        $columns = (array) $columns;

        return array_combine($columns, $columns);
    }

    /**
     * Replace the scaffolded Columns field with a dropdown of the available options.
     */
    protected function addColumnsField(FieldList $fields, string $tab = 'Root.Main'): void
    {
        $fields->removeByName('Columns');

        $options = $this->getAvailableColumns();

        if ($options) {
            $fields->addFieldToTab($tab, DropdownField::create('Columns', 'Columns', $options));
        }
    }
}
