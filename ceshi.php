#!/bin/env php
<?php
/** 确保这个函数只能运行在SHELL中 */

use Illuminate\Contracts\Console\Kernel;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

if (substr(php_sapi_name(), 0, 3) !== 'cli') {
    die("This Programe can only be run in CLI mode");
}
class Daemon{
    private function file($fileName)
    {
        return './storage/app/pids/' . $fileName . '.pid';
    }
    public function main(){
        $a = file_get_contents($this->file('queues'));

        var_dump($pids);
    }
}

$cgse = new Daemon();
$cgse->main($argv);