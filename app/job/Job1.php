<?php

namespace app\job;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use think\facade\Log;
use think\queue\Job;

class Job1
{

    public function fire(Job $job, $data): void
    {
        try {
            // 假设这个任务时间也很长
            sleep(5);
            //....这里执行具体的任务
            Log::info("执行任务:" . json_encode($data));
            // 发起请求
            $client = new Client(); // 初始化实例
            $url = "https://jsonplaceholder.typicode.com/posts/1";
            Log::info("开始请求:" . $url);
            $promise = $client->getAsync($url); // 发送异步请求
            Log::info("请求发送成功:" . $url);
            // 如果需要返回结果的逻辑处理，则可以调用then方法，then方法接受两个闭包参数，第一闭包处理请求成功的回调，第二个闭包处理请求异常的回调
            $promise->then(
                function (ResponseInterface $res) {
                    echo $res->getStatusCode() . "\n";
                    Log::info("请求响应成功:" . $res->getBody());
                },
                function (RequestException $e) {
                    echo $e->getMessage() . "\n";
                    echo $e->getRequest()->getMethod();
                    Log::error("请求响应失败:" . $e->getMessage());

                });
            Log::info("请求等待中:" . $url);
            // 如果需要返回结果的逻辑处理，则可以调用wait方法，wait方法会阻塞当前进程，直到请求完成
            $promise->wait();
            Log::info("这里会阻塞:" . $url);

            // 获取重试次数 $job->attempts()
            if ($job->attempts() > 3) {
                // 通过这个方法可以检查这个任务已经重试了几次了
                // 这里设置重试次数超过三次仍旧没有成功，就删除这个任务
                // 实际上应该保留日志，查看错误的原因
                $job->delete();
            }

            //如果任务执行成功后 记得删除任务，不然这个任务会重复执行，直到达到最大重试次数后失败后，执行failed方法
            $job->delete();
        } catch (\Exception $e) {
            $job->delete();
            Log::error("任务执行失败:" . $e->getMessage());
        }

    }


    public function failed($data)
    {
        Log::error("任务一直没有成功:", $data);
        // ...任务达到最大重试次数后，失败了
    }
}
