<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

class HttpRedirection extends RecordValue
{
    public bool $enabled = true;
    public bool $hard;
    public int $redirectType;
    public ?string $title;
    public ?string $keywords;
    public ?string $description;
    public string $url;
}
