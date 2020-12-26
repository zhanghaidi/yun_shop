<?php

namespace Yunshop\Examination\models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExaminationModel extends BaseModel
{
    use SoftDeletes;

    public $table = 'yz_exam_examination';

    public function content()
    {
        return $this->hasOne('Yunshop\Examination\models\ExaminationContentModel', 'examination_id');
    }

    public function getOpenStatusAttribute()
    {
        if (!isset($this->id)) {
            return 2;
        }
        if (!isset($this->status) || $this->status != 1) {
            return 2;
        }
        if (is_null($this->start) && is_null($this->end)) {
            return 1;
        }

        $nowTime = time();
        $startAt = strtotime($this->start);
        $endAt = strtotime($this->end);
        if ($startAt == false) {
            if ($endAt < $nowTime) {
                return 2;
            }
        } elseif ($endAt == false) {
            if ($nowTime < $startAt) {
                return 2;
            }
        } else {
            if ($nowTime < $startAt || $endAt < $nowTime) {
                return 2;
            }
        }
        return 1;
    }

    public function getTimeStatusAttribute()
    {
        if (!isset($this->id)) {
            return 0;
        }
        if (is_null($this->start) && is_null($this->end)) {
            return 0;
        }
        return 1;
    }

    public function setStartAttribute($data)
    {
        if (!isset($data['time_status']) || $data['time_status'] != 1) {
            $this->attributes['start'] = null;
            return;
        }

        if (!isset($data['time_range']['start'])) {
            $this->attributes['start'] = null;
            return;
        }

        $start = strtotime($data['time_range']['start']);
        if ($start === false) {
            $this->attributes['start'] = null;
            return;
        }

        $this->attributes['start'] = date('Y-m-d H:i:s', $start);
    }

    public function setEndAttribute($data)
    {
        if (!isset($data['time_status']) || $data['time_status'] != 1) {
            $this->attributes['end'] = null;
            return;
        }

        if (!isset($data['time_range']['end'])) {
            $this->attributes['end'] = null;
            return;
        }

        $start = strtotime($data['time_range']['end']);
        if ($start === false) {
            $this->attributes['end'] = null;
            return;
        }

        $this->attributes['end'] = date('Y-m-d H:i:s', $start);
    }

    public function setDurationAttribute($data)
    {
        if (!isset($data['duration'])) {
            $this->attributes['duration'] = 0;
            return;
        }

        if ($data['duration'] <= 0 || $data['duration'] > 65535) {
            $this->attributes['duration'] = 0;
            return;
        }

        $this->attributes['duration'] = $data['duration'];
    }

    public function setFrequencyAttribute($data)
    {
        if (!isset($data['frequency_status']) || $data['frequency_status'] != 1) {
            $this->attributes['frequency'] = 0;
            return;
        }

        if (!isset($data['frequency_number']) || $data['frequency_number'] <= 0 ||
            $data['frequency_number'] > 255
        ) {
            $this->attributes['frequency'] = 0;
            return;
        }

        $this->attributes['frequency'] = $data['frequency_number'];
    }

    public function setIntervalAttribute($data)
    {
        if (!isset($data['interval'])) {
            $this->attributes['interval'] = 0;
            return;
        }

        if ($data['interval'] <= 0 || $data['interval'] > 255) {
            $this->attributes['interval'] = 0;
            return;
        }

        $this->attributes['interval'] = $data['interval'];
    }

    public function setIsQuestionScoreAttribute($data)
    {
        if (!isset($data['is_question_score']) || $data['is_question_score'] != 1) {
            $this->attributes['is_question_score'] = 2;
            return;
        }
        $this->attributes['is_question_score'] = 1;
    }

    public function setIsScoreAttribute($data)
    {
        if (!isset($data['is_score']) || $data['is_score'] != 2) {
            $this->attributes['is_score'] = 1;
            return;
        }
        $this->attributes['is_score'] = 2;
    }

    public function setIsQuestionAttribute($data)
    {
        if (!isset($data['is_question'])) {
            $this->attributes['is_question'] = 1;
            return;
        }

        if ($data['is_question'] == 2) {
            $this->attributes['is_question'] = 2;
        } elseif ($data['is_question'] == 3) {
            $this->attributes['is_question'] = 3;
        } else {
            $this->attributes['is_question'] = 1;
        }
    }

    public function setIsAnswerAttribute($data)
    {
        if (!isset($data['is_answer'])) {
            $this->attributes['is_answer'] = 1;
            return;
        }

        if ($data['is_answer'] == 2) {
            $this->attributes['is_answer'] = 2;
        } elseif ($data['is_answer'] == 3) {
            $this->attributes['is_answer'] = 3;
        } else {
            $this->attributes['is_answer'] = 1;
        }
    }
}
