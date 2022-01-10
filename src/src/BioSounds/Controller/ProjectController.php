<?php

namespace BioSounds\Controller;

class ProjectController extends BaseController
{
    const TITLE = 'ecoSound - Project';

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
        return $this->twig->render('about.html.twig', ['title' => 'ecoSound - About']);
    }

    public function gsp()
    {
        return $this->twig->render('gsp.html.twig', ['title' => 'ecoSound - Global Soundscapes Project']);
    }
}
