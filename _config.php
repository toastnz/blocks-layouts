<?php

/**
 * Legacy path constants.
 *
 * These are kept for backwards compatibility with site code that references them. Internally the
 * module now resolves its own location through the module manifest, so it no longer assumes it is
 * installed at vendor/toastnz/<dir>.
 */
if (!defined('TOAST_BLOCKS_IMAGE_DIR')) {
    define('TOAST_BLOCKS_IMAGE_DIR', 'vendor/toastnz/' . basename(__DIR__) . '/client/images/layout-icons/default/');
}

if (!defined('TOAST_BLOCKS_DIR')) {
    define('TOAST_BLOCKS_DIR', 'vendor/toastnz/' . basename(__DIR__));
}

if (!defined('TOAST_RESOURCES_DIR')) {
    define('TOAST_RESOURCES_DIR', RESOURCES_DIR . '/vendor');
}

if (!defined('TOAST_BLOCKS_TEMPLATE_DIR')) {
    define('TOAST_BLOCKS_TEMPLATE_DIR', 'templates/Toast/Blocks/Default');
}

if (!defined('TOAST_DEFAULT_DIR')) {
    define('TOAST_DEFAULT_DIR', '/default/');
}
