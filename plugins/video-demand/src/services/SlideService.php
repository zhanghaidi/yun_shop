<?php

namespace Yunshop\VideoDemand\services;

use Yunshop\VideoDemand\models\SlideModel;

class SlideService
{
    public function getSlideData()
    {
        return SlideModel::getSlide()->get();
    }

    public static function createStatusService($data)
    {

        switch ($data->status) {
            case 0:
                return '不显示';
                break;
            case 1:
                return '显示';
                break;
        }
    }
}