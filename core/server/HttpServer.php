<?php
namespace Core\server;
use Core\init\TestProcess;
use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
class HttpServer {
    private $server;
    private $dispatcher;
    public function __construct()
    {
        $this->server = new Server("0.0.0.0", 8080);
        $this->server->set(
            array(
                "worker_num" => 1,
                "daemonize" => false,
            )
        );

        /*
        $this->server->on('request', function($req, $res) {

        });
        $this->server->on('Start', function(Server $server) {

        });
        $this->server->on('ShutDown', function(Server $server) {
            echo "关闭了" . PHP_EOL;
        });
        */
        $this->server->on('request', [$this, "onRequest"]);
        $this->server->on('start', [$this, "onStart"]);
        $this->server->on('shutDown', [$this, "onShutDown"]);
        $this->server->on('workerStart', [$this, "onWorkerStart"]);

    }

    public function onWorkerStart() {
        // 修改worker进程名
        cli_set_process_title("[worker] simpleHttpSwoole");
        /*
         * 平滑重启只是重启worker进程
         * 平滑重启只对onWorkerStart或onReceive等在Worker进程中include/require的PHP文件有效
         * Server启动前就已经include/require的PHP文件，不能通过平滑重启重新加载
         * 对于Server的配置即$serv->set()中传入的参数设置，必须关闭/重启整个Server才可以重新加载
         * Server可以监听一个内网端口，然后可以接收远程的控制命令，去重启所有Worker进程
         * http://wiki.swoole.com/wiki/page/20.html
         *
         */
        // 该回调方法是启动swoole 的worker进程
        // 所以重新能重新加载的文件，放在这个地方


        // 初始化Bean工厂
        \Core\BeanFactory::init();
        // 加载路由
        $this->dispatcher = \Core\BeanFactory::getBean("RouterCollector")->getDispatcher();
    }

    public function onRequest(Request $request, Response $response) {
        $myrequest = \Core\http\Request::init($request);
        $myresponse = \Core\http\Response::init($response);
        // $routeInfo = $dispatcher->dispatch($request->server["request_method"], $request->server["request_uri"]);
        $routeInfo = $this->dispatcher->dispatch($myrequest->getMethod(), $myrequest->getUri());
        // $routeInfo[0]:请求返回码 $routeInfo[1]:请求方式 $routeInfo[2]:请求方法
        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                $response->status(404);
                $response->end();
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                $response->status(405);
                $response->end();
                break;
            case \FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                // var_dump($routeInfo);
                $vars = $routeInfo[2];
                // 设置额外参数，方便控制器注入
                $extVars = [$myrequest, $myresponse];
                // $response->end($handler($vars, $extVars));
                $myresponse->setBody($handler($vars, $extVars));
                $myresponse->end();
                break;
        }
    }

    public function onManagerStart() {
        // 该回调方法是启动swoole 的manager进程
        cli_set_process_title("[manager] simpleHttpSwoole");
    }

    public function onStart(Server $server) {
        // 该回调方法是启动swoole 的master进程
        // 修改master进程名
        cli_set_process_title("[master] simpleHttpSwoole");
        $mid = $server->master_pid;
        file_put_contents("./simpleHttpSwoole", $mid);
    }

    public function onShutDown(Server $server) {
        unlink("./simpleHttpSwoole");
    }

    public function run() {
        $p = new TestProcess();
        // 文件热更新测试
        $this->server->addProcess($p->run());
        $this->server->start();
    }
}