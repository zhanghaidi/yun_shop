<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/12/24
 * Time: 3:38 PM
 */

namespace Yunshop\PluginsMarket;

use app\common\services\Hook;

class PluginApplication extends \app\common\services\PluginApplication
{
    protected function setConfig()
    {

    }

    protected function setMenuConfig()
    {

    }

    public function boot()
    {
        $request = request();
        $events = app('events');
        Hook::registerPluginTransScripts('plugins-market');

        if ($request->is('admin/*') || $request->is('admin')) {
            $events->listen(\app\common\events\RenderingFooter::class, function ($event) {
                //          $event->addContent('<script src="'.plugin_assets('plugins-market', 'assets/js/check.js').'"></script>');
            });
        }


        //var_dump($events);
        //Listen the event of plugin was installed and call a callback function
        $events->listen(\Yunshop\PluginsMarket\Events\PluginWasInstalled::class, function ($event) {

            if (file_exists($file = $event->plugin->getPath() . '/callbacks.php')) {
                $callbacks = require $file;
                call_user_func($callbacks[\Yunshop\PluginsMarket\Events\PluginWasInstalled::class], $event->plugin);
            }
        });

        //Determine to if replace default market of Blessing Skin Server
        if (option('replace_default_market')) {
            Hook::addRoute(function ($routers) {
                $routers->get('admin/plugins/market', 'Yunshop\PluginsMarket\Controllers\MarketController@show')->middleware(['web', 'admin']);
            });
        } else {
            Hook::addMenuItem('admin', 4, [
                'title' => 'Yunshop\PluginsMarket::general.name',
                'link' => 'admin/plugins-market',
                'icon' => 'fa-shopping-bag'
            ]);
        }

        Hook::addRoute(function ($routers) {

            $routers->group(['middleware' => ['web', 'admin'],
                'namespace' => 'Yunshop\PluginsMarket\Controllers',
                'prefix' => 'admin/plugins-market'],
                function ($router) {
                    $router->get('/', 'MarketController@show');
                    $router->get('/data', 'MarketController@ajaxPluginList');
                    $router->get('/check', 'PluginController@updateCheck');
                    $router->post('/download', 'PluginController@downloadPlugin');
                });

        });
    }

    public function register()
    {
    }
}