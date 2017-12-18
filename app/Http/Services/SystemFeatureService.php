<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/12/8
 * Time: 下午 4:53
 */

namespace App\Http\Services;


use App\Http\Repository\AdminRepository;
use App\Http\Repository\AdminSystemFeatureRepository;
use App\Http\Repository\SystemFeatureKindRepository;

class SystemFeatureService
{
    public $systemFeatureKindRepository;
    public $adminRepository;
    public $adminSystemFeatureRepository;

    public function __construct(SystemFeatureKindRepository $systemFeatureKindRepository, AdminRepository $adminRepository, AdminSystemFeatureRepository $adminSystemFeatureRepository)
    {
        $this->systemFeatureKindRepository = $systemFeatureKindRepository;
        $this->adminRepository = $adminRepository;
        $this->adminSystemFeatureRepository = $adminSystemFeatureRepository;

    }

    public function checkUserRights($account, $featureList)
    {
        if ($this->adminRepository->findByAccount($account)->status != 9 &&
            in_array($this->systemFeatureKindRepository->getIdByKindCode('system_feature')['id'], $featureList)
        ) {
            return false;
        }
        return true;
    }

    public function getProcess($account, $featureList)
    {
        //如果已有權限資料存在
        if (! ($oldListCollect = $this->adminSystemFeatureRepository->findFeatureByAccount($account)->pluck('system_feature_kind_id'))->isEmpty()) {
            $newListCollect = collect($featureList);
            $createList = $newListCollect->diff($oldListCollect->toArray());
            $deleteList = $oldListCollect->diff($newListCollect->toArray());

        } else {
            $createList = $featureList;
        }

        return [
            'createList' => $createList,
            'deleteList' => $deleteList??null
        ];
    }

    public function create($account, $createList)
    {
        foreach ($createList as $value) {
            if (! $this->adminSystemFeatureRepository->create($account, (int)$value)) {
                return false;
            };

        }
        return true;
    }

    public function delete($account, $deleteList)
    {
        if (! $this->adminSystemFeatureRepository->deleteInArray($account, $deleteList)) {
            return false;
        }
        return true;
    }
}