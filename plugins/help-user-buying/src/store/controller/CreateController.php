<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/23
 * Time: 10:23
 */
namespace Yunshop\HelpUserBuying\store\controller;


class CreateController extends \Yunshop\StoreCashier\frontend\store\CreateController
{
    protected $publicAction = ['index'];
    protected $ignoreAction = ['index', 'validateParam', 'getMemberCarts', 'getShopOrder', 'getPluginOrders'];

    public function __construct()
    {

        parent::__construct();
    }
}