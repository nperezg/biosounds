<?php

namespace BioSounds\Controller\Administration;

use BioSounds\Controller\BaseController;
use BioSounds\Provider\CollectionProvider;
use BioSounds\Utils\Auth;

class CollectionController extends BaseController
{
    const SECTION_TITLE = 'Collections';

    /**
     * @return string
     * @throws \Exception
     */
    public function show()
    {
        if (!Auth::isUserAdmin()) {
            throw new \Exception(ERROR_NO_ADMIN);
        }

        return $this->twig->render('administration/collections.html.twig', [
            'collections' => (new CollectionProvider())->getListOrderById(),
        ]);
    }
}
