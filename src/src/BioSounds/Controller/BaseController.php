<?php

namespace BioSounds\Controller;

use Twig\Environment;

class BaseController
{
    const SECTION_TITLE = '';
    const PAGE_TITLE = '%s - ecoSound-web';

    const SITE_SYMBOL_FOR_COLLECTIONS_QUERY_ALL = -2;

    protected $twig;

    /**
     * BaseController constructor.
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
        $this->twig->addGlobal('title', sprintf(self::PAGE_TITLE, static::SECTION_TITLE));
        $this->twig->addGlobal('section', static::SECTION_TITLE);
    }
}
