<?php

namespace App\Http\Helper;

class ErrorCode
{
    CONST ACCOUNT_ERROR = 52101; // 帳號錯誤
    CONST PASSWORD_ERROR = 52102; // 密碼錯誤
    CONST CANT_EDIT_THIS_ACCOUNT = 52103; // 不能編輯此帳號
    CONST ACCOUNT_EXIST = 52104; // 帳號已存在
    CONST ILLEGAL_FILE_TYPE = 52105; // 非法檔案類型
    CONST AccountOrPasswordError = 52106; // 帳號或密碼錯誤
    CONST GameUsed = 52107; // 此遊戲已被使用
    CONST MUST_BE_HIGHEST_LEVEL = 52108; // 需最高權限
    CONST INVALID_AUTHORITY_DISTRIBUTION = 52109; // 無效的權限派發
    CONST AccountDisabled = 52201; // 帳號停用
    CONST AUTHORITY_ERROR = 52202; // 沒有權限
    CONST NONE_SITE = 53101; // 無站台資料
    CONST OPERATION_LIMIT = 54111; // 操作權限不符
    CONST FILE_UPLOAD_FAIL = 54201; // 無法寫入
    CONST CONNECTION_CYPRESS_ERROR = 55001; // 連接CP錯誤
    CONST NO_THIS_SIDE = 55101; // 沒有相對應的站台
    CONST PARAMS_ERROR = 55102; // 參數錯誤
    CONST FILE_UPLOAD_VALIDATE = 55103; // 上傳圖檔驗證錯誤
    CONST URL_FORMAT_ERROR = 55104; // Url 格式錯誤
    CONST CONTENT_EXISTS = 56101; // 內容已存在
    CONST UNABLE_WRITE = 56102; // 資料庫寫入失敗
    CONST UNABLE_UPDATE = 56103; // 資料庫更新失敗
    CONST DB_EXCEPTION = 56104; // 資料庫操作失敗
    CONST DELETE_ERROR = 56105; // 刪除失敗
    CONST REDIS_CONNECT_ERROR = 57101; // Redis連結錯誤
    CONST CONNECTION_ERROR = 59000; // 連接錯誤
    CONST INPUT_PARAMS_ERROR = 59101; // 參數錯誤
    CONST VALIDATE_ERROR = 59102; // 驗證錯誤
    CONST NO_CONFIG_VALUE = 59103; // 無對應設定值
    CONST CAN_NOT_BE_NULL = 59104; // 不得為空值


//    public function getMessage($errorCode)
//    {
//        $message = [
//
//        ];
//        return $message[$errorCode];
//    }

}