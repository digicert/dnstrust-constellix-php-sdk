<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Interfaces\Models\ContactListInterface;
use Constellix\Client\Interfaces\Traits\EditableModelInterface;
use Constellix\Client\Models\Common\CommonContactList;
use Constellix\Client\Traits\EditableModel;

/**
 * Represents a Contact List resource.
 * @package Constellix\Client\Models
 *
 * @property string[] $emails
 */
class ContactList extends CommonContactList implements ContactListInterface, EditableModelInterface
{
    use EditableModel;

    protected array $props = [
        'name' => null,
        'emailCount' => null,
        'emails' => [],
    ];

    protected array $editable = [
        'name',
        'emails',
    ];

    protected function hasEmail(string $address): bool
    {
        foreach ($this->emails as $email) {
            if ($email->address == $address) {
                return true;
            }
        }
        return false;
    }

    public function addEmail(string $email): self
    {
        if (!$this->hasEmail($email)) {
            $emails = $this->emails;
            $emails[] = (object) [
                'address' => $email,
                'verified' => false,
            ];
            $this->emails = $emails;
        }
        return $this;
    }

    public function removeEmail(string $email): self
    {
        $emails = $this->emails;
        foreach ($emails as $index => $value) {
            if ($value->address === $email) {
                unset($emails[$index]);
                $this->emails = $emails;
                return $this;
            }
        }
        return $this;
    }

    public function transformForApi(): object
    {
        $payload = parent::transformForApi();
        $payload->emails = [];
        foreach ($this->emails as $email) {
            $payload->emails[] = $email->address;
        }
        return $payload;
    }
}