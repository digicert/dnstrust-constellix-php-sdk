<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Common;

use Constellix\Client\Managers\ContactListManager;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Contact List resource.
 * @package Constellix\Client\Models
 */
abstract class CommonContactList extends AbstractModel
{
    use ManagedModel;

    protected ContactListManager $manager;
}
