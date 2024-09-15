<?php

namespace App\Support;

use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class TenantAwareUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        $url = asset('storage/'.$this->getPathRelativeToRoot());

        $url = $this->versionUrl($url);

        return $url;
    }
}
