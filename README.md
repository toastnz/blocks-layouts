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

### Add / remove available block classes

```yaml
Toast\Blocks\Extensions\PageExtension:
  available_blocks:
    - Toast\Blocks\TextBlock
```

### Position page template content between blocks

```yaml
Your\Page\Class:
  extensions:
    - Toast\Blocks\Extensions\PageContentBlockPageExtension
```

```silverstripe
<% loop $ContentBlocks.Sort('SortOrder') %>
    <% if $IsPageContentBlock %>
        <% with $Top %>
            <%-- Your page content code here --%>
        <% end_with %>
    <% else %>
        {$ForTemplate}
    <% end_if %>
<% end_loop %>
```

### Add / remove available alternate block layouts

"layout_src": directory that holds folders of different layouts with .ss templates
"layout_icon_src": directory that holds all the layout icons
"layout_dist_dir": specificed the css for block layouts

CSS file will only be included with the syntax of 'theme/themename/dist/styles/$LayoutName-$BlockType.css"

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

### Per-layout configuration (labels, icons, disabling)

Use `layout_config` on any block class to customise the label or icon shown in the CMS layout picker, or to disable a layout entirely. Keys are short layout folder names (e.g. `Default`, `Split`).

View the available icon classes here: `client/fonts/layout-icons/font/preview.html`

```yaml
Toast\Blocks\TextBlock:
  layout_config:
    Default:
      label: 'Default'
      layout_icon_path: '[resources]/app/client/images/layout-icons/default/textblock.svg'
    Split:
      label: 'Split'
      layout_icon_class: 'layout-icons-text-columns'
      disabled: true
```

Supported keys per layout entry:

| Key | Type | Description |
|---|---|---|
| `label` | string | Display label shown beneath the icon in the CMS picker |
| `layout_icon_path` | string | Path to an SVG file. Supports the `[resources]` token. Highest priority. |
| `layout_icon_class` | string | CSS class for an icon font or sprite. Overrides directory discovery. |
| `disabled` | bool | Set `true` to hide this layout from the CMS picker |

**Icon resolution priority:** `layout_icon_path` → `layout_icon_class` → auto-discovered SVG from `layout_icon_src` directory.

> Full class-name keys (e.g. `Toast\Blocks\Default\TextBlock`) are still supported for backward compatibility.

### Excluding layouts

Use `exclude_layouts` alongside `layout_config` to hide one or more layouts from the picker without specifying full config entries. This is the quickest way to remove a layout.

```yaml
Toast\Blocks\MediaTextBlock:
  exclude_layouts:
    - ShortRounded
```

**Precedence rules:**
- `layout_config.<Layout>.disabled: true` takes precedence over `exclude_layouts`.
- `exclude_layouts` only hides layouts from the CMS picker — the `.ss` template file must still exist for the block to render correctly if a record already holds that layout value.
- YAML cannot create layouts. A `.ss` template file must exist for a layout to appear.

### Fallback when a saved layout is removed

If a block record has a previously saved layout that is later excluded or disabled, the block will automatically fall back to the `Default` layout at render time and on next save. If `Default` is also unavailable, the first available layout is used instead.

When a content editor opens such a block in the CMS, a warning notice will appear above the layout picker advising them that the previous layout is no longer available and prompting them to select a replacement and save.

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

## Block Method Reference

The `Toast\Blocks\Block` class exposes the following methods.

### Public methods

#### Rendering and CMS
- `forTemplate(): string` - Resolve and render the active template with fallback.
- `getCMSFields()` - Build CMS fields for block editing.
- `getAvailableLayouts($className = null)` - Return discovered/allowed layouts.
- `getOptionsForLayouts($layouts = [])` - Build icon/label options for layout selectors.
- `getTemplateOptionsField()` - Return selector field (`OptionsetField`, `DropdownField`, or hidden fallback).
- `getCSSFile()` - Resolve the CSS file for the current layout/template.
- `onBeforeWrite()` - Enforce template fallback and persist CSS file path.
- `getTemplateClass()` - Return default template class name.
- `populateDefaults()` - Set default/fallback template on new records.

