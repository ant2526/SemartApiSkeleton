<?php

declare(strict_types=1);

namespace Alpabit\ApiSkeleton\Service;

use Alpabit\ApiSkeleton\Messenger\Message\EntityMessage;
use Alpabit\ApiSkeleton\Pagination\AliasHelper;
use Alpabit\ApiSkeleton\Service\Model\ServiceableRepositoryInterface;
use Alpabit\ApiSkeleton\Service\Model\ServiceInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.iksanudin@alpabit.com>
 */
abstract class AbstractService implements ServiceInterface
{
    private $messageBus;

    protected $repository;

    protected $aliasHelper;

    public function __construct(MessageBusInterface $messageBus, ServiceableRepositoryInterface $repository, AliasHelper $aliasHelper)
    {
        $this->messageBus = $messageBus;
        $this->repository = $repository;
        $this->aliasHelper = $aliasHelper;
    }

    public function get(string $id)
    {
        return $this->repository->find($id);
    }

    public function save(object $object): void
    {
        $this->messageBus->dispatch(new EntityMessage($object));
    }

    public function remove(object $object): void
    {
        $this->messageBus->dispatch(new EntityMessage($object, EntityMessage::ACTION_REMOVE));
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->repository->queryBuilder($this->aliasHelper->findAlias('root'));
    }
}
