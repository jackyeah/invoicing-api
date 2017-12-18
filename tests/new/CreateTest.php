<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Http\Repository\NewReportRepository;
use App\Http\Repository\NewReportDetailRepository;
use Illuminate\Http\UploadedFile;

class CreateTest extends TestCase
{
    private $create, $detail;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->create = [
            'newsTypeID' => '1',
            'date' => date('Y-m-d H:i:s', time()),
            'title' => 'TEST',
            'overview' => 'test test',
            'content' => 'this is content Ok!',
            'status' => '6'
        ];

        $this->detail = [
            'promotion_code' => '1',
            'mod_user' => 'admin',
            'updated_at' => date('Y-m-d H:i:s', time())
        ];
    }

    /**
     * @group test_noImg
     */
    public function testCreate()
    {
        $repository = new NewReportRepository();
        $addNews_id = $repository->insertData($this->create);

        if($addNews_id) {
            $this->assertTrue(true);
        } else {
            $this->assertFalse(true, 'create news fail');
        }

        return $addNews_id;
    }

    /**
     * @group test_noImg
     * @depends testCreate
     */
    public function testCreateDetail($addNews_id)
    {
        $this->detail['new_report_id'] = $addNews_id;

        $repository = new NewReportDetailRepository();
        $result = $repository->insertData($this->detail);

        if($result) {
            $this->assertTrue(true);
        } else {
            $this->assertFalse(true, 'add news detail fail');
        }
    }

    /**
     * @depends testCreate
     */
    public function testCreateImage($addNews_id)
    {
        $name = 'News.jpg';
        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'News Create image', $text_color);
        imagejpeg($im, $name);

        $path = __DIR__ . '/../../' . $name;
        $file = new UploadedFile($path, $name, 'image/jpeg', filesize($path), null, true);

        //目前是测整个上传图片流程
        $response = $this->call('POST', 'backend/upload/news_img', ['id' => $addNews_id], [], ['image' => $file], []);
        $result = $response->getOriginalContent();
        if($result['error_code'] == '1') {
            $this->assertTrue(true);
        } else {
            $this->assertFalse(true, 'add news image fail');
        }

//        $mock = $this->getMockForTrait(UploadFileTrait::uploadFile('image', 123));
    }
}
