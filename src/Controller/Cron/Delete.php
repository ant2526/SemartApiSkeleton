<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\Controller\Cron;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use KejawenLab\ApiSkeleton\Cron\CronService;
use KejawenLab\ApiSkeleton\Cron\Model\CronInterface;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use Nelmio\ApiDocBundle\Annotation\Security;
use Noxlogic\RateLimitBundle\Annotation\RateLimit;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Permission(menu="CRON", actions={Permission::DELETE})
 *
 * @author Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 */
final class Delete extends AbstractFOSRestController
{
    private CronService $service;

    public function __construct(CronService $service)
    {
        $this->service = $service;
    }

    /**
     * @Rest\Delete("/cronjobs/{id}", priority=-7)
     *
     * @SWG\Tag(name="Cron")
     * @SWG\Response(
     *     response=204,
     *     description="Delete cron"
     * )
     *
     * @Security(name="Bearer")
     *
     * @RateLimit(limit=7, period=1)
     */
    public function __invoke(Request $request, string $id): View
    {
        $cron = $this->service->get($id);
        if (!$cron instanceof CronInterface) {
            throw new NotFoundHttpException(sprintf('Cron ID: "%s" not found', $id));
        }

        $this->service->remove($cron);

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
