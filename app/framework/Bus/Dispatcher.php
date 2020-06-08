<?php

namespace app\framework\Bus;

class Dispatcher extends \Illuminate\Bus\Dispatcher
{
    /**
     * Dispatch a command to its appropriate handler.
     *
     * @param mixed $command
     * @return mixed
     */
    public function dispatch($command)
    {
        if ($this->queueResolver && $this->commandShouldBeQueued($command)) {
            return $this->dispatchToQueue($command);
        } else {
            return $this->dispatchNow($command);
        }
    }

    /**
     * Push the command onto the given queue instance.
     *
     * @param \Illuminate\Contracts\Queue\Queue $queue
     * @param mixed $command
     * @return mixed
     */
    protected function pushCommandToQueue($queue, $command)
    {
        if (isset($command->queue, $command->delay)) {
            return $queue->laterOn($command->queue, $command->delay, $command);
        }
// todo 设置项
        if (isset($command->queue) && 1) {
            return $queue->pushOn($command->queue, $command);
        }

        if (isset($command->delay)) {
            return $queue->later($command->delay, $command);
        }

        return $queue->push($command);
    }

}