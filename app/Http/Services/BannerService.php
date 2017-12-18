<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/31
 * Time: 上午 10:11
 */

namespace App\Http\Services;

use App\Http\Helper\ErrorCode;
use App\Http\Repository\BannerDetailRepository;
use App\Http\Repository\BannerRepository;
use App\Http\RepositoryProtocol\Banner;
use App\Http\Services\Traits\ValidateTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BannerService
{
    use ValidateTrait;

    public $repository;
    public $detailRepository;

    public function __construct(BannerRepository $bannerRepository, BannerDetailRepository $bannerDetailRepository)
    {
        $this->repository = $bannerRepository;
        $this->detailRepository = $bannerDetailRepository;
    }

    /**
     * @return bool
     */
    public function validateUpdateRules()
    {
        return $this->validateParams(Banner::$updateRules);
    }

    /**
     * @return bool
     */
    public function validateRules()
    {
        return $this->validateParams(Banner::$rules);
    }

    /**
     * @return bool
     */
    public function validateSearchRules()
    {
        return $this->validateParams(Banner::$searchRules);
    }

    /**
     * @param $url
     * @param $description
     * @param $status
     * @param $pic_web
     * @param $pic_mobile
     * @param $sort
     * @return mixed
     */
    public function create($url, $description, $status, $pic_web, $pic_mobile, $sort)
    {
        if ($this->repository->create($url, $description, $status, $pic_web, $pic_mobile, $sort)) {
            return $this->repository->getId();
        }
        Log::error('DB insert error	');
        return $this->fail(ErrorCode::UNABLE_WRITE);
    }

    /**
     * @param $params
     * @return mixed
     */
    public function update($params)
    {
        return $this->repository->update($params['id'], $params['url'], $params['description'],
            $params['status'], $params['sort'], $params['pic_web'], $params['pic_mobile']);
    }

    /**
     * 根據`id`，取得`pic_web`, `pic_mobile`
     *
     * @param $id
     * @return array|mixed
     */
    public function getImagePath($id)
    {
        return $this->repository->getWebMobile($id);

    }

    /**
     * @param $id
     * @param $params
     * @return array
     */
    public function toInsertArray($id, $params)
    {
        $insertArray = [];
        $modUser = Auth::user()->account;
        foreach ($params as $value) {
            $insertArray[] = [
                'banner_id' => $id,
                'promotion_code' => $value['pCode'],
                'mod_user' => $modUser
            ];
        }
        return $insertArray;
    }

    /**
     * 判斷banner的歸屬站台是否有做異動
     *
     * @param $old_promotion
     * @param $new_promotion
     * @param $banner_id
     * @param $mod_user
     * @return bool
     */
    public function checkPromotionUpdate($old_promotion, $new_promotion, $banner_id, $mod_user)
    {
        $repository = $this->detailRepository;
        $old_promotion_array = array_column($old_promotion, 'promotion_code');//explode(',', $old_promotion);
        $new_promotion_array = array_column(json_decode($new_promotion, true), 'pCode');
        $del_promotion = array_diff($old_promotion_array, $new_promotion_array);
        $add_promotion = array_diff($new_promotion_array, $old_promotion_array);

        if (count($add_promotion) > 0) {
            $insert_array = array();

            foreach ($add_promotion as $promotion_code) {
                $insert_array[] = [
                    'banner_id' => $banner_id,
                    'promotion_code' => $promotion_code,
                    'mod_user' => $mod_user
                ];
            }

            if ($repository->insertDetail($insert_array) == false) {
                return false;
            }
        }

        if (count($del_promotion) > 0) {
            if ($repository->delDetailWithPromotion($banner_id, $del_promotion) == false) {
                return false;
            }
        }
        return true;
    }

    /**
     * 刪除圖檔
     *
     * @param $file : 檔名
     * @param $img_path : 路徑
     */
    public function deleteImg($file, $img_path)
    {
        File::delete($img_path . '/' . $file);
    }
}