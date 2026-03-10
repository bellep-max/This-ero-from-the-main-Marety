<?php

namespace App\Http\Controllers\Frontend\Upload;

use App\Enums\AdventureSongTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Adventure\AdventureHeadingUploadRequest;
use App\Http\Resources\Adventure\AdventureTypeResource;
use App\Models\Adventure;
use App\Services\AdventureService;
use Illuminate\Http\JsonResponse;

class AdventureHeadingUploadController extends Controller
{
    public function __construct(private readonly AdventureService $adventureService) {}

    public function store(AdventureHeadingUploadRequest $request): AdventureTypeResource|JsonResponse
    {
        $result = $this->adventureService->handle(
            [
                ...$request->all(),
                'type' => AdventureSongTypeEnum::Heading,
            ],
            $request->file('file'),
            $request->hasFile('artwork') ? $request->file('artwork') : null
        );

        return class_basename($result) === class_basename(Adventure::class)
            ? AdventureTypeResource::make($result)
            : response()->json($result);
    }

    public function destroy(Adventure $adventure): JsonResponse
    {
        $adventure->media()->delete();
        $adventure->delete();

        return response()->json([
            'message' => 'Adventure deleted successfully.',
        ]);
    }
    //
    //
    //    public function adventureDiagram(Request $request)
    //    {
    //        //        dd($request->routeIs('frontend.diagram'));
    //        if ($request->id) {
    //            $song = Song::query()
    //                ->withoutGlobalScopes()
    //                ->find($request->id);
    //            $adventure = $this->editMode($song, $request);
    //        } else {
    //            $data = $request->session()->all();
    //            $adventure = @$data[$this->adventureSessionKey];
    //            // checking deletions
    //            $childOrder = 0;
    //            if (! empty($adventure['children'])) {
    //                foreach ($adventure['children'] as $k => $child) {
    //                    $song = ChildSong::query()
    //                        ->withoutGlobalScopes()
    //                        ->find($child['id']);
    //
    //                    if (array_key_exists('deleted', $child)) {
    //                        $song->delete();
    //                        unset($adventure['children'][$k]);
    //                    } else {
    //                        $song->order = $childOrder;
    //                        $song->title = $child['title'];
    //                        $song->description = $child['short_description'];
    //                        $childOrder++;
    //                        $song->save();
    //
    //                        $finalOrder = 0;
    //                        foreach ($child['finals'] as $i => $final) {
    //                            if (array_key_exists('deleted', $final)) {
    //                                $final->delete();
    //                                unset($adventure['children'][$k]['finals'][$i]);
    //                            } else {
    //                                FinalSong::query()
    //                                    ->withoutGlobalScopes()
    //                                    ->find($final['final_id'])
    //                                    ->update([
    //                                        'order' => $finalOrder,
    //                                        'title' => $child['finals'][$i]['title'],
    //                                        'short_description' => $child['finals'][$i]['short_description'],
    //                                    ]);
    //
    //                                $finalOrder++;
    //                            }
    //                        }
    //                    }
    //                }
    //            }
    //        }
    //
    //        $request->session()->put($this->adventureSessionKey, []);
    //
    //        if (! count($adventure)) {
    //            return;
    //        }
    //
    //        $view = View::make('adventure-upload.diagram')
    //            ->with([
    //                'data' => $adventure,
    //            ]);
    //
    //        return $request->ajax()
    //            ? $this->ajaxViewService->getRenderedSections($view, $request, $this->ajaxViewService::NO_CHECK)
    //            : $view;
    //    }
    //
    //    public function adventureValidate(Request $request): JsonResponse
    //    {
    //        $result = [];
    //        $childrenReq = $request->children;
    //        $this->updateSession($request, $childrenReq);
    //        $data = $request->session()->all();
    //        $children = $data[$this->adventureSessionKey]['children'];
    //
    //        $hasErrors = false;
    //
    //        foreach ($children as $key => $child) {
    //            if (array_key_exists('deleted', $child)) {
    //                continue;
    //            }
    //
    //            $validator = Validator::make($child, [
    //                'title' => 'required|max:100',
    //                'short_description' => 'required|max:100',
    //                'child_file_link' => 'required',
    //            ]);
    //
    //            $result['child_'.$key] = [];
    //            $errors = $validator->errors();
    //            $errorMessages = $errors->all();
    //
    //            if (count($errorMessages)) {
    //                $hasErrors = true;
    //                foreach ($errorMessages as $error) {
    //                    $result['child_'.$key][] = $error;
    //                }
    //            } else {
    //                if ($child['id']) {
    //                    $childSong = ChildSong::query()
    //                        ->withoutGlobalScopes()
    //                        ->find($child['id']);
    //
    //                    if ($childSong) {
    //                        $childSong->update([
    //                            'title' => $child['title'],
    //                            'description' => $child['short_description'],
    //                        ]);
    //                    }
    //                }
    //            }
    //
    //            foreach ($child['finals'] as $finalKey => $final) {
    //                if (array_key_exists('deleted', $final)) {
    //                    continue;
    //                }
    //
    //                $finalValidator = Validator::make($final, [
    //                    'title' => 'required|max:100',
    //                    'short_description' => 'required|max:100',
    //                    'final_file_link' => 'required',
    //                ]);
    //
    //                $finalErrors = $finalValidator->errors();
    //                $finalErrorsMessages = $finalErrors->all();
    //
    //                if (count($finalErrorsMessages)) {
    //                    $hasErrors = true;
    //                    $result['child_'.$key]['final_errors'][$finalKey] = [];
    //
    //                    foreach ($finalErrorsMessages as $finalErrorsMessage) {
    //                        $result['child_'.$key]['final_errors'][$finalKey][] = $finalErrorsMessage;
    //                    }
    //                } else {
    //                    if ($final['final_id']) {
    //                        $finalSong = FinalSong::query()
    //                            ->withoutGlobalScopes()
    //                            ->find($final['final_id']);
    //
    //                        if ($finalSong) {
    //                            $finalSong->update([
    //                                'title' => $final['title'],
    //                                'description' => $final['short_description'],
    //                            ]);
    //                        }
    //                    }
    //                }
    //            }
    //        }
    //
    //        if (! $hasErrors) {
    //            $request->session()->put($this->adventureSessionKey, []);
    //        }
    //
    //        $view = (string) View::make('adventure-upload.child-errors')
    //            ->with([
    //                'result' => $result,
    //            ]);
    //
    //        return response()->json([
    //            'view' => $view,
    //            'hasErrors' => $hasErrors,
    //        ]);
    //    }
}
