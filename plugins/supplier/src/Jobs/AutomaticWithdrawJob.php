<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/7/15
 * Time: 下午 02:19
 */

namespace Yunshop\Supplier\Jobs;


use Yunshop\Supplier\supplier\models\SupplierWithdraw;
use Yunshop\Supplier\common\services\withdraw\SupplierAutomaticService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AutomaticWithdrawJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    /**
     * @var SupplierWithdraw
     */
    private $withdrawModel;


    public function __construct(SupplierWithdraw $withdrawModel)
    {
        $this->withdrawModel = $withdrawModel;
    }

    /**
     * @throws \app\common\exceptions\ShopException
     */
    public function handle()
    {
        $automateAuditService = new SupplierAutomaticService($this->withdrawModel);

        $automateAuditService->freeAudit();
    }

}