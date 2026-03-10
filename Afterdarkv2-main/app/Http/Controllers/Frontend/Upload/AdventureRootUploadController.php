<?php

namespace App\Http\Controllers\Frontend\Upload;

use App\Enums\AdventureSongTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Adventure\AdventureRootUploadRequest;
use App\Http\Resources\Adventure\AdventureTypeResource;
use App\Models\Adventure;
use App\Services\AdventureService;
use Illuminate\Http\JsonResponse;

class AdventureRootUploadController extends Controller
{
    public function __construct(private readonly AdventureService $adventureService) {}

    public function store(AdventureRootUploadRequest $request): AdventureTypeResource|JsonResponse
    {
        $result = $this->adventureService->handle(
            [
                ...$request->all(),
                'type' => AdventureSongTypeEnum::Root,
            ],
            $request->file('file'),
            $request->hasFile('artwork') ? $request->file('artwork') : null
        );

        foreach ($request->input('finals') as $index => $final) {
            $this->adventureService->handle(
                [
                    ...$final,
                    'parent_id' => $result->id,
                    'type' => AdventureSongTypeEnum::Final,
                ],
                $request->file("finals.$index.file"),
                $request->hasFile('artwork') ? $request->file("finals.$index.artwork") : null,
            );
        }

        session()->flash('message', [
            'level' => 'success',
            'content' => 'Root was successfully uploaded.',
        ]);

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
    //    public function uploadBeat(FileUploadRequest $request): JsonResponse
    //    {
    //        $data = $request->session()->all();
    //
    //        $request->request->add(['is_adventure' => DefaultConstants::TRUE]);
    //        $request->request->add(['genre' => $data[$this->adventureSessionKey]['adventure_genres']]);
    //        $request->request->add(['adventure_title' => $data[$this->adventureSessionKey]['adventure_title']]);
    //        $request->request->add(['adventure_tags' => $data[$this->adventureSessionKey]['adventure_tags']]);
    //        $request->request->add(['adventure_gender' => $data[$this->adventureSessionKey]['adventure_gender']]);
    //
    //        if (isset($data[$this->adventureSessionKey]['adventure_id'])) {
    //            $request->request->add(['adventure_id' => $data[$this->adventureSessionKey]['adventure_id']]);
    //        }
    //
    //        $response = $this->uploadService->handle($request, TrackTypeConstants::SONG);
    //
    //        if ($response) {
    //            $data[$this->adventureSessionKey]['adventure_id'] = $response->id;
    //            $data[$this->adventureSessionKey]['parent_file_link'] = $response->permalink;
    //            $data[$this->adventureSessionKey]['parent_artwork'] = $response->artwork;
    //            $request->session()->put($this->adventureSessionKey, $data[$this->adventureSessionKey]);
    //
    //            $data = $request->session()->all();
    //            $adventureData = $data[$this->adventureSessionKey];
    //            $adventureData['query_status'] = 'ok';
    //
    //            return response()->json($adventureData);
    //        }
    //
    //        return response()->json($response);
    //    }
    //
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
    //    public function updateSession(Request $request, $childrenReq): void
    //    {
    //        $data = $request->session()->all();
    //        $adventure = $data[$this->adventureSessionKey];
    //
    //        foreach ($adventure['children'] as $k => $child) {
    //            $adventure['children'][$k]['title'] = @$childrenReq[$k]['title'];
    //            $adventure['children'][$k]['short_description'] = @$childrenReq[$k]['description'];
    //
    //            foreach ($adventure['children'][$k]['finals'] as $j => $final) {
    //                $adventure['children'][$k]['finals'][$j]['title'] = @$childrenReq[$k]['finals'][$j]['title'];
    //                $adventure['children'][$k]['finals'][$j]['short_description'] = @$childrenReq[$k]['finals'][$j]['description'];
    //            }
    //        }
    //
    //        $request->session()->put($this->adventureSessionKey, $adventure);
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
    //
    //    public function clearAdventureFromSession(Request $request)
    //    {
    //        $request->session()->forget('name');
    //    }
    //
    //    public function getParentFileInfo(Request $request)
    //    {
    //        $data = $request->session()->all();
    //
    //        return $data[$this->adventureSessionKey];
    //    }
    //
    //    public function deleteMediaWhenReplaced($entity)
    //    {
    //        $entity->delete();
    //    }
    //
    //    public function uploadArtwork(ArtworkUploadRequest $request)
    //    {
    //        $id = $request->id;
    //
    //        $order = $request->order;
    //        $childNumber = $request->child_order;
    //        $data = $request->session()->all();
    //        $adventure = $data[$this->adventureSessionKey];
    //
    //        switch ($request->type) {
    //            case AdventureConstants::PARENT:
    //                $song = Song::query()->withoutGlobalScopes()->find($id);
    //                break;
    //            case AdventureConstants::CHILD:
    //                $song = ChildSong::query()->withoutGlobalScopes()->find($id);
    //                break;
    //            case AdventureConstants::FINAL:
    //                $song = FinalSong::query()->withoutGlobalScopes()->find($id);
    //                break;
    //            default:
    //                return null;
    //        }
    //
    //        if (! $song) {
    //            return 'not found';
    //        }
    //
    //        if ($request->hasFile('artwork')) {
    //            ArtworkService::updateArtwork($request, $song);
    //        }
    //
    //        switch ($request->type) {
    //            case AdventureConstants::PARENT:
    //                $adventure['parent_artwork_url'] = $song->artwork_url;
    //                break;
    //            case AdventureConstants::CHILD:
    //                $adventure['children'][$order]['child_artwork_url'] = $song->artwork_url;
    //                break;
    //            case AdventureConstants::FINAL:
    //                $adventure['children'][$childNumber]['finals'][$order]['final_artwork_url'] = $song->artwork_url;
    //                break;
    //            default:
    //                return null;
    //        }
    //
    //        $request->session()->put($this->adventureSessionKey, $adventure);
    //
    //        return $song;
    //    }
    //
    //    public function removeArtwork(Request $request)
    //    {
    //        $adventure = $request->session()->all()[$this->adventureSessionKey];
    //        $song = Song::query()
    //            ->withoutGlobalScopes()
    //            ->find($adventure['adventure_id']);
    //
    //        $song->clearMediaCollection('artwork');
    //        $adventure['parent_artwork_url'] = null;
    //        $request->session()->put($this->adventureSessionKey, $adventure);
    //
    //        return $song;
    //    }
    //
    //    public function deleteChildOrFinal(Request $request, $order, $childOrder = null): bool
    //    {
    //        $data = $request->session()->all();
    //        $adventure = $data[$this->adventureSessionKey];
    //
    //        if (! is_null($childOrder)) {
    //            // delete final
    //            $adventure['children'][$childOrder]['finals'][$order]['deleted'] = true;
    //        } else {
    //            // delete child
    //            $adventure['children'][$order]['deleted'] = true;
    //        }
    //
    //        $request->session()->put($this->adventureSessionKey, $adventure);
    //
    //        return true;
    //    }
}
