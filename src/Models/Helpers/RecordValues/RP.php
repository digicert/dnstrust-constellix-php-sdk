<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

class RP extends RecordValue
{
    public bool $enabled = true;
    public string $mailbox;
    public string $txt;
}
