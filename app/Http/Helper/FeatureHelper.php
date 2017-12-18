<?php
namespace App\Http\Helper;

use App\Http\Repository\AdminSystemFeatureRepository;
use Illuminate\Support\Facades\Redis;

class FeatureHelper
{
    /**
     *將管理者使用權限寫入Redis
     *使用admin_system_feature saved事件監聽觸發
     * @param $user
     */
    public function setFeatureList($user)
    {
        $repository = new AdminSystemFeatureRepository();
        $kindCodeList = [];
        if ($feature = $repository->userKindCode($user)) {
            $list = array_column($feature, 'system_feature_kind');
            $kindCodeList = array_column($list, 'kind_code');
        }
        $redis = Redis::connection();
        $redis->set($user . '_feature', json_encode($kindCodeList));
    }

    /**
     * 取得該管理者使用權限
     * @param $user
     * @return array|mixed
     */
    public function getList($user)
    {
        try {
            $redis = Redis::connection();
            $list = $redis->get($user . '_feature');
            return json_decode($list);
        } catch (\Exception $e) {
            return [];
        }
    }

}