<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-05-26
 * Time: 15:51.
 */

namespace App\Models;

use App\Constants\DefaultConstants;
use App\Traits\SanitizedRequest;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class Poll extends Model
{
    use SanitizedRequest;

    protected $fillable = [
        'object_type',
        'object_id',
        'title',
        'body',
        'votes',
        'multiple',
        'answer',
        'is_visible',
        'started_at',
        'ended_at',
    ];

    protected $dates = [
        'started_at',
        'ended_at',
    ];

    public function scopeVisible($query)
    {
        return $query->where('is_visible', DefaultConstants::TRUE);
    }

    public static function buildData($all, $ansid): string
    {
        $data = [];
        $alldata = [];

        if ($all != '') {
            $all = explode('|', $all);

            foreach ($all as $vote) {
                [$answerid, $answervalue] = explode(':', $vote);
                $data[$answerid] = intval($answervalue);
            }
        }

        foreach ($ansid as $id) {
            if (isset($data[$id])) {
                $data[$id]++;
            } else {
                $data[$id] = 1;
            }
        }

        foreach ($data as $key => $value) {
            $alldata[] = intval($key) . ':' . intval($value);
        }

        return implode('|', $alldata);
    }

    public static function getVotes($all): array
    {
        $data = [];

        if ($all != '') {
            $all = explode('|', $all);

            foreach ($all as $vote) {
                [$answerid, $answervalue] = explode(':', $vote);
                $data[$answerid] = intval($answervalue);
            }
        }

        return $data;
    }

    public static function buildResult($poll): array
    {
        $body = explode("\n", stripslashes($poll->body));
        $answer = self::getVotes($poll->answer);
        $allcount = intval($poll->votes);
        $pn = 0;
        $result = [];

        for ($v = 0; $v < count($body); $v++) {
            $num = $answer[$v] ?? 0;

            $pn++;
            if ($pn > 5) {
                $pn = 1;
            }

            if ($allcount != 0) {
                $proc = (100 * $num) / $allcount;
            } else {
                $proc = 0;
            }

            $proc = round($proc, 2);
            $vote = new stdClass;
            $vote->title = $body[$v];
            $vote->num = $num;
            $vote->proc = $proc;
            $vote->procIntval = intval($proc);
            $vote->color = sprintf('#%06X', mt_rand(0x775999, 0xFFFF00));
            $result[] = $vote;
        }

        return $result;
    }
}
