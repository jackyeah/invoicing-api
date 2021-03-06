<?php

/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/22
 * Time: 下午 3:06
 */

use App\Http\Repository\NewReportRepository;
use App\Http\Repository\NewReportDetailRepository;
use Illuminate\Http\UploadedFile;

class UpdateTest extends TestCase
{
    private $report_link, $report_detail, $promotion, $upd_data, $find_data, $test_data;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->report_link = new NewReportRepository();
        $this->report_detail = new NewReportDetailRepository();

        //update data default
        $this->promotion = '[{"pCode":"2"}]';
        $this->upd_data = ['newsID' => '',
                            'newsTypeID' => '2',
                            'date' => date('Y-m-d H:i:s', time()),
                            'title' => 'Update test',
                            'overview' => 'test success',
                            'content' => 'This is update test !',
                            'status' => '2'];
    }

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->find_data = ['newsTypeID' => '',
            'promotionCode' => '',
            's_Date' => '',
            'adminAccount' => 'admin',
            'title' => 'TEST',
            'status' => '6'];

        $this->test_data = $this->report_link->getListByParameters($this->find_data);
        if (empty($this->test_data)) {
            $this->assertFalse(true, 'Not find test record');
        }
    }

    public function testUpdateImg()
    {
        $new_id = $this->test_data[0]['id'];
        $name = 'New_update.jpg';
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'New Update image', $text_color);
        imagejpeg($im, $name);

        $path = __DIR__ . '/../../' . $name;
        $file = new UploadedFile($path, $name, 'image/jpeg', filesize($path), null, true);

        //目前是测整个上传图片流程
        $response = $this->call('POST', 'backend/upload/news_img', ['id' => $new_id], [], ['image' => $file], []);
        $result = $response->getOriginalContent();
        if($result['error_code'] == '1') {
            $this->assertTrue(true);
        } else {
            $this->assertFalse(true, 'add news image fail');
        }
    }

    public function testUpdatePromotion()
    {
        $new_id = $this->test_data[0]['id'];
        $result_add = $result_del = true;

        $repository_report_detail = new NewReportDetailRepository();
        $result_pCode = $repository_report_detail->getDataByReportID($new_id);

        if (empty($result_pCode)) {
            $this->assertFalse(true, 'Fail : No Get Promotion Code');
        }

        // 比對是否有需要異動
        $pCode_before = array_column($result_pCode, 'promotion_code');
        $pCode_after = array_column(json_decode($this->promotion, TRUE), 'pCode');
        $pCode_diff_add = array_diff($pCode_after, $pCode_before);
        $pCode_diff_cut = array_diff($pCode_before, $pCode_after);

        // 新增
        if(count($pCode_diff_add) > 0){
            foreach ($pCode_diff_add as $item) {
                $arr_add = ['new_report_id' => $new_id,
                    'promotion_code' => $item,
                    'mod_user' => 'admin',
                    'updated_at' => date('Y-m-d H:i:s', time())
                ];
                $result_add = $repository_report_detail->insertData($arr_add);
            }
        }

        // 刪除
        if(count($pCode_diff_cut) > 0) {
            $result_del = $repository_report_detail->delDataByReportIdAndPCode($new_id, $pCode_diff_cut);
        }

        if($result_add && $result_del) {
            $this->assertTrue(true);
        } else {
            $this->assertFalse(true, 'Fail : Update New Promotion');
        }
    }

    public function testUpdate()
    {
        $this->upd_data['newsID'] = $this->test_data[0]['id'];

        $result_update = $this->report_link->updateData($this->upd_data);

        if($result_update) {
            $this->assertTrue(true);
            $this->report_link->delDataByID($this->upd_data['newsID']);
        } else {
            $this->assertFalse(true, 'Fail : Update New Info');
        }
    }
}