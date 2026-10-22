# Silverstripe Blocks

Simple content blocks system for Silverstripe CMS 6. Nothing fancy, easy to implement.

## Requirements

See composer.json (Silverstripe CMS 6, PHP 8.3+).

## Installation

```sh
composer require toastnz/blocks-layouts
vendor/bin/sake db:build --flush
```

Blocks are added to `Page` (and every subclass) automatically.

In your `Layout/Page.ss` template, add the following:

```silverstripe
<% loop $ContentBlocks.Sort('SortOrder') %>
    $ForTemplate
<% end_loop %>
```

In your main `Page.ss` template, add the block helpers to the `<head>`:

```silverstripe
{$BlockStyles}          <%-- layout stylesheets for the blocks on this page --%>
{$ColourBlockScript}    <%-- the <colour-block> custom element --%>
{$BlockPreviewScripts}  <%-- live updates inside the CMS preview panel --%>
```

### Optional: render a single block over AJAX

```yaml
PageController:
  extensions:
    - Toast\Blocks\Extensions\PageControllerExtension
  allowed_actions:
    - contentblock
```

`/your-page/contentblock/<BlockID>` then returns the rendered block (only blocks on that page, AJAX requests only).

## CMS preview

Blocks and block items use Silverstripe's built-in CMS preview panel (they implement `CMSPreviewable`). Editing a block shows the page it is on, scrolled to the block, with the usual Draft/Published switches. Block items preview their parent block, scrolled to the item.

Hide the switches per block class if needed:

```yaml
Toast\Blocks\TextBlock:
  show_stage_link: false
  show_live_link: false
```

> The module no longer uses `toastnz/open-cms-preview` itself, but still requires it so that sites using it directly keep working.

## Configuration

### Add / remove available block classes

```yaml
Toast\Blocks\Extensions\PageExtension:
  available_blocks:
    - Toast\Blocks\TextBlock
```

Page types can change the list with the `updateAvailableBlocks(array &$classes)` extension hook.

### Required fields

Each block class lists the fields that must be filled in before it can be saved. `TextBlock`, `CodeBlock` and `MediaTextBlock` require `Content`.

```yaml
Toast\Blocks\ImageBlock:
  required_fields:
    - Image
```

To remove a requirement, use the `updateCMSRequiredFields(array &$fields)` hook, or reset the list with a `null` value in an earlier YAML fragment.

### Columns

Blocks with a Columns option (Blog, Children, Collection, Gallery, Link, Logo, Posts) read their options from `available_columns` (default 2, 3, 4):

```yaml
Toast\Blocks\LinkBlock:
  available_columns: [2, 3]
```

`PercentageBlock.available_columns` (default 1–4) controls the width options of percentage items.

### Block stylesheets

`$BlockStyles` inlines the first two layout stylesheets as critical CSS and combines the rest into `blocks.css`. Change how many are inlined:

```yaml
Page:
  critical_block_stylesheets: 1
```

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

The Page Content Block renders the page's own `Layout` template in its position.

### Add / remove available alternate block layouts

"layout_src": directory that holds folders of different layouts with .ss templates
"layout_icon_src": directory that holds all the layout icons
"layout_dist_dir": directory that holds the css for block layouts

CSS file will only be included with the syntax of 'theme/themename/dist/styles/$LayoutName-$BlockType.css" (change the file name with the `updateBlockTemplateCSS(&$cssFileName)` hook).

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

> Once a block class has `layout_config` entries, only the layouts listed there are offered.
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

### Block icons in the CMS

Set the icon font class in a subclass, or point to an image with the `block-icon` config:

```yaml
Toast\Blocks\AccordionBlock:
  block-icon: 'toastnz/blocks-layouts: client/images/block-icons/accordion.svg'
```

Paths use Silverstripe's module resource syntax (`vendor/module: path/to/file`). The older `[resources]/...` token and plain URLs still work, as do `layout_icon_path` values (see above).

### Pages using this block

The block edit form lists the pages using the block (grouped by subsite when subsites is installed). To show Silverstripe's built-in "Used on" table instead:

```yaml
Toast\Blocks\Block:
  use_native_used_on_table: true
```

The built-in table only lists pages in the current subsite.

### CMS edit links

`getCMSEditLink()` links through the page being edited. When there is no page context it falls back to the Blocks admin section, using Silverstripe's `CMSEditLinkExtension` (`cms_edit_owner`). The `updateCMSEditLink` hook still applies.

### CMS templates

The markup the module adds to the CMS (block icons, the "pages using this block" list, sibling block links, layout picker options, notices) is rendered from templates in `templates/Toast/Blocks/Includes/`. Override any of them by adding a file with the same name to your theme or `app/templates/Toast/Blocks/Includes/`.

