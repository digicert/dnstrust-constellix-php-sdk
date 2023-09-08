<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Interfaces\Traits\EditableModelInterface;
use Constellix\Client\Managers\ContactListManager;
use Constellix\Client\Traits\EditableModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Contact List resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 * @property-read int $emailCount
 * @property \stdClass[] $emails
 */
class ContactList extends AbstractModel implements EditableModelInterface
{
    use EditableModel;
    use ManagedModel;

    protected ContactListManager $manager;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'name' => null,
        'emailCount' => null,
        'emails' => [],
    ];

    /**
     * @var string[]
     */
    protected array $editable = [
        'name',
        'emails',
    ];

    /**
     * Add an email to the contact list.
     * @param string $email
     * @return $this
     */
    public function addEmail(string $email): self
    {
        $obj = (object)[
            'address' => $email,
            'verified' => false,
        ];
        $this->addToCollection('emails', $obj);
        return $this;
    }

    /**
     * Remove an email from the contact list.
     * @param string $email
     * @return $this
     */
    public function removeEmail(string $email): self
    {
        $obj = (object)[
            'address' => $email,
            'verified' => false,
        ];
        $this->removeFromCollection('emails', $obj);
        $obj->verified = true;
        $this->removeFromCollection('emails', $obj);
        return $this;
    }

    /**
     * Transform this object and return a representation suitable for submitting to the API.
     * @return \stdClass
     * @internal
     */
    public function transformForApi(): \stdClass
    {
        $payload = parent::transformForApi();
        $payload->emails = [];
        foreach ($this->emails as $email) {
            $payload->emails[] = $email->address;
        }
        return $payload;
    }
}
