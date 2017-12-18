<?php

/**
 * Created by PhpStorm.
 * User: dev
 * Date: 2017/11/24
 * Time: 下午 3:22
 */

use App\Http\Repository\AdminRepository;

class CreateTest extends TestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    public function testCreate()
    {
        $create = [
            'account' => 'test123',
            'pwd' => 'test123',
            'name' => 'Heloon',
            'email' => 'ttt321@gmail.com'
        ];

        $repository = new AdminRepository();
        $result = $repository->create($create['account'], $create['pwd'], $create['name'], $create['email']);

        if ($result) {
            $this->assertTrue(true);
        } else {
            $this->assertFalse(true, 'Fail : create admin');
        }
    }
}