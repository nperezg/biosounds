<?php

namespace Hybridars\BioSounds\Classes;

use Hybridars\BioSounds\Exception\RenderException;

class BaseController
{
    private $twig;

    /**
     * BaseController constructor.
     */
    public function __construct()
    {
        $loader = new \Twig_Loader_Filesystem(TEMPLATES_DIR);
        $this->twig = new \Twig_Environment($loader, array(
            'cache' => CACHE_DIR,
        ));
    }

    /**
     * @param string $template
     * @param array $vars
     * @return string
     * @throws RenderException
     */
    protected function render(string $template, array $vars = []) : string
    {
        try {
            return $this->twig->render($template, $vars);
        } catch(\Exception $exception){
            throw new RenderException($exception->getMessage(), $exception->getCode());
        }
    }
}