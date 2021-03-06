<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\Admin\Controller\Setting;

use KejawenLab\ApiSkeleton\Entity\Setting;
use KejawenLab\ApiSkeleton\Form\SettingType;
use KejawenLab\ApiSkeleton\Security\Annotation\Permission;
use KejawenLab\ApiSkeleton\Setting\SettingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Permission(menu="SETTING", actions={Permission::ADD})
 *
 * @author Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 */
final class Post extends AbstractController
{
    private SettingService $service;

    public function __construct(SettingService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/settings/add", methods={"GET", "POST"}, priority=1)
     */
    public function __invoke(Request $request): Response
    {
        $setting = new Setting();
        $form = $this->createForm(SettingType::class, $setting);
        if ($request->isMethod(Request::METHOD_POST)) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->service->save($setting);

                $this->addFlash('info', 'sas.page.setting.saved');

                return new RedirectResponse($this->generateUrl('kejawenlab_apiskeleton_admin_setting_getall__invoke'));
            }
        }

        return $this->render('setting/form.html.twig', [
            'page_title' => 'sas.page.setting.add',
            'form' => $form->createView(),
        ]);
    }
}
