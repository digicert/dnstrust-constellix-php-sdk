<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

class HttpRedirection extends RecordValue
{
    public bool $enabled = true;
    public bool $hard;
    public $redirectType;
    public $title;
    public $keywords;
    public $description;
    public $url;
}