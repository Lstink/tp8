<?php

namespace app\controller;

use app\BaseController;
use app\job\Job1;
use think\facade\Queue;

class Test extends BaseController
{

    public function test()
    {
        $data = [
            'lat' => '36.650200',
            'lng' => '117.120000'
        ];
        // 发送请求
        Queue::push(Job1::class, $data);
    }
}
