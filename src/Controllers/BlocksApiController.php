<?php

namespace Toast\Blocks\Controllers;

use Toast\Blocks\Block;
use SilverStripe\Control\Director;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Security\Permission;

class BlocksApiController extends Controller
{
    private static $allowed_actions = [
        'getBlock'
    ];

    private static $url_segment = 'blocks-api';

    public function getBlock(HTTPRequest $request)
    {
        $this->getResponse()->addHeader('Content-Type', 'text/html');
        $this->getResponse()->addHeader('X-Robots-Tag', 'noindex');
        $this->getResponse()->addHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $this->getResponse()->addHeader('Cache-Control', 'post-check=0, pre-check=0', false);
        $this->getResponse()->addHeader('Pragma', 'no-cache');

        $baseFolder = Director::baseFolder();

        if ($blockID = $request->getVar('BlockID')) {
            // Try get the block by ID
            if ($contentBlock = Block::get()->byID($blockID)) {
                $class = $contentBlock->ClassName;
                $contentBlock = $class::get()->byID($blockID);
            }

            // Otherwise try use the getBlockID method to get the block
            else {
                $blocks = Block::get();
                $contentBlock = null;

                foreach ($blocks as $block) {
                    if ($block->getBlockID() == $blockID) {
                        $contentBlock = $block;
                        $class = $contentBlock->ClassName;
                        $contentBlock = $class::get()->byID($contentBlock->ID);
                        break;
                    }
                }
            }

            if ($contentBlock && $contentBlock->exists()) {
                if (Permission::check('CMS_ACCESS')) {
                    if ($blockVars = (Config::inst()->get($class, 'db'))) {
                        $blockVars = array_keys($blockVars);
                        foreach($request->getVars() as $key => $value) {
                            if (is_string($value)) {
                                $contentBlock->$key = urldecode($value);
                            }
                        }
                    }
                }

                $template = (string)$contentBlock->forTemplate();

                // Call the $contentBlock->getCSSFile() method and get the file contents
                if (method_exists($contentBlock, 'getCSSFile')) {
                    $cssFile = $contentBlock->getCSSFile();
                    if ($cssFile) {
                        // Get the file contents and add it to the template
                        $styles = file_get_contents($baseFolder . '/' . $cssFile);
                    }
                }

                // Return the template and styles in a JSON object
                return json_encode([
                    'template' => $template,
                    'styles' => $styles ?? ''
                ]);
            }
        }

        return $this->httpError(404, 'Block not found');
    }
}