| Template | Used for |
|---|---|
| `BlockIcon.ss` | `Block::getIconForCMS()` (GridField "Type" column and sibling strip) |
| `BlockSiblings.ss` | `Block::getCMSSiblingBlocksLinks()` |
| `BlockPageLinks.ss` | The "Pages using this block" list |
| `BlockLayoutOption.ss` | One option in the layout picker |
| `BlockLink.ss` | The block link on the More tab |
| `CMSNotice.ss` | Info/warning notices in block edit forms (`Helper::createCMSNoticeField()`) |
| `ImageThumbnail.ss` | Image previews in GridField summaries |
| `BlockPreviewScripts.ss` | `$BlockPreviewScripts` |

The module's CMS stylesheet and script are registered with `LeftAndMain.extra_requirements_css` / `extra_requirements_javascript`. `Toast\Blocks\Extensions\LeftAndMainExtension` is deprecated and does nothing.

### Create a custom block

Extend `Block` to create a new block type.

```php
<?php

namespace App\Blocks;

use SilverStripe\Forms\FieldList;
use Toast\Blocks\Block;

class MyBlock extends Block
{
    private static $table_name = 'App_MyBlock';

    private static $singular_name = 'My Block';

    private static $plural_name = 'My Blocks';

    protected static $icon_class = 'font-icon-block-content';

    private static $db = [
        'Intro' => 'Text',
    ];

    private static $has_many = [
        'Items' => MyBlockItem::class, // extends Toast\Blocks\Items\BlockItem, has_one Parent => MyBlock
    ];

    private static $required_fields = ['Intro'];

    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            // Sortable items GridField (or a "save first" notice for new blocks)
            $this->addItemsGridField($fields, 'Items', 'Items');
        });

        return parent::getCMSFields();
    }
}
```

`/themes/default/templates/Toast/Blocks/Default/MyBlock.ss`:

```silverstripe
<colour-block id="{$BlockID}" class="my-block {$ExtraClasses}">
    <h2>$Heading</h2>
    $Content
</colour-block>
```

Use the `Toast\Blocks\Traits\HasColumns` trait to add a Columns dropdown (`$this->addColumnsField($fields)`).

## The `<colour-block>` element

`{$ColourBlockScript}` loads a custom element that compares each block's background colour with its neighbours and adds:

- `collapsed-top` / `collapsed-bottom` when the previous / next block has the same background
- `transparent` when the block has no background of its own (it is treated as the page background)

It understands any CSS colour (including `oklch()` and `rgb(r g b / a)`), and re-checks when blocks are added, removed or restyled, when stylesheets load, and when the viewport or colour scheme changes. Call `customElements.get('colour-block').refreshAll()` to force a re-check. If your theme already defines `<colour-block>`, the module's version steps aside.

## Extension hooks

| Hook | On | Arguments |
|---|---|---|
| `updateBlockTemplate` | Block | `&$template` |
| `updateBlockTemplateCSS` | Block | `&$cssFileName` |
| `updateExtraClasses` | Block | `&$classes` |
| `updateBlockSpecificExtraClasses` | Block | `&$classes` |
| `updateExtraRequirements` | Block | `&$extraRequirements` |
| `updateBlockID` | Block | `&$id` |
| `updateContentSummary` | Block | `&$summary` |
| `updateIconForCMS` | Block | `&$html` |
| `updateAvailableLayouts` | Block | `&$layouts, $className` |
| `updateTemplateOptionsField` | Block | `&$field` |
| `updateCMSRequiredFields` | Block | `&$fields` |
| `updateItemsGridFieldConfig` | Block | `$config, $relationName` |
| `updateLink` / `updatePreviewLink` | Block | `&$link, $action` |
| `updateCMSEditLink` | Block | `&$link` |
| `updateAllPages` | Block | `&$pages` |
| `updateBlockPageLinksHTMLForCMS` | Block | `&$html, $pages` |
| `updateAvailableColumns` | column blocks | `&$columns` |
| `updatePosts` | BlogBlock, PostsBlock | `&$posts` |
| `updateItems` | ChildrenBlock, ResourcesBlock | `&$items` |
| `updateRelevantPages` | CollectionBlock | `&$pages` |
| `updateWidthOptions` | PercentageBlockItem | `&$options` |
| `updatePreviewLink` | BlockItem | `&$link, $action` |
| `updateAvailableBlocks` | Page | `&$classes` |
| `updateContentBlocksGridFieldConfig` | Page | `$config` |
| `updateCombinedBlocks` | Page | `&$components` |
| `updateBlockStylesheets` | Page | `&$styles` |
| `updateLinkAttributes` | Link | `&$html` |
| `updateBlockApiResponse` | BlocksApiController | `&$data, $block` |

## Block Method Reference

The `Toast\Blocks\Block` class exposes the following methods.

### Public methods

