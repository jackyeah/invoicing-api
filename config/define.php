<?php

return [
//    'self' => [
//        'token' => config('setting/' . app()->environment() . '.self.token')
//    ],
    'cypress' => [
        'domain' => config('setting/' . app()->environment() . '.cypress.domain'),
        'token' => config('setting/' . app()->environment() . '.cypress.token'),
        'url' => [
            'getRank' => 'dev/rich/getrank',
        ],
    ],
    //DB Connection 未上線,暫時紀錄各站的對應token
    'site_token' => [
        //賽車
        'f2a16a20094b3136fb909e294323ff83' => '1',
        //遊樂園
        'e7d34d26404bb9e36c7eb688b0dc5bf6' => '2',
    ],
    'img_path' => [
        'web' => config('setting/' . app()->environment() . '.img_path.web'),
        'mobile' => config('setting/' . app()->environment() . '.img_path.mobile'),
        'pic_web' => config('setting/' . app()->environment() . '.img_path.web'),
        'pic_mobile' => config('setting/' . app()->environment() . '.img_path.mobile'),
        'news' => config('setting/' . app()->environment() . '.img_path.news'),
        'content' => config('setting/' . app()->environment() . '.img_path.content'),
        'game' => config('setting/' . app()->environment() . '.img_path.game')
    ],
    'img_server' => [
        'web' => config('setting/' . app()->environment() . '.img_server.web'),
        'mobile' => config('setting/' . app()->environment() . '.img_server.mobile'),
        'pic_web' => config('setting/' . app()->environment() . '.img_server.web'),
        'pic_mobile' => config('setting/' . app()->environment() . '.img_server.mobile'),
        'news' => config('setting/' . app()->environment() . '.img_server.news'),
        'content' => config('setting/' . app()->environment() . '.img_server.content'),
        'game' => config('setting/' . app()->environment() . '.img_server.game')
    ]
];