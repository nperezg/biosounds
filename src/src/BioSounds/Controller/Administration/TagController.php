<?php

namespace BioSounds\Controller\Administration;

use BioSounds\Controller\BaseController;
use BioSounds\Provider\TagProvider;
use BioSounds\Utils\Auth;

class TagController extends BaseController
{
    const SECTION_TITLE = 'Tags';

    /**
     * @return string
     * @throws \Exception
     */
    public function show(int $page = 1)
    {
        if (!Auth::isUserLogged()) {
            throw new ForbiddenException();
        }

        $tagProvider = new TagProvider();

        $tagNum = $tagProvider->countTags();
        $pages = $tagNum > 0 ? ceil($tagNum / self::ITEMS_PAGE) : 1;

        return $this->twig->render('administration/tags.html.twig', [
            'tags' => $tagProvider->getTagPages(
                $this::ITEMS_PAGE,
                $this::ITEMS_PAGE * ($page - 1)
            ),
            'currentPage' => ($page > $pages) ?: $page,
            'pages' => $pages
        ]);
    }

    /**
     * @throws \Exception
     */
    public function export()
    {
        if (!Auth::isUserLogged()) {
            throw new ForbiddenException();
        }

        $file_name = "tags.csv";
        $fp = fopen('php://output', 'w');
        header('Content-Type: application/octet-stream;charset=utf-8');
        header('Accept-Ranges:bytes');
        header('Content-Disposition: attachment; filename=' . $file_name);

        $tagList = (new TagProvider())->getListByTags();

        foreach ($tagList as $tagItem) {
            $tagArray = array(
                $tagItem->getId(),
                $tagItem->getSpeciesName(),
                $tagItem->getRecordingName(),
                $tagItem->getUserName(),
                $tagItem->getTime(),
                $tagItem->getFrequency(),
                $tagItem->getCallDistance(),
                $tagItem->getNumberIndividuals(),
                $tagItem->getType(),
                $tagItem->getComments(),
                $tagItem->getCreationDate(),
            );
            $tagAls[] = $tagArray;
        }

        foreach ($tagAls as $line) {
            fputcsv($fp, $line);
        }
        fclose($fp);
        exit();
    }
}
