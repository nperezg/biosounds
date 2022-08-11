<?php

namespace BioSounds\Controller\Administration;

use BioSounds\Controller\BaseController;
use BioSounds\Entity\Explore;
use BioSounds\Entity\Sensor;
use BioSounds\Entity\Site;
use BioSounds\Exception\ForbiddenException;
use BioSounds\Provider\SiteProvider;
use BioSounds\Utils\Auth;


class SiteController extends BaseController
{
    const SECTION_TITLE = 'Site';

    /**
     * @return false|string
     * @throws \Exception
     */
    public function show(int $page = 1)
    {
        if (!Auth::isUserAdmin()) {
            throw new ForbiddenException();
        }
        // echo Utils::getSetting('license');

        $siteProvider = new SiteProvider();

        $siteNum = $siteProvider->countSites();
        $pages = $siteNum > 0 ? ceil($siteNum / self::ITEMS_PAGE) : 1;
        return $this->twig->render('administration/sites.html.twig', [
            'explores' => (new Explore())->getAllExplores(),
            'siteList' => $siteProvider->getSitePages(
                $this::ITEMS_PAGE,
                $this::ITEMS_PAGE * ($page - 1)
            ),
            'currentPage' => ($page > $pages) ?: $page,
            'pages' => $pages
        ]);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function save()
    {
        $siteEnt = new Site();

        if (!Auth::isUserAdmin()) {
            throw new ForbiddenException();
        }

        $data = [];
        $sitePdoValue = '';
        foreach ($_POST as $key => $value) {
            if (strrpos($key, '_')) {
                $type = substr($key, strrpos($key, '_') + 1, strlen($key));
                $key = substr($key, 0, strrpos($key, '_'));
            }
            $sitePdoValue = $value;
            if ($sitePdoValue != '0' && empty($sitePdoValue)) {
                $sitePdoValue = '';
            }

            switch ($key) {
                case 'realm_id':
                    $data['realm_id'] = $sitePdoValue == '' ? 0 : $sitePdoValue;
                    break;
                case 'biome_id':
                    $data['biome_id'] = $sitePdoValue == '' ? 0 : $sitePdoValue;
                    break;
                case 'functional_group_id':
                    $data['functional_group_id'] = $sitePdoValue == '' ? 0 : $sitePdoValue;
                    break;
                case 'longitude':
                    $data['longitude_WGS84_dd_dddd'] = filter_var($sitePdoValue, FILTER_SANITIZE_STRING);
                    break;
                case 'latitude':
                    $data['latitude_WGS84_dd_dddd'] = filter_var($sitePdoValue, FILTER_SANITIZE_STRING);
                    break;
                default:
                    $data[$key] = filter_var($sitePdoValue, FILTER_SANITIZE_STRING);
            }
        }

        if (isset($data['steId'])) {
            $siteEnt->update($data);
            return json_encode([
                'errorCode' => 0,
                'message' => 'Site updated successfully.',
            ]);
        } else {
            $data['creation_date_time'] = date('Y-m-d H:i:s', time());
            $data['user_id'] = $_SESSION['user_id'];

            if ($siteEnt->insert($data) > 0) {
                return json_encode([
                    'errorCode' => 0,
                    'message' => 'Site created successfully.',
                ]);
            }
        }
    }


    /**
     * @param int $id
     * @return false|string
     * @throws \Exception
     */
    public function delete(int $id)
    {
        if (!Auth::isUserAdmin()) {
            throw new ForbiddenException();
        }

        if (empty($id)) {
            throw new \Exception(ERROR_EMPTY_ID);
        }

        $siteProvider = new SiteProvider();
        $siteProvider->delete($id);

        return json_encode([
            'errorCode' => 0,
            'message' => 'Site deleted successfully.',
        ]);
    }

    public function getExplore(int $pid = 0)
    {
        $explores = (new Explore())->getExplores($pid);
        return json_encode($explores);
    }
}
