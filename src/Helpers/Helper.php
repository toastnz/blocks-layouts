<?php

namespace Toast\Blocks\Helpers;

use SilverStripe\Subsites\Model\Subsite;

class Helper
{
    static function getBlockPageLinksHTMLForCMS($block = null)
    {
        $pages = $block->getAllPages();

        // Generate HTML for the list of links
        $linksHtml = '<div class="blocks-layouts-page-links">';

        // Group all the pages by subsiteID
        $groupedPages = [];

        if ($pages) {

            // Check if the Subsites module is installed
            if (class_exists(Subsite::class)) {
                // Group pages by subsite
                $groupedPages = [];

                foreach ($pages as $page) {
                    $subsiteID = $page->SubsiteID ?: 0; // Use 0 for main site pages
                    if (!isset($groupedPages[$subsiteID])) {
                        $groupedPages[$subsiteID] = [];
                    }
                    $groupedPages[$subsiteID][] = $page;
                }

                foreach ($groupedPages as $subsiteID => $subsitePages) {
                    // Get the subsite title
                    if ($subsiteID == 0) {
                        $subsiteTitle = 'Main Site';
                    } else {
                        $subsite = Subsite::get()->byID($subsiteID);
                        $subsiteTitle = $subsite ? $subsite->Title : 'Unknown Subsite';
                    }

                    $linksHtml .= '<h4>' . $subsiteTitle . '</h4>';

                    foreach ($subsitePages as $page) {
                        // Get the icon class for the page
                        $iconClass = $page->config()->get('icon_class');

                        // Construct the HTML with the icon class and link
                        $linksHtml .= '<div class="blocks-layouts-page-links__item"><i class="' . $iconClass . '"></i><a href="' . $page->CMSEditLink() . '">' . $page->Title . '</a></div>';
                    }
                }
            } else {
                foreach ($pages as $page) {
                    // Get the icon class for the page
                    $iconClass = $page->config()->get('icon_class');

                    // Construct the HTML with the icon class and link
                    $linksHtml .= '<div class="blocks-layouts-page-links__item"><i class="' . $iconClass . '"></i><a href="' . $page->CMSEditLink() . '">' . $page->Title . '</a></div>';
                }
            }
        } else {
            $linksHtml .= '<p class="message warning" style="margin-bottom: 0;">No pages are using this block.</p>';
        }

        $linksHtml .= '</div>';

        return $linksHtml;
    }
}
