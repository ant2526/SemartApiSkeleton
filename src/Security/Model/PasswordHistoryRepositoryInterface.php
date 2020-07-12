<?php

declare(strict_types=1);

namespace Alpabit\ApiSkeleton\Security\Model;

use Alpabit\ApiSkeleton\Service\Model\ServiceableRepositoryInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 */
interface PasswordHistoryRepositoryInterface extends ServiceableRepositoryInterface
{
    public function findPassword(UserInterface $user): array;
}