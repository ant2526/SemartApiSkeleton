<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\Admin\Controller\Menu;

use KejawenLab\ApiSkeleton\Form\MenuType;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\ApiSkeleton\Security\Model\MenuInterface;
use KejawenLab\ApiSkeleton\Security\Service\MenuService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="MENU", actions={Permission::EDIT})
 *
 * @author Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 */
final class Put extends AbstractController
{
    private MenuService $service;

    public function __construct(MenuService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/menus/{id}/edit", methods={"GET", "POST"}, priority=1)
     */
    public function __invoke(Request $request, string $id): Response
    {
        $menu = $this->service->get($id);
        if (!$menu instanceof MenuInterface) {
            $this->addFlash('error', 'sas.page.menu.not_found');

            return new RedirectResponse($this->generateUrl('kejawenlab_apiskeleton_admin_menu_getall__invoke'));
        }

        $form = $this->createForm(MenuType::class, $menu);
        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->service->save($menu);

                $this->addFlash('info', 'sas.page.menu.saved');

                return new RedirectResponse($this->generateUrl('kejawenlab_apiskeleton_admin_menu_getall__invoke'));
            }
        }

        return $this->render('menu/form.html.twig', [
            'page_title' => 'sas.page.menu.edit',
            'form' => $form->createView(),
        ]);
    }
}