#### Rendering and CMS
- `forTemplate(): string` - Resolve and render the active template with fallback.
- `getCMSFields()` - Build CMS fields for block editing.
- `getCMSCompositeValidator(): CompositeValidator` - CMS validation, including `required_fields`.
- `getCMSRequiredFields(): array` - The required field names.
- `getAvailableLayouts(?string $className = null): array` - Return discovered/allowed layouts.
- `getOptionsForLayouts(array $layouts = []): array` - Build icon/label options for layout selectors.
- `getTemplateOptionsField(): FormField` - Return selector field (`OptionsetField`, `DropdownField`, or hidden fallback).
- `getCSSFile(): ?string` - Resolve the CSS file for the current layout/template.
- `onBeforeWrite(): void` - Enforce template fallback and persist CSS file path.
- `getTemplateClass(): string` - Return default template class name.
- `populateDefaults(): void` - Set default/fallback template on new records.

#### Display and metadata
- `getContentSummary()` - CMS summary for content/heading.
- `getIconForCMS(): ?DBField` / `IconForCMS()` - Block icon HTML shown in CMS.
- `getTitle(): ?string` - Display title fallback to block singular name.
- `getApiURL(): string` - API endpoint URL for this block.
- `getBlockTemplateName(): string` - Short class name used as template base.
- `getBlockLayoutName(): string` - Short layout name, e.g. `Default`.
- `getHtmlID(): string` - Deterministic HTML id value for this block.
- `getDisplayTitle(): string` - Title with parent page context.
- `getBlockID(): string` - Anchor-friendly block id (from `AnchorName` or fallback).
- `getExtraRequirements(): mixed` - Extra frontend requirements injected by extensions.
- `getExtraClasses(): string` - Position-based CSS classes (`first_block_classes`, `last_block_classes`, `other_block_classes`, `common_block_classes`).
- `getBlockSpecificExtraClasses(): array` - Extra CSS classes injected by extensions.

#### Linking and preview
- `getLink($action = null): string` / `Link()` - Relative URL to this block anchor.
- `getBlockLink($parent): string` - Block link for a specific parent page.
- `PreviewLink($action = null): ?string` - URL for the CMS preview panel.
- `getBlockPreviewURL($anchor = null): string` - Draft preview URL including stage/subsite params.
- `getAbsoluteLink($action = null): string` / `AbsoluteLink()` - Absolute stage URL to this block anchor.

#### Page relationships
- `getLinkedPagesList(): string` - Comma-separated linked page titles.
- `getAllPages(): array` - Unique pages (main + subsites) using this block.
- `getPagesFromSiteTree(): array` - Pages using this block in current context.
- `getPagesFromMainSite(): array` - Pages using this block in main site context.
- `getPagesFromSubsites(): array` - Pages using this block across all subsites.
- `getParentPage(): ?SiteTree` - The page being viewed on the front end.
- `getPage(): ?SiteTree` - The current page, if this block is on it.
- `getCMSParentPage(): ?SiteTree` - Current CMS parent page.
- `getCMSEditLink(): ?string` - Direct CMS edit URL for this block.
- `getCMSSiblingBlocks(): ?DataList` - Sibling blocks on the same page.
- `getCMSSiblingBlocksLinks(): ?string` - HTML list of sibling block links (rendered from `BlockSiblings.ss`).
- `isFirstBlock(): bool` / `isLastBlock(): bool`

#### Permissions and lifecycle
- `canView`, `canEdit`, `canDelete`, `canCreate` - CMS page access, with extension votes.
- `canDeleteFromLive`, `canPublish`, `canArchive`
- `isPublished(): bool`, `isNew(): bool`
- `doArchive(): bool` - Archive the block (keeping its history) and detach it from every page.

### Key protected methods for subclasses

- CMS helpers: `addItemsGridField(...)`, `getItemsGridField(...)`, `getSaveFirstNoticeField()`
- Layout config and filtering:
  - `getLayoutConfig()`, `getExcludedLayouts()`, `getLayoutConfigEntry(...)`, `isLayoutDisabled(...)`
  - `filterLayoutsByClassName(...)`, `filterToConfiguredLayouts(...)`, `filterDisabledLayouts(...)`, `filterLayoutTemplates(...)`
- Layout discovery:
  - `discoverLayouts()`, `discoverModuleLayouts()`, `discoverAdditionalLayouts()`, `scanTemplateDirectory(...)`, `getModuleTemplateDirectory()`
- Icon resolution:
  - `getConfiguredLayoutIcon(...)`, `getLayoutIconClass(...)`, `getDiscoveredLayoutIcon(...)`, `getLayoutIconSourcePath()`
  - `buildLayoutOption(...)`, `getLayoutOptionLabel(...)`
- Template fallback and warnings:
  - `resolveTemplateWithFallback(...)`, `getAvailableTemplateClasses(...)`, `isTemplateMissing()`, `getTemplateMissingNoticeField()`
- URL helpers:
  - `getCurrentCMSRecord()`, `getLinkParentPage()`, `buildBlockLinkFromParent(...)`, `appendQueryParams(...)`, `appendHash(...)`
- Permissions and versioning: `checkCMSPermission(...)`, `invokeVersioned(...)`, `detachFromAllPages(...)`

## Development

```sh
npm ci
npm run build   # or: npm run watch
```

Source scripts and styles live in `client/source`; the built files in `client/dist` are committed.
