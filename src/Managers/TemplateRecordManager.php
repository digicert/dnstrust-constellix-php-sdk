<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Interfaces\Traits\TemplateAwareInterface;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Models\TemplateRecord;
use Constellix\Client\Traits\HasPagination;
use Constellix\Client\Traits\TemplateAware;

/**
 * Manages template record resources.
 * @package Constellix\Client\Managers
 */
class TemplateRecordManager extends AbstractManager implements TemplateAwareInterface
{
    use TemplateAware;
    use HasPagination;

    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/templates/:template_id/records';

    /**
     * Create a new Template Record.
     * @return TemplateRecord
     */
    public function create(): TemplateRecord
    {
        /**
         * @var TemplateRecord $object
         */
        $object = $this->createObject();
        return $object;
    }

    /**
     * Fetch an existing Template Record.
     * @param int $id
     * @return TemplateRecord
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\Client\ModelNotFoundException
     * @throws \ReflectionException
     */
    public function get(int $id): TemplateRecord
    {
        /**
         * @var TemplateRecord $object
         */
        $object = $this->getObject($id);
        return $object;
    }

    /**
     * Get the base URI representing this Template Record.
     * @return string
     */

    protected function getBaseUri(): string
    {
        return str_replace(':template_id', (string)$this->template->id, $this->baseUri);
    }

    /**
     * Instantiate a new Template Record.
     * @param string|null $className
     * @return AbstractModel
     */
    protected function createObject(?string $className = null): AbstractModel
    {
        $object = new TemplateRecord($this, $this->client);
        $object->setTemplate($this->template);
        return $object;
    }
}
