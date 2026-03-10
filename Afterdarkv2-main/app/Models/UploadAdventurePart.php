<?php

/**
 * Created by NiNaCoder.
 * Date: 2019-07-05
 * Time: 18:01.
 */

namespace App\Models;

use App\Constants\DefaultConstants;
use App\Http\Requests\Frontend\Upload\FileUploadRequest;
use App\Services\UploadService;
use App\Traits\SanitizedRequest;
use File;
use Illuminate\Support\Str;
use Storage;

class UploadAdventurePart
{
    use SanitizedRequest;

    public function handle(FileUploadRequest $request, $session)
    {
        $uploadService = new UploadService;

        $filenameWithExtension = $request->file('file')->getClientOriginalName();
        $filePath = $request->file('file')->path();

        $mp3Info = $uploadService->getMp3Data($filePath);
        $data = $uploadService->getAudioData($mp3Info, $filePath);
        $trackInfo = $uploadService->getId3Data($mp3Info);

        if (!is_null($request->file_child_value)) {
            if (!is_null($request->file_final_value)) {
                $songId = @$session['children'][$request->file_child_value]['finals'][$request->file_final_value]['final_id'];

                if ($songId) {
                    $song = FinalSong::withoutGlobalScopes()->find($songId);
                } else {
                    $song = new FinalSong;

                    $song->adventure_children_id = $session['children'][$request->file_child_value]['id'];
                    if (!$song->title) {
                        $song->title = $session['children'][$request->file_child_value]['finals'][$request->file_final_value]['title'];
                    }
                    if (!$song->description) {
                        $song->description = $session['children'][$request->file_child_value]['finals'][$request->file_final_value]['short_description'];
                    }
                    $song->order = $request->file_final_value;
                }
            } else {
                $songId = @$session['children'][$request->file_child_value]['id'];

                if ($songId) {
                    $song = ChildSong::withoutGlobalScopes()->find($songId);
                } else {
                    $song = new ChildSong;
                    $song->song_id = $session['adventure_id'];
                    if (!$song->title) {
                        $song->title = $session['children'][$request->file_child_value]['title'];
                    }
                    if (!$song->description) {
                        $song->description = $session['children'][$request->file_child_value]['short_description'];
                    }
                    $song->order = $request->file_child_value;
                }
            }
        } else {
            header('HTTP/1.0 403 Forbidden');
            header('Content-Type: application/json');
            echo json_encode([
                'message' => 'Not support',
                'errors' => ['message' => [__('Wrong adventure scenario!')]],
            ]);
            exit;
        }

        $song->title = $trackInfo['title'][0] ?? pathinfo($filenameWithExtension, PATHINFO_FILENAME);
        $song->duration = $data['playtimeSeconds'];

        $uploadService->processVisualMedia($song, $mp3Info, $trackInfo);

        $song->save();

        $tempPath = Str::random(32);
        File::copy($filePath, Storage::disk('public')->path($tempPath));

        $uploadService->processAudioMedia($song, $request, $tempPath, $data['bitRate']);

        $song->approved = DefaultConstants::TRUE;
        $song->user_id = auth()->id();

        $song->save();

        return $song;
    }
}
