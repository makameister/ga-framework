<?php
namespace Framework\Renderer;

use Framework\Renderer\RendererInterface;

class PHPRenderer implements RendererInterface
{
    const DEFAULT_NAMESPACE = '__MAIN';

    private $paths = [];

    /**
     * Variables globalement accessibles pour toutes les vues
     * @var array
     */
    private $globals = [];

    public function __construct(?string $defaultPath = null)
    {
        if (!is_null($defaultPath)) {
            $this->addPath($defaultPath);
        }
    }

    /**
     * Allow to add route to load views
     * @param string $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        if (is_null($path)) {
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        } else {
            $this->paths[$namespace] = $path;
        }
    }

    /**
     * Allow to render a view
     * Le chemin peut être précisé avec des namespaces rajoutés via addPath()
     * $this->render('@blog/view');
     * $this->render('view');
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        if ($this->hasNamespace($view)) {
            $path = $this->replaceNamespace($view) . '.php';
        } else {
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        }
        ob_start();
        $renderer = $this;
        extract($this->globals);
        extract($params);
        require($path);
        return ob_get_clean();
    }

    /**
     * Permet de rajouter des variables globales à toutes les vues
     * @param string $key
     * @param mixed $value
     * @return Void
     */
    public function addGlobal(string $key, $value): Void
    {
        $this->globals[$key] = $value;
    }

    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') - 1);
    }

    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }
}
