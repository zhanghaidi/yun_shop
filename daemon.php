#!/bin/env php
<?php
/** 确保这个函数只能运行在SHELL中 */

use Illuminate\Contracts\Console\Kernel;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

if (substr(php_sapi_name(), 0, 3) !== 'cli') {
    die("This Programe can only be run in CLI mode");
}

class Daemon
{
    /* config */
    const LISTEN = "tcp://192.168.2.15:5555";
    const MAXCONN = 100;
    const uid = 80;
    const gid = 80;

    protected $pool = NULL;
    protected $zmq = NULL;
    private $pidFile;

    public function __construct()
    {
        $this->pidFile = $this->file(__CLASS__);
    }

    private function file($fileName)
    {
        return './storage/app/pids/' . $fileName . '.pid';
    }

    private function daemon()
    {
        if (file_exists($this->pidFile)) {
            echo "The file $this->pidFile exists.\n";
            exit();
        }

        $pid = pcntl_fork();
        if ($pid == -1) {
            die('could not fork');
        } else if ($pid) {
            // we are the parent
            //pcntl_wait($status); //Protect against Zombie children
            exit($pid);
        } else {
            // we are the child
            file_put_contents($this->pidFile, getmypid());
            posix_setuid(self::uid);
            posix_setgid(self::gid);
            return (getmypid());
        }
    }

    private function start()
    {
        $pid = $this->daemon();


        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        define('LARAVEL_START', microtime(true));

        $file = __DIR__ . '/../../framework/bootstrap.inc.php';
        if (file_exists($file)) {
            include_once $file;
        }

        /*
        |--------------------------------------------------------------------------
        | Register The Auto Loader
        |--------------------------------------------------------------------------
        |
        | Composer provides a convenient, automatically generated class loader
        | for our application. We just need to utilize it! We'll require it
        | into the script here so that we do not have to worry about the
        | loading of any our classes "manually". Feels great to relax.
        |
        */
        require __DIR__ . '/vendor/autoload.php';
        $app = require_once __DIR__ . '/bootstrap/app.php';
        $this->queue($app);
    }

    private function queue($app)
    {
        while (true) {

            $pid = pcntl_fork();
            if ($pid == -1) {
                die('could not fork');
            } else if ($pid) {
                // we are the parent
                //pcntl_wait($status); //Protect against Zombie children

            } else {
                file_put_contents($this->file('queues'), getmypid() . PHP_EOL, FILE_APPEND);
                // we are the child
                $this->aQueue($app, []);

            }
        }

    }

    private function aQueue($app, $input)
    {
        /*
        |--------------------------------------------------------------------------
        | Run The Artisan Application
        |--------------------------------------------------------------------------
        |
        | When we run the console application, the current CLI command will be
        | executed in this console and the response sent back to a terminal
        | or another output device for the developers. Here goes nothing!
        |
        */
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

        $status = $kernel->handle(
            $input = new Symfony\Component\Console\Input\ArgvInput(['artisan', 'queue:work', 'redis','--daemon']),
            new Symfony\Component\Console\Output\ConsoleOutput
        );
        /*
        |--------------------------------------------------------------------------
        | Shutdown The Application
        |--------------------------------------------------------------------------
        |
        | Once Artisan has finished running, we will fire off the shutdown events
        | so that any final work may be done by the application before we shut
        | down the process. This is the last thing to happen to the request.
        |
        */
        $kernel->terminate($input, $status);
        exit($status);
    }

    private function stop()
    {

        if (file_exists($this->pidFile)) {
            // 关闭守护进程
            $pid = file_get_contents($this->pidFile);
            posix_kill($pid, 9);
            unlink($this->pidFile);

            // 关闭队列进程
            $pids = file_get_contents($this->file('queues')) ?: [];
            $pids = explode(PHP_EOL, $pids) ?: [];
            $pids = array_filter($pids) ?: [];
            foreach ($pids as $pid) {
                posix_kill($pid, 9);
            }
            unlink($this->file('queues'));

        }
    }

    private function help($proc)
    {
        printf("%s start | stop | help \n", $proc);
    }

    public function main($argv)
    {
        if (count($argv) < 2) {
            printf("please input help parameter\n");
            exit();
        }
        if ($argv[1] === 'stop') {
            $this->stop();
        } else if ($argv[1] === 'start') {
            $this->start();
        } else {
            $this->help($argv[0]);
        }
    }
}

$cgse = new Daemon();
$cgse->main($argv);
