<?php
/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/10/24
 * Time: 下午 5:48
 */

namespace App\Http\Repository;


use App\Http\Repository\Traits\TryCatchTrait;
use App\Http\RepositoryProtocol\Banner;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\LogHelper;
use Illuminate\Support\Facades\Log;

class BannerRepository extends InitRepository implements RepositoryInterface
{
    use TryCatchTrait;

    public function __construct(Banner $banner)
    {
        parent::__construct($banner);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        if (isset($this->model->id)) {
            return $this->model->id;
        }
        Log::error('id not exist');
        return false;
    }

    public function find($id)
    {
        try {
            return $this->model->find($id);
        } catch (QueryException $e) {
            Log::error('find id error');
        }
        return [];

    }

    /**
     * @param $id
     * @param $field
     * @param $fileName
     * @return bool
     */
    public function updateImg($id, $field, $fileName)
    {
        $this->connectionMaster();
        $this->model->find($id);
        try {
            $this->model->$field = $fileName;
            return $this->save();
        } catch (QueryException $e) {
            Log::error('DB Update error');
        }
        return false;
    }

    /**
     * 根據`id`，取得`pic_web`, `pic_mobile`
     *
     * @param $id
     * @return array|mixed
     */
    public function getWebMobile($id)
    {
        return $this->selectTryCatch(function () use ($id) {
            return $this->model->select('pic_web', 'pic_mobile')->where('id', $id)->first()->toArray();
        });
    }

    /**
     * 新增banner資料
     *
     * @param $url
     * @param $description
     * @param $status
     * @param $pic_web
     * @param $pic_mobile
     * @param $sort
     * @return bool
     */
    public function create($url, $description, $status, $pic_web, $pic_mobile, $sort)
    {
        try {
            $this->connectionMaster();
            $this->model->url = $url;
            $this->model->description = $description;
            $this->model->status = $status;

            if (!is_null($sort) && $sort != '') {
                $this->model->sort = $sort;
            }

            $this->model->pic_web = $pic_web;
            $this->model->pic_mobile = $pic_mobile;
            $this->model->save();
            return true;
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            return false;
        }
    }

    /**
     * 取得banner的清單列表
     *
     * @param $status
     * @return array
     */
    public function getAllList($status)
    {
        try {
            return $this->model->with('banner_details:banner_id,promotion_code')
                ->select('id', 'pic_web', 'pic_mobile', 'status', 'sort',
                    DB::raw("'" . config('define')['img_server']['web'] . "' as web_domain"),
                    DB::raw("'" . config('define')['img_server']['mobile'] . "' as mobile_domain"))
                ->where(function ($query) use ($status) {
                    if ($status != '') {
                        $query->where('banner.status', $status);
                    }
                })
                ->whereNotNull('banner.status')
                ->orderBy('sort', 'ASC')
                ->orderBy('updated_at', 'DESC')
                ->groupBy('banner.id')
                ->get()->toArray();
        } catch (QueryException $e) {
            return [];
        }
    }

    /**
     * 取得banner的詳細資料
     *
     * @param $id
     * @return array
     */
    public function getDetails($id)
    {
        try {
            return $this->model->with('banner_details:banner_id,promotion_code')
                ->select('id', 'pic_web', 'pic_mobile', 'url', 'description', 'status', 'sort',
                    DB::raw("'" . config('define')['img_server']['web'] . "' as web_domain"),
                    DB::raw("'" . config('define')['img_server']['mobile'] . "' as mobile_domain"))
                ->where('banner.id', $id)
                ->first()->toArray();
        } catch (QueryException $e) {
            Log::error($e->getMessage());
            return [];
        }
    }

    /**
     * 更新banner的資料，回傳的格式較特別，array()，藏3個狀態
     *
     * @param $id
     * @param $url
     * @param $description
     * @param $status
     * @param $sort
     * @param $pic_web
     * @param $pic_mobile
     * @return mixed
     */
    public function update($id, $url, $description, $status, $sort, $pic_web, $pic_mobile)
    {
        $this->connectionMaster();

        try {
            $model = $this->model->find($id);
            $model->url = $url;
            $model->description = $description;
            $model->status = $status;
            $model->sort = $sort;
            $model->pic_web = $pic_web;
            $model->pic_mobile = $pic_mobile;
            $isDirty_pic_web = $model->isDirty('pic_web');
            $isDirty_pic_mobile = $model->isDirty('pic_mobile');

            $model->save();

            return ['pic_web' => $isDirty_pic_web, 'pic_mobile' => $isDirty_pic_mobile];
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString($e->getMessage()));
        }
        return false;
    }

    /**
     * 刪除banner
     *
     * @param $banner_id
     * @return bool
     */
    public function delete($banner_id)
    {
        try {
            $this->connectionMaster();
            $this->model->destroy($banner_id);
            return true;
        } catch (QueryException $e) {
            return false;
        }
    }

    /**
     * 根據推廣站代碼，取得banner的清單列表
     *
     * @param string $str_promotionCode
     * @return array|bool
     */
    public function getListByPCode($str_promotionCode)
    {
        $result_data = array();

        try {
            $result_data = $this->model->join('banner_detail', 'banner.id', '=', 'banner_detail.banner_id')
                ->join('promotion_station', 'banner_detail.promotion_code', '=', 'promotion_station.code')
                ->select('banner.id', 'banner.pic_web', 'banner.pic_mobile', 'banner.url', 'banner.sort',
                    DB::raw("'" . config('define')['img_server']['web'] . "' as web_domain"),
                    DB::raw("'" . config('define')['img_server']['mobile'] . "' as mobile_domain"))
                ->groupBy('banner.id')
                ->where('banner_detail.promotion_code', $str_promotionCode)
                ->where('banner.status', 1)
                ->orderBy('banner.sort', 'ASC')
                ->orderBy('banner.updated_at', 'DESC')
                ->get()->toArray();
        } catch (QueryException $e) {
            Log::error(LogHelper::toFormatString('56104'));
        }

        return $result_data;
    }
}