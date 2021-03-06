<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\Admin\Controller\Group;

use KejawenLab\ApiSkeleton\Entity\Permission as Entity;
use KejawenLab\ApiSkeleton\Pagination\Paginator;
use KejawenLab\ApiSkeleton\Security\Annotation as Semart;
use KejawenLab\ApiSkeleton\Security\Model\GroupInterface;
use KejawenLab\ApiSkeleton\Security\Service\GroupService;
use KejawenLab\ApiSkeleton\Security\Service\PermissionService;
use KejawenLab\ApiSkeleton\Util\StringUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Semart\Permission(menu="GROUP", actions=Semart\Permission::VIEW)
 *
 * @author Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 */
final class Permission extends AbstractController
{
    private PermissionService $service;

    private GroupService $groupService;

    private Paginator $paginator;

    public function __construct(PermissionService $service, GroupService $groupService, Paginator $paginator)
    {
        $this->service = $service;
        $this->groupService = $groupService;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/groups/{id}/permissions", methods={"GET"})
     */
    public function __invoke(Request $request, string $id): Response
    {
        $group = $this->groupService->get($id);
        if (!$group instanceof GroupInterface) {
            $this->addFlash('error', 'sas.page.group.not_found');

            return new RedirectResponse($this->generateUrl('kejawenlab_apiskeleton_admin_group_getall__invoke'));
        }

        $class = new \ReflectionClass(Entity::class);

        return $this->render('group/permission.html.twig', [
            'page_title' => 'sas.page.permission.list',
            'group' => $group,
            'context' => StringUtil::lowercase($class->getShortName()),
            'properties' => $class->getProperties(\ReflectionProperty::IS_PRIVATE),
            'paginator' => $this->paginator->paginate($this->service->getQueryBuilder(), $request, Entity::class),
        ]);
    }
}
