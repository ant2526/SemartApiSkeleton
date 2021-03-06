<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\ApiClient\Model;

use KejawenLab\ApiSkeleton\Security\Model\AuthInterface;
use KejawenLab\ApiSkeleton\Security\Model\UserInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 */
interface ApiClientInterface extends AuthInterface
{
    public function getUser(): ?UserInterface;

    public function getName(): ?string;

    public function getApiKey(): ?string;

    public function getSecretKey(): ?string;
}
