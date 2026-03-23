<?php

namespace Toast\Blocks;

use Toast\Blocks\Block;
use SilverStripe\ORM\DataList;
use SilverStripe\Forms\ListboxField;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\Taxonomy\TaxonomyTerm;

class ResourcesBlock extends Block
{
    private static $singular_name = 'Resources Block';
    private static $plural_name = 'Resources Blocks';
    private static $description = 'Resources Block';
    private static $table_name = 'Blocks_ResourcesBlock';
    private static $target_resource_classname = 'Toast\Models\Resources\ResourceItem';

    protected static $icon_class = 'font-icon-p-document';

    private static $many_many = [
        'Categories' => TaxonomyTerm::class
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // Get the relevant categories
        $categories = $this->getRelevantCategories();

        $fields->addFieldsToTab('Root.Main', [
            ListboxField::create('Categories', 'Filter by Categories', $categories)
                ->setDescription('Select categories to show resources from. You can add more Resource Categories in the Taxonomies section of the CMS'),
        ]);

        return $fields;
    }

    /** Get the relevant resource items to reference in this block
    * @return DataList
    */
    public function getTargettedResourceItems(): DataList
    {
        // Get the class name of the target resource item from the config
        $className = $this->Config()->get('target_resource_classname');
        // Get all the items of the target resource item class
        $items = $className::get();

        return $items;
    }

    /** Get all the categories from the taxonomies
     * @return DataList
     */
    public function getRelevantCategories(): DataList
    {
        // Get all blog tags
        $categories = TaxonomyTerm::get()->filter('Type.Name', 'Resource Category');

        // Allow extensions to modify the list of categories
        return $this->updateRelevantCategories($categories);
    }

    /** Allow extensions to modify relevant categories
     * @param DataList $categories
     * @return DataList
     */
    public function updateRelevantCategories($categories): DataList
    {
        // Allow extensions to modify the list of categories
        $this->extend('updateRelevantCategories', $categories);

        return $categories;
    }

    /** Get all resource items
     * @return DataList
     */
    public function getResourceItems(): DataList
    {
        $items = $this->getTargettedResourceItems();

        // Return the items after allowing extensions to modify them
        return $this->updateResourceItems($items);
    }

    /** Allow extensions to modify the list of resource items
     * @param DataList $items
     * @return DataList
     */
    public function updateResourceItems($items): DataList
    {
        // Allow extensions to modify the list of resource items
        $this->extend('updateResourceItems', $items);

        return $items;
    }

    /** Get posts from the relevant items
     * @param string $sort
     * @return ArrayList
     */
    public function getItems($sort = 'SortOrder'): ArrayList
    {
        // Require categories to be selected - return empty list if none selected
        if (!$this->Categories()->exists()) {
            return new ArrayList();
        }

        $items = new ArrayList();
        $categoryIDs = $this->Categories()->column('ID');

        // Get all the items that are in the selected categories
        foreach ($this->getResourceItems() as $resourceItem) {
            $itemCategoryIDs = $resourceItem->ResourceCategories()->column('ID');
            if (array_intersect($categoryIDs, $itemCategoryIDs)) {
                $items->push($resourceItem);
            }
        }

        // Allow extensions to modify the list of items
        $items = $this->updateItems($items);

        // Sort the items
        $items = $items->sort($sort);

        // Limit the number of items if a limit is set
        $items = $items->limit($this->Limit ?: 4);

        return $items;
    }

    /** Allow extensions to modify the list of items
     * @param ArrayList $items
     * @return ArrayList
     */
    public function updateItems($items): ArrayList
    {
        // Allow extensions to modify the list of items
        $this->extend('updateItems', $items);

        return $items;
    }
}
