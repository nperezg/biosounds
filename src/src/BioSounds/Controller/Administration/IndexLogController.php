<?php

namespace BioSounds\Controller\Administration;

use BioSounds\Controller\BaseController;
use BioSounds\Exception\ForbiddenException;
use BioSounds\Provider\IndexLogProvider;
use BioSounds\Utils\Auth;

class IndexLogController extends BaseController
{
    const SECTION_TITLE = 'IndexLogs';

    /**
     * @return string
     * @throws \Exception
     */
    public function show(int $page = 1)
    {
        if (!Auth::isUserLogged()) {
            throw new ForbiddenException();
        }
        $indexLogProvider = new IndexLogProvider();
        $indexLogNum = $indexLogProvider->countIndexLogs();
        $pages = $indexLogNum > 0 ? ceil($indexLogNum / self::ITEMS_PAGE) : 1;
        return $this->twig->render('administration/indexLogs.html.twig', [
            'indexLogs' => $indexLogProvider->getIndexLogPages(
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

        $file_name = "indexLogs.csv";
        $fp = fopen('php://output', 'w');
        header('Content-Type: application/octet-stream;charset=utf-8');
        header('Accept-Ranges:bytes');
        header('Content-Disposition: attachment; filename=' . $file_name);

        $indexLogList = (new IndexLogProvider())->getList();
        $indexLogAls[] = array('#', 'Recording', 'Creation User', 'Index', 'Coordinates', 'Parameter', 'Result', 'Creation Date (UTC)');

        foreach ($indexLogList as $indexLogItem) {
            $value = '';
            foreach (explode('!', $indexLogItem->getValue()) as $v) {
                $value = $value . explode('?', $v)[0] . ': ' . number_format(explode('?', $v)[1], 2, '.', ',') . '; ';
            }
            $indexLogArray = array(
                $indexLogItem->getLogId(),
                $indexLogItem->getRecordingName(),
                $indexLogItem->getUserName(),
                $indexLogItem->getIndexName(),
                $indexLogItem->getCoordinates(),
                str_replace('@', '; ', str_replace('?', ': ', $indexLogItem->getParam())),
                $value,
                $indexLogItem->getDate(),
            );
            $indexLogAls[] = $indexLogArray;
        }

        foreach ($indexLogAls as $line) {
            fputcsv($fp, $line);
        }
        fclose($fp);
        exit();
    }
}
