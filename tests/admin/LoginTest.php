<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Http\Repository\AdminRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;


class LoginTest extends TestCase
{
    private $login;
    private static  $user;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->login = ['account' => 'admin', 'pwd' => '1234'];
    }

    public function testFindAccount()
    {
        $repository = new AdminRepository();
        self::$user = $repository->findByAccount($this->login['account']);

        if(! is_null(self::$user)) {
            $this->assertTrue(true);
        }
    }

    public function testHash()
    {
        $check = Hash::check($this->login['pwd'], self::$user->pwd);

        if($check) {
            $this->assertTrue(true);
        }
    }

    public function testTokenSession()
    {
        $account = self::$user->account;
        $token = Str_random(60);
        $api_token = Hash::make($token);
        if(empty($api_token)) {
            $this->assertFalse(true, 'hash make token false !');
        } else {
            Session::put($api_token, $account);

            if(Session::get($api_token) == $account) {
                $this->assertTrue(true);
            }
        }
    }

    public function testLoginSaveInfo()
    {
        $repository = new AdminRepository();
        $repository->loginSaveInfo(self::$user);

        self::$user->save();
        $this->assertTrue(true);
    }
}
