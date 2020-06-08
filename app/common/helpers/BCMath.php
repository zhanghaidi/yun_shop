<?php

namespace app\common\helpers;

class BCMath
{

    static public function proportionMath($money, $proportion)
    {
        return bcdiv(bcmul($money, $proportion, 2), 100, 2);
    }
}