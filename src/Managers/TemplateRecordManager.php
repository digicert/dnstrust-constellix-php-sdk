<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Interfaces\Traits\TemplateAwareInterface;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Models\TemplateRecord;
use Constellix\Client\Traits\TemplateAware;

/**
 * Manages template record resources.
 * @package Constellix\Client\Managers
 */
class TemplateRecordManager extends AbstractManager implements TemplateAwareInterface
{
    use TemplateAware;

    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/templates/:template_id/records';

    public function create(): TemplateRecord
    {
        return $this->createObject();
    }

    public function get(int $id): TemplateRecord
    {
        return $this->getObject($id);
    }

    protected function getBaseUri(): string
    {
        return str_replace(':template_id', (string)$this->template->id, $this->baseUri);
    }

    protected function createObject(?string $className = null): AbstractModel
    {
        $object = new TemplateRecord($this, $this->client);
        $object->setTemplate($this->template);
        return $object;
    }
}
