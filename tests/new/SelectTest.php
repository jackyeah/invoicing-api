<?php

/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/23
 * Time: 上午 11:38
 */

use App\Http\Repository\NewReportTypeRepository;
use App\Http\Repository\NewReportRepository;

class SelectTest extends TestCase
{
    private $select, $report_link;
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->select = [
            'newsTypeID' => '',
            'promotionCode' => '',
            's_Date' => '',
            'e_Date' => '',
            'adminAccount' => '',
            'title' => '',
            'status' => ''
        ];

        $this->report_link = new NewReportRepository();
    }

    public function testSelectType()
    {
        $repository = new NewReportTypeRepository();
        $status_1 = $repository->getAllList('', 1);
        $status_0 = $repository->getAllList('', 0);

        $new_type = array_merge($status_1, $status_0);
        if (count($new_type) == 4) {
            $this->assertTrue(true);
        } else {
            $this->assertFalse(true, 'Type count not 4');
        }
    }

    public function testNoWhere()
    {
        $this->getData($this->select, 'Fail : Not find data');
    }

    public function testWhereTypeId()
    {
        $data = $this->select;
        $data['newsTypeID'] = '1';

        $this->getData($data, 'Fail : Not find data - Where TypeId = 1');
    }

    public function testWhereCode()
    {
        $data = $this->select;
        $data['promotionCode'] = '1';

        $this->getData($data, 'Fail : Not find data - Where promotionCode = 1');
    }

    public function testWhereDate()
    {
        $data = $this->select;
        $data['s_Date'] = date('Y-m-d 00:00:00', time());
        $data['e_Date'] = date('Y-m-d 23:59:59', time());

        $this->getData($data, 'Fail : Not find data - Where s_Date = ' . $data['s_Date'] . 'and e_Date = ' . $data['e_Date']);
    }

    public function testWhereAccount()
    {
        $data = $this->select;
        $data['adminAccount'] = 'admin';

        $this->getData($data, 'Fail : Not find data - Where adminAccount = admin');
    }

    public function testWhereTitle()
    {
        $data = $this->select;
        $data['title'] = 'TE';
        $this->getData($data, 'Fail : Not find data - Where title = TE');

        $data['title'] = 'ES';
        $this->getData($data, 'Fail : Not find data - Where adminAccount = ES');

        $data['title'] = 'ST';
        $this->getData($data, 'Fail : Not find data - Where adminAccount = ST');
    }

    public function testWhereStatus()
    {
        $data = $this->select;
        $data['status'] = 6;
        $this->getData($data, 'Fail : Not find data - Where status = 6');
    }

    private function getData($data, $fail_str)
    {
        $result_data = $this->report_link->getListByParameters($data);

        if (empty($result_data)) {
            $this->assertFalse(true, $fail_str);
        } else {
            $this->assertTrue(true);
        }
    }
}