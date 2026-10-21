<?php

namespace Toast\Blocks\Controllers;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\Middleware\HTTPCacheControlMiddleware;
use SilverStripe\Security\Permission;
use Toast\Blocks\Block;

/**
 * Renders a single block as JSON: `/blocks-api/getBlock?BlockID=<ID or anchor>`.
 *
 * CMS users can pass field values in the query string to preview unsaved changes; they are applied in
 * memory only and never written.
 */
class BlocksApiController extends Controller
{
    private static $allowed_actions = [
        'getBlock'
    ];

    private static $url_segment = 'blocks-api';

    /**
     * Fields that can never be overridden from the query string.
     *
     * @config
     */
    private static $protected_preview_fields = [
        'ID',
        'ClassName',
        'RecordClassName',
        'Version',
        'Created',
        'LastEdited',
    ];

    public function getBlock(HTTPRequest $request): HTTPResponse
    {
        $block = $this->findBlock((string) $request->getVar('BlockID'));

        if (!$block || !$block->exists()) {
            return $this->httpError(404, 'Block not found');
        }

        if (Permission::check('CMS_ACCESS')) {
            $this->applyPreviewValues($block, $request->getVars());
        }

        $data = [
            'template' => (string) $block->forTemplate(),
            'styles' => $this->getBlockStyles($block),
        ];

        $this->extend('updateBlockApiResponse', $data, $block);

        HTTPCacheControlMiddleware::singleton()->disableCache(true);

        return $this->getResponse()
            ->addHeader('Content-Type', 'application/json; charset=utf-8')
            ->addHeader('X-Robots-Tag', 'noindex')
            ->setBody((string) json_encode($data));
    }

    /**
     * Find a block by database ID or by its anchor ID (see Block::getBlockID()).
     */
    protected function findBlock(string $identifier): ?Block
    {
        if ($identifier === '') {
            return null;
        }

        if (ctype_digit($identifier)) {
            return Block::get()->byID((int) $identifier);
        }

        // Default anchors look like "TextBlock_12", so try the trailing ID first
        if (preg_match('/_(\d+)$/', $identifier, $matches)) {
            $block = Block::get()->byID((int) $matches[1]);

            if ($block && $block->getBlockID() === $identifier) {
                return $block;
            }
        }

        foreach (Block::get() as $block) {
            if ($block->getBlockID() === $identifier) {
                return $block;
            }
        }

        return null;
    }

    /**
     * Apply query string values to the block's own database fields, in memory only.
     */
    protected function applyPreviewValues(Block $block, array $values): void
    {
        $fieldSpecs = $block->getSchema()->fieldSpecs(get_class($block));
        $protectedFields = (array) $this->config()->get('protected_preview_fields');

        foreach ($values as $field => $value) {
            if (is_string($value) && isset($fieldSpecs[$field]) && !in_array($field, $protectedFields, true)) {
                $block->setField($field, $value);
            }
        }
    }

    /**
     * Contents of the block's layout stylesheet, if it has one.
     */
    protected function getBlockStyles(Block $block): string
    {
        $cssFile = $block->getCSSFile();

        if (!$cssFile) {
            return '';
        }

        return (string) file_get_contents(BASE_PATH . '/' . $cssFile);
    }
}
