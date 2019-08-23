<?php
namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Psr\Http\Message\ServerRequestInterface as Request;
use Framework\Renderer\RendererInterface;

class CategoryShowAction
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var PostTable
     */
    private $postTable;

    /**
     * @var CategoryTable
     */
    private $categoryTable;

    use RouterAwareAction;

    /**
     * BlogAction constructor.
     * @param RendererInterface $renderer
     * @param PostTable $postTable
     * @param CategoryTable $categoryTable
     */
    public function __construct(RendererInterface $renderer, PostTable $postTable, CategoryTable $categoryTable)
    {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->categoryTable = $categoryTable;
    }

    public function __invoke(Request $request)
    {
        $params = $request->getQueryParams();
        $category = $this->categoryTable->findBy('slug', $request->getAttribute('slug'));
        $posts =  $this->postTable->findPaginatedPublicForCategory(12, $params['p'] ?? 1, $category->id);
        $categories = $this->categoryTable->findAll();
        $page = $params['p'] ?? 1;

        return $this->renderer->render('@blog/index', compact('posts', 'categories', 'category', 'page'));
    }
}