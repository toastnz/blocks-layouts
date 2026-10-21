<?php

namespace Toast\Blocks\Extensions;

use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Core\Extension;
use SimpleXMLElement;

/**
 * Template helpers for files: download links, "PDF 1.2MB" style info and width/height attributes that
 * also work for SVGs.
 *
 * @extends Extension<File>
 */
class FileExtension extends Extension
{
    /**
     * E.g. "PDF 1.2MB"
     */
    public function getFileInfo(): string
    {
        $owner = $this->getOwner();

        return sprintf('%s %s', strtoupper((string) $owner->getExtension()), str_replace(' ', '', strtoupper((string) $owner->getSize())));
    }

    public function getDownloadLink(): string
    {
        return (string) $this->getOwner()->getURL();
    }

    public function DownloadLink(): string
    {
        return $this->getDownloadLink();
    }

    public function isSVG(?File $file = null): bool
    {
        $file ??= $this->getOwner();

        return strtolower((string) $file->getExtension()) === 'svg';
    }

    /**
     * @return array{width: int|string|null, height: int|string|null}
     */
    public function getDimensions(?File $file = null): array
    {
        $file ??= $this->getOwner();

        if (!$file || !$file->exists()) {
            return $this->emptyDimensions();
        }

        if ($this->isSVG($file)) {
            return $this->getSVGDimensionsFromString((string) $file->getString());
        }

        if ($file instanceof Image) {
            return [
                'width' => $file->getWidth(),
                'height' => $file->getHeight(),
            ];
        }

        return $this->emptyDimensions();
    }

    /**
     * Width and height of the SVG at the given filesystem path.
     *
     * @return array{width: string|null, height: string|null}
     */
    public function getSVGDimensions($path): array
    {
        if (!$path || !is_file($path)) {
            return $this->emptyDimensions();
        }

        return $this->getSVGDimensionsFromString((string) file_get_contents($path));
    }

    /**
     * Width and height from an SVG's width/height attributes, falling back to its viewBox.
     *
     * @return array{width: string|null, height: string|null}
     */
    public function getSVGDimensionsFromString(string $svgContent): array
    {
        if (trim($svgContent) === '') {
            return $this->emptyDimensions();
        }

        // Remove the XML declaration if present
        $svgContent = (string) preg_replace('/<\?xml.*?\?>/s', '', $svgContent);

        $previousErrors = libxml_use_internal_errors(true);

        try {
            $svg = new SimpleXMLElement($svgContent, LIBXML_NONET);
        } catch (\Exception $e) {
            return $this->emptyDimensions();
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousErrors);
        }

        $width = (string) $svg['width'];
        $height = (string) $svg['height'];

        if ((!$width || !$height) && isset($svg['viewBox'])) {
            $viewBox = preg_split('/[\s,]+/', trim((string) $svg['viewBox']));

            if (count($viewBox) === 4) {
                [, , $width, $height] = $viewBox;
            }
        }

        return [
            'width' => $width ?: null,
            'height' => $height ?: null,
        ];
    }

    /**
     * E.g. "width=120 height=40", for use inside an <img> tag.
     */
    public function getSizeAttr(): ?string
    {
        $dimensions = $this->getDimensions();

        if (!$dimensions['width'] || !$dimensions['height']) {
            return null;
        }

        return 'width=' . $dimensions['width'] . ' height=' . $dimensions['height'];
    }

    private function emptyDimensions(): array
    {
        return [
            'width' => null,
            'height' => null,
        ];
    }
}
