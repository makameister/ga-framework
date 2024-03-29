<?php
namespace App\Admin;

class AdminTwigExtension extends \Twig_Extension
{
    /**
     * @var array
     */
    private $widgets;

    public function __construct(array $widgets)
    {
        $this->widgets = $widgets;
    }

    public function getFunctions(): array
    {
        return [
            new \twig_simpleFunction('admin_menu', [$this, 'renderMenu'], ['is_safe' => ['html']])
        ];
    }

    public function renderMenu(): string
    {
        return array_reduce($this->widgets, function ($html, AdminWidgetInterface $widget) {
            return $html . $widget->renderMenu();
        }, '');
    }
}
