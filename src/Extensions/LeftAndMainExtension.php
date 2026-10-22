<?php

namespace Toast\Blocks\Extensions;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Core\Extension;

/**
 * The CMS styles and scripts are now registered through `LeftAndMain.extra_requirements_css` and
 * `extra_requirements_javascript` in _config/config.yml, so this extension no longer does anything.
 *
 * @deprecated Remove this extension from your config; it will be deleted in a future release.
 * @extends Extension<LeftAndMain>
 */
class LeftAndMainExtension extends Extension
{
}
