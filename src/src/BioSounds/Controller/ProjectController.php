<?php

namespace BioSounds\Controller;

class ProjectController extends BaseController
{
    const TITLE = 'BioSounds - Project';

    /**
     * @return string
     * @throws \Exception
     */
    public function show()
    {
        return $this->twig->render('project.html.twig', [
            'title' => self::TITLE,
        ]);
    }

    public function about()
    {
        return $this->twig->render('about.html.twig');
    }

    public function gsp()
    {
        return $this->twig->render('gsp.html.twig');
    }
}
