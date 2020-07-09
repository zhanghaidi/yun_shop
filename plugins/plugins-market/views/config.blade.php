@extends('admin.master')

@section('title', trans('Yunshop\PluginsMarket::config.title'))

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      {{ trans('Yunshop\PluginsMarket::config.title') }}
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">
    <?php
      $form = Option::form('market_source_title', trans('Yunshop\PluginsMarket::config.options.title'), function($form) {
        $form->text('market_source', trans('Yunshop\PluginsMarket::config.options.source-text'))->hint(trans('Yunshop\PluginsMarket::config.options.source-hint'));
        $form->checkbox('auto_enable_plugin', trans('Yunshop\PluginsMarket::config.options.auto-enable-text'))->label(trans('Yunshop\PluginsMarket::config.options.auto-enable-label'));
        $form->checkbox('replace_default_market', trans('Yunshop\PluginsMarket::config.options.replace-default-market-text'))->label(trans('Yunshop\PluginsMarket::config.options.replace-default-market-label'));
        $form->select('plugin_update_notification', trans('Yunshop\PluginsMarket::config.options.update-notif-text'))
            ->option('none', trans('Yunshop\PluginsMarket::config.options.update-none'))
            ->option('release_only', trans('Yunshop\PluginsMarket::config.options.update-release-only'))
            ->option('both', trans('Yunshop\PluginsMarket::config.options.update-both'));
      })->handle();
    ?>

    <div class="row">
        <div class="col-md-6">
            {!! $form->render() !!}
        </div>

        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('Yunshop\PluginsMarket::config.readme.title') }}</h3>
                </div><!-- /.box-header -->
                <div class="box-body">

                </div><!-- /.box-body -->
            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('Yunshop\PluginsMarket::config.list.title') }}</h3>
                </div><!-- /.box-header -->

                <div class="box-body">
                    {!! trans('Yunshop\PluginsMarket::config.list.text') !!}

                </div><!-- /.box-body -->
            </div>
        </div>
    </div>
    
  </section><!-- /.content -->
</div><!-- /.content-wrapper -->

@endsection

