<?php

namespace Toast\Blocks\Extensions;

use SimpleXMLElement;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Core\Extension;
use SilverStripe\Control\Director;

class FileExtension extends Extension
{
    public function getFileInfo()
    {
        return sprintf("%s %s", strtoupper($this->owner->getExtension()), str_replace(' ', '', strtoupper($this->owner->getSize())));
    }

    public function getDownloadLink()
    {
        return $this->owner->URL();
    }

    public function DownloadLink()
    {
        return $this->getDownloadLink();
    }

    public function isSVG($file)
    {

        return $file->getExtension() === 'svg';
    }

    public function getDimensions(File $file)
    {
        if ($file && $file->exists()) {
            if ($this->isSVG($file)) {
                $filePath = $file->URL;
                $fullFilePath = Director::publicFolder() . $filePath;
                return $this->getSVGDimensions($fullFilePath);
            }

            if ($file instanceof Image) {
                return [
                    'width' => $file->getWidth(),
                    'height' => $file->getHeight(),
                ];
            }
        }

        return [
            'width' => null,
            'height' => null,
        ];
    }

    public function getSVGDimensions($path)
    {

        if ($path) {
            $svgContent = file_get_contents($path);
            // Remove XML declaration if present
            $svgContent = preg_replace('/<\?xml.*?\?>/', '', $svgContent);

            try {
                $svg = new SimpleXMLElement($svgContent);

                $width = (string)$svg['width'];
                $height = (string)$svg['height'];

                if (!$width || !$height) {
                    if (isset($svg['viewBox'])) {
                        $viewBox = explode(' ', (string)$svg['viewBox']);
                        if (count($viewBox) === 4) {
                            $width = $viewBox[2];
                            $height = $viewBox[3];
                        }
                    }
                }

                return [
                    'width' => $width,
                    'height' => $height,
                ];
            } catch (Exception $e) {
                // Handle error if SVG parsing fails
                return [
                    'width' => null,
                    'height' => null,
                ];
            }
        }

        return [
            'width' => null,
            'height' => null,
        ];
    }

    public function getSizeAttr()
    {
        $dimensions = $this->getDimensions($this->owner);
        if ($dimensions['width'] && $dimensions['height']) {
            return 'width=' . $dimensions['width'] . ' height=' . $dimensions['height'];
        }
    }
}
