<?php

namespace Yunshop\FaceAnalysis\services;

use app\common\traits\ValidatorTrait;
use app\common\facades\Setting;

class SetService
{
    use ValidatorTrait;

    public function storeSet($array)
    {
        $validator = (new SetService())->validator($array);
        if ($validator->fails()) {
            return $validator->messages();
        }

        if (isset($array['consume_frequency'])) {
            if ($array['consume_frequency'] <= 0) {
                $array['consume_frequency'] = 0;
            }
        }
        if (isset($array['consume_number'])) {
            if ($array['consume_number'] <= 0) {
                $array['consume_number'] = 0;
            } elseif ($array['consume_number'] > 65535) {
                $array['consume_number'] = 65535;
            }
        }
        if (isset($array['consume_surplus'])) {
            if ($array['consume_surplus'] <= 0) {
                $array['consume_surplus'] = 0;
            } elseif ($array['consume_surplus'] > 65535) {
                $array['consume_surplus'] = 65535;
            }
        }

        if (isset($array['gain_frequency'])) {
            if ($array['gain_frequency'] <= 0) {
                $array['gain_frequency'] = 0;
            }
        }
        if (isset($array['gain_number'])) {
            if ($array['gain_number'] <= 0) {
                $array['gain_number'] = 0;
            } elseif ($array['gain_number'] > 65535) {
                $array['gain_number'] = 65535;
            }
        }
        if (isset($array['gain_surplus'])) {
            if ($array['gain_surplus'] <= 0) {
                $array['gain_surplus'] = 0;
            } elseif ($array['gain_surplus'] > 65535) {
                $array['gain_surplus'] = 65535;
            }
        }

        foreach ($array as $key => $item) {
            Setting::set((new FaceAnalysisService)->get('label') . '.' . $key, $item);
        }
        return true;
    }
}
