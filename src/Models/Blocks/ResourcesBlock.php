<?php

namespace Toast\Blocks;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ListboxField;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\Taxonomy\TaxonomyTerm;

class ResourcesBlock extends Block
{
    private static $singular_name = 'Resources Block';

    private static $plural_name = 'Resources Blocks';

    private static $description = 'Resources Block';

    private static $table_name = 'Blocks_ResourcesBlock';

    /**
     * Class of the resource records to list, and the many_many relation on it that holds its categories.
     */
    private static $target_resource_classname = 'Toast\Models\Resources\ResourceItem';

    private static $target_resource_category_relation = 'ResourceCategories';

    /**
     * Taxonomy type name whose terms can be picked as categories.
     */
    private static $target_resource_category = 'Resource Category';

    protected static $icon_class = 'font-icon-p-document';

    private static $many_many = [
        'Categories' => TaxonomyTerm::class
    ];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            // Remove the scaffolded Categories tab/GridField; categories are picked from a listbox instead
            $fields->removeByName('Categories');

            $fields->addFieldToTab(
                'Root.Main',
                ListboxField::create('Categories', 'Filter by Categories', $this->getRelevantCategories())
                    ->setDescription('Select categories to show resources from. You can add more Resource Categories in the Taxonomies section of the CMS')
            );
        });

        return parent::getCMSFields();
    }

    /**
     * All records of the configured resource class
     */
    public function getTargettedResourceItems(): DataList
    {
        $className = $this->config()->get('target_resource_classname');

        return $className::get();
    }

    /**
     * Taxonomy terms of the configured category type
     */
    public function getRelevantCategories(): DataList
    {
        $categories = TaxonomyTerm::get()->filter(
            'Type.Name',
            $this->config()->get('target_resource_category') ?? 'Resource Category'
        );

        return $this->updateRelevantCategories($categories);
    }

    /**
     * Allow extensions to modify relevant categories
     */
    public function updateRelevantCategories($categories): DataList
    {
        $this->extend('updateRelevantCategories', $categories);

        return $categories;
    }

    /**
     * All resource items, after extensions have had a chance to modify them
     */
    public function getResourceItems(): DataList
    {
        return $this->updateResourceItems($this->getTargettedResourceItems());
    }

    /**
     * Allow extensions to modify the list of resource items
     */
    public function updateResourceItems($items): DataList
    {
        $this->extend('updateResourceItems', $items);

        return $items;
    }

    /**
     * Resource items in any of the selected categories (all items when none are selected).
     */
    public function getItems($sort = 'SortOrder'): ArrayList
    {
        $items = $this->getResourceItems();
        $categoryIDs = $this->Categories()->column('ID');

        if ($categoryIDs) {
            $relation = $this->config()->get('target_resource_category_relation') ?: 'ResourceCategories';
            $items = $items->filter($relation . '.ID', $categoryIDs);
        }

        return $this->updateItems(ArrayList::create($items->toArray()))->sort($sort);
    }

    /**
     * Allow extensions to modify the list of items
     */
    public function updateItems($items): ArrayList
    {
        $this->extend('updateItems', $items);

        return $items;
    }
}
