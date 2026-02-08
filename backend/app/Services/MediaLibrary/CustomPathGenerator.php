<?php

namespace App\Services\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media).'conversions/';
    }

    public function getPath(Media $media): string
    {
        return md5($media->uuid.$media->model_id).'/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media).'/responsive-images/';
    }
}
