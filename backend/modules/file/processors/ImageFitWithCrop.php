<?php

namespace steroids\modules\file\processors;

/**
 * Class ImageFit
 *
 * Fits an image to the given sizes.
 * The difference between ImageResize processor is that in ImageFit only larger side is resized to fit it's size,
 * while smaller side is cropped to fit it's size.
 *
 * @package steroids\modules\file\processors
 */
class ImageFitWithCrop extends BaseFileProcessor
{
    /**
     * @var int
     */
    public $width;

    /**
     * @var int
     */
    public $height;

    /**
     * @var int
     */
    public $thumbQuality = 90;

    protected function getSizesAndScales()
    {
        $imageContent = file_get_contents($this->filePath);

        // New size
        list($originalWidth, $originalHeight) = getimagesizefromstring($imageContent);

        $scaleX = $this->width / $originalWidth;
        $scaleY = $this->height / $originalHeight;
        $maxScale = max($scaleX, $scaleY);
        $minScale = min($scaleX, $scaleY);

        return [$scaleX, $maxScale, $minScale, $originalWidth, $originalHeight];
    }

    protected function runInternal()
    {
        list($scaleX, $maxScale, $minScale, $originalWidth, $originalHeight) = $this->getSizesAndScales();

        // If the given image is smaller than the given sizes, then do not modify the image
        if ($maxScale >= 1 && $minScale >= 1) {
            $this->width = $originalWidth;
            $this->height = $originalHeight;
            return;
        }

        // If the image is smaller than the given sizes, then resize it so that at least one side would fit the given size
        if ($maxScale < 1 && $minScale < 1) {
            $resizeProcessor = new ImageResize([
                'filePath' => $this->filePath,
                'width' => $this->width,
                'height' => $this->height,
                'thumbQuality' => $this->thumbQuality,
                'isFit' => false,
            ]);
            $resizeProcessor->run();

            list($scaleX, $maxScale, $minScale, $originalWidth, $originalHeight) = $this->getSizesAndScales();
        }

        // If one of the image sides is larger than the given sizes but another one is not, then crop
        // the side that exceeds the given size
        if ($maxScale >= 1 && $minScale < 1) {
            // Decide if width or height should be cropped
            $doCropWidth = $minScale === $scaleX;

            if ($doCropWidth) {
                $cropWidth = $this->width;
                $cropHeight = $originalHeight;
                $offsetX = round(($originalWidth - $cropWidth) / 2);
                $offsetY = 0;
            } else {
                $cropWidth = $originalWidth;
                $cropHeight = $this->height;
                $offsetX = 0;
                $offsetY = round(($originalHeight - $cropHeight) / 2);
            }

            $cropProcessor = new ImageCrop([
                'filePath' => $this->filePath,
                'width' => $cropWidth,
                'height' => $cropHeight,
                'thumbQuality' => $this->thumbQuality,
                'offsetX' => $offsetX,
                'offsetY' => $offsetY,
            ]);
            $cropProcessor->run();
        }
    }

}