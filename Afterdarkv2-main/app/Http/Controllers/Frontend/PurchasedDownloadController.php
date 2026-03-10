<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Order;
use App\Models\Song;
use App\Services\DownloadService;
use Illuminate\Http\Request;

class PurchasedDownloadController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function song()
    {
        if (Order::query()->where('orderable_type', Song::class)->where('user_id', auth()->id())->exists()) {
            $song = Song::query()
                ->withoutGlobalScopes()
                ->findOrFail($this->request->route('id'));

            $format = $this->request->route('format');

            if ($format == 'mp3' && $song->mp3) {
                $file = new DownloadService(
                    $song->getFirstMedia('hd_audio') ? $song->getFirstMedia('hd_audio') : $song->getFirstMedia('audio'),
                    $song->title . '.mp3',
                    intval(Group::getValue('option_download_resume')),
                    intval(Group::getValue('option_download_speed'))
                );
                $song->increment('download_count');
                session_write_close();
                $file->downloadFile();
                exit();
            }
        } else {
            abort(403, 'You have to buy the song before download it.');
        }
    }
}
