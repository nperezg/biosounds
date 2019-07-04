<?php

namespace BioSounds\Controller;

class BaseController
{
    const SECTION_TITLE = '';
    const PAGE_TITLE = '%s - BioSounds';

    protected $twig;

    /**
     * BaseController constructor.
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
        $this->twig->addGlobal('title', sprintf(self::PAGE_TITLE, static::SECTION_TITLE));
        $this->twig->addGlobal('section', static::SECTION_TITLE);
    }
}
