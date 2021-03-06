<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\Security\Twig;

use KejawenLab\ApiSkeleton\Security\Model\MenuInterface;
use KejawenLab\ApiSkeleton\Security\Service\MenuService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 */
final class MenuExtension extends AbstractExtension
{
    private Environment $twig;

    private RequestStack $requestStack;

    private UrlGeneratorInterface $urlGenerator;

    private MenuService $menuService;

    public function __construct(Environment $twig, RequestStack $requestStack, UrlGeneratorInterface $urlGenerator, MenuService $menuService)
    {
        $this->twig = $twig;
        $this->requestStack = $requestStack;
        $this->urlGenerator = $urlGenerator;
        $this->menuService = $menuService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('convert_to_menu', [$this, 'getMenu']),
            new TwigFunction('render_menu', [$this, 'renderMenu']),
            new TwigFunction('is_active_path', [$this, 'isActive']),
            new TwigFunction('is_menu_open', [$this, 'isOpen']),
        ];
    }

    public function isOpen(MenuInterface $menu): bool
    {
        if (!$this->menuService->hasChildMenu($menu)) {
            return $this->isActive($menu->getAdminPath());
        }

        $childs = $this->menuService->getChildsMenu($menu);
        foreach ($childs as $child) {
            if ($this->isActive($child->getAdminPath())) {
                return true;
            }
        }

        return false;
    }

    public function isActive(string $path): bool
    {
        return $this->requestStack->getCurrentRequest()->getPathInfo() === $path;
    }

    public function getMenu(string $code): ?MenuInterface
    {
        return $this->menuService->getMenuByCode($code);
    }

    public function renderMenu(): string
    {
        $html = '';
        /** @var MenuInterface[] $parentMenus */
        $parentMenus = $this->menuService->getParentMenu();
        foreach ($parentMenus as $menu) {
            if (!$this->menuService->hasChildMenu($menu)) {
                $html = sprintf('%s%s', $html, $this->twig->render('layout/child_menu.html.twig', ['menu' => $menu]));

                continue;
            }

            $childs = iterator_to_array($this->menuService->getChildsMenu($menu));
            $html = sprintf('%s%s', $html, $this->twig->render('layout/menu.html.twig', ['childs' => $childs, 'menu' => $menu]));
        }

        return $html;
    }
}
