# SilverStripe Blocks

Simple content blocks system. Nothing fancy, easy to implement.

## Requirements

See composer.json

## Installation

Add the following to your `config.yml` (optional):

```yaml
PageController:
  extensions:
    - Toast\Blocks\Extensions\PageControllerExtension
```

Use `Page` or other class that extends `SiteTree`.

In your `Layout/Page.ss` template, add the following:

```silverstripe
<% loop $ContentBlocks %>
    $ForTemplate
<% end_loop %>
```

## Configuration

### Add / remove available blocks

```yaml
Toast\Blocks\Extensions\PageExtension:
  available_blocks:
    - Toast\Blocks\TextBlock
```

### Configure your own extra block classes

```yaml
Toast\Blocks\Block:
  common_block_classes:
    - 'block'
  first_block_classes:
    - 'first'
    - 'in-view'
  last_block_classes:
    - 'last'
  other_block_classes:
    - 'js-in-view'

### Position page template content between blocks

```yaml
Your\Page\Class:
  extensions:
    - Toast\Blocks\Extensions\PageContentBlockPageExtension
```

```silverstripe
<% if $HasPageContentBlock %>
    <% loop $ContentBlocks.Sort('SortOrder') %>
        {$ForTemplate}
    <% end_loop %>
<% else %>
    {$Layout}
<% end_if %>
```

### Add / remove available alternate block layouts

"layout_src": directory that holds folders of different layouts with .ss templates
"layout_icon_src": directory that holds all the layout icons
"layout_dist_dir": specificed the css for block layouts

CSS file will only be included with the syntax of 'theme/themename/dist/styles/$LayoutName-$BlockType.css"

You can update this syntax using `updateBlockTemplateCSS` in a BlockExtension.

```yaml
Toast\Blocks\Extensions\PageExtension:
  layout_src: 'app/templates/Toast/Blocks'
  layout_icon_src: 'app/client/images/layout-icons'
  layout_dist_dir: 'theme/themename/dist/styles'

```

Ensure there are at least one CustomBlock.ss and 'customblock.svg' icon in each of the specified directory.
Layout will be available for all subsites.

### .ss template naming
You may have multiple layouts, please ensure you have the block.ss created under a new layout folder in the src directory.
e.g. 'app/templates/Toast/Blocks/**CustomLayoutNameOne**/ImageBlock.ss'
or  'app/templates/Toast/Blocks/**CustomLayoutNameTwo**/ImageBlock.ss'

### Layout icon naming:
Please ensure the layout icon are named after the block name are all in lowercase, e.g. customblock.svg.
e.g. 'app/client/images/layout-icons/**customlayoutone**/customblock.svg'
or  'app/templates/Toast/Blocks/**customlayouttwo**/customblock.svg'

### Icon extensions
Allowed extension: 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'

### Create a custom block

Extend `Block` to create a new block type.

```php
<?php


class MyBlock extends Toast\Blocks\Block
{
    private static $singular_name = 'My Block';
    private static $plural_name = 'My Blocks';
    private static $icon = 'mysite/images/blocks/custom.png';

    private static $db = [
        'Content' => 'HTMLText'
    ];

}
```

`/themes/default/templates/Toast/Blocks/MyBlock.ss`:

```silverstripe
<%-- Your block template here --%>

<h2>$Title</h2>
$Content
```

## Todo:
