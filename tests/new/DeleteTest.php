<?php

/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/20
 * Time: 上午 9:39
 */

use App\Http\Repository\NewReportRepository;
use App\Http\Services\NewsReportService;
use App\Http\Repository\NewReportDetailRepository;

class DeleteTest extends TestCase
{
    private $report_link, $del_data, $test_data;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->report_link = new NewReportRepository();
    }

    public function setUp()
    {
        parent::setUp();
        $this->del_data = ['newsTypeID' => '',
            'promotionCode' => '',
            's_Date' => '',
            'adminAccount' => 'admin',
            'title' => 'TEST',
            'status' => '6'];

        $this->test_data = $this->report_link->getListByParameters($this->del_data);
        if (empty($this->test_data)) {
            $this->assertFalse(true, 'Not find test record');
        }
    }

    public function testDelImg()
    {
        foreach ($this->test_data as $item) {
            $news_id = $item['id'];
            $pic = $this->report_link->getImgById($news_id);

            if(! empty($pic)) {
                $new_service = new NewsReportService();
                $del_res = $new_service->deleteImg($pic['pic'], config('define.img_path.news'));

                if($del_res) {
                    $this->assertTrue(true, 'Success : News image delete');
                } else {
                    $this->assertFalse(true, 'Fail : News image delete');
                }
            }
            var_dump('This news record no picture (ID : ' . $news_id . ')');
        }
    }

    /**
     * @group test_noImg
     */
    public function testDelete()
    {
        foreach ($this->test_data as $item) {
            $news_id = $item['id'];
            $result_del = $this->report_link->delDataByID($news_id);

            if($result_del) {
                $this->assertTrue(true);
            } else {
                $this->assertFalse(true, 'Delete New_report Fail (ID : ' . $news_id . ')');
            }
        }
    }
}