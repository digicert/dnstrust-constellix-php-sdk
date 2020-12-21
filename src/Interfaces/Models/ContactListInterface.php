<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models;

use Constellix\Client\Interfaces\Models\Common\CommonContactListInterface;

/**
 * Represents a Contact List resource
 * @package Constellix\Client\Interfaces
 *
 * @property string[] $emails
 */
interface ContactListInterface extends CommonContactListInterface
{
    public function addEmail(string $email): self;
    public function removeEmail(string $email): self;
}