<?php

namespace Toast\Blocks\Helpers;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Subsites\Model\Subsite;

class Helper
{
    static function getBlockPageLinksHTMLForCMS($block = null)
    {
        $pages = $block->getAllPages();

        $html = '<div class="blocks-layouts-page-links">';

        if (!$pages) {
            $html .= '<p class="message warning" style="margin-bottom: 0;">No pages are using this block.</p>';
            $html .= '</div>';
            return $html;
        }

        if (class_exists(Subsite::class)) {
            $grouped = [];
            foreach (SiteTree::get()->filter('ID', $pages) as $page) {
                $grouped[$page->SubsiteID ?: 0][] = $page;
            }

            foreach ($grouped as $subsiteID => $subsitePages) {
                $subsiteTitle = $subsiteID === 0
                    ? 'Main Site'
                    : (($subsite = Subsite::get()->byID($subsiteID)) ? $subsite->Title : 'Unknown Subsite');

                $html .= '<h4>' . $subsiteTitle . '</h4>';
                foreach ($subsitePages as $page) {
                    $html .= self::renderPageLink($page);
                }
            }
        } else {
            foreach ($pages as $page) {
                $html .= self::renderPageLink($page);
            }
        }

        $html .= '</div>';

        return $html;
    }

    private static function renderPageLink($page): string
    {
        $iconClass = $page->config()->get('cms_icon_class');
        return '<div class="blocks-layouts-page-links__item"><i class="' . $iconClass . '"></i><a href="' . $page->CMSEditLink() . '">' . $page->Title . '</a></div>';
    }
}
