<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/25
 * Time: 下午 4:24
 */

namespace App\Http\Repository;


use App\Http\RepositoryProtocol\BannerDetail;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class BannerDetailRepository extends InitRepository implements RepositoryInterface
{

    public function __construct(BannerDetail $bannerDetail)
    {
        parent::__construct($bannerDetail);
    }

    public function insertDetail($insert_array)
    {
        try {
            $this->connectionMaster();
            $this->model->insert($insert_array);
            return true;
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    public function delDetailWithPromotion($banner_id, $promotion_code)
    {
        try {
            $this->connectionMaster();
            $this->model->whereIn('promotion_code', $promotion_code)->where('banner_id', $banner_id)->delete();
            return true;
        } catch (QueryException $e) {
            return false;
        }
    }

    public function delete($banner_id)
    {
        try {
            $this->connectionMaster();
            $this->model->where('banner_id', $banner_id)->delete();
            return true;
        } catch (QueryException $e) {
            return false;
        }
    }
}