#### Display and metadata
- `getContentSummary()` - CMS summary for content/heading.
- `getIconForCMS()` - Block icon HTML shown in CMS.
- `IconForCMS()` - Alias for `getIconForCMS()`.
- `getTitle()` - Display title fallback to block singular name.
- `getApiURL()` - API endpoint URL for this block.
- `getBlockTemplateName()` - Short class name used as template base.
- `getHtmlID()` - Deterministic HTML id value for this block.
- `getDisplayTitle()` - Title with parent page context.
- `getImageFocusPosition($imageid = null)` - Return focus point CSS position for an image.
- `getBlockID()` - Anchor-friendly block id (from `AnchorName` or fallback).
- `getExtraRequirements()` - Extra frontend requirements injected by extensions.
- `getBlockSpecificExtraClasses()` - Extra CSS classes injected by extensions.

#### Linking and preview
- `getLink($action = null)` - Relative URL to this block anchor.
- `Link($action = null)` - Alias for `getLink()`.
- `getBlockLink($parent)` - Block link for a specific parent page.
- `getBlockPreviewURL($anchor = null)` - CMS preview URL including stage/subsite params.
- `getAbsoluteLink($action = null)` - Absolute stage URL to this block anchor.
- `AbsoluteLink($action = null)` - Alias for `getAbsoluteLink()`.

#### Page relationships
- `getLinkedPagesList()` - Comma-separated linked page titles.
- `getAllPages()` - Unique pages (main + subsites) using this block.
- `getPagesFromSiteTree()` - Pages using this block in current context.
- `getPagesFromMainSite()` - Pages using this block in main site context.
- `getPagesFromSubsites()` - Pages using this block across all subsites.
- `getParentPage()` - Attempt to resolve current parent page.
- `getPage()` - Resolve page owning this block in current controller context.
- `getCMSParentPage()` - Current CMS parent page.
- `getCMSEditLink(): ?string` - Direct CMS edit URL for this block.
- `getCMSSiblingBlocks()` - Sibling blocks on the same page.
- `getCMSSiblingBlocksLinks()` - HTML list of sibling block links.

#### Permissions and lifecycle
- `canView($member = null)`
- `canEdit($member = null)`
- `canDelete($member = null)`
- `canCreate($member = null, $context = [])`
- `canDeleteFromLive($member = null)`
- `canPublish($member = null)`
- `canArchive($member = null)`
- `isPublished()`
- `isNew()`
- `doArchive()`

### Key protected methods for subclasses

These are not part of the public API, but are useful if you extend `Block` and override behavior.

- Layout config and filtering:
  - `getLayoutConfig()`, `getExcludedLayouts()`, `getLayoutConfigEntry(...)`, `isLayoutDisabled(...)`
  - `filterLayoutsByClassName(...)`, `filterToConfiguredLayouts(...)`, `filterDisabledLayouts(...)`
- Layout discovery:
  - `discoverLayouts()`, `discoverModuleLayouts()`, `discoverAdditionalLayouts()`, `scanTemplateDirectory(...)`
- Icon resolution:
  - `getConfiguredLayoutIcon(...)`, `getLayoutIconClass(...)`, `getDiscoveredLayoutIcon(...)`, `getLayoutIconSourcePath()`
  - `buildLayoutOption(...)`, `getLayoutOptionLabel(...)`
- Template fallback and warnings:
  - `resolveTemplateWithFallback(...)`, `getAvailableTemplateClasses(...)`, `isTemplateMissing()`, `getTemplateMissingNoticeField()`
- URL helpers:
  - `getCurrentCMSRecord()`, `getLinkParentPage()`, `buildBlockLinkFromParent(...)`, `appendQueryParams(...)`, `appendHash(...)`

## Todo:
