<?php
namespace Framework\Renderer;

use Psr\Container\containerInterface;
use Twig\Extension\DebugExtension;

class TwigRendererFactory
{
    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        $debug = $container->get('env') !== 'production';
        $viewPath = $container->get('views.path');
        $loader = new \Twig_Loader_Filesystem($viewPath);
        $twig = new \Twig_Environment($loader, [
            'debug' => true,
            'cache' => $debug ? false : 'tmp/views',
            'auto_reload' => $debug
        ]);
        $twig->addExtension(new DebugExtension());
        if ($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }
        return new TwigRenderer($loader, $twig);
    }
}
