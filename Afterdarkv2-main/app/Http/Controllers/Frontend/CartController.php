<?php

namespace App\Http\Controllers\Frontend;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Services\AjaxViewService;
use Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use View;

class CartController extends Controller
{
    private $request;

    private AjaxViewService $ajaxViewService;

    public function __construct(Request $request, AjaxViewService $ajaxViewService)
    {
        $this->request = $request;
        $this->ajaxViewService = $ajaxViewService;
    }

    public function index()
    {
        $view = View::make('cart.index')
            ->with([
                'cart' => Cart::getContent(),
            ]);

        if ($this->request->ajax()) {
            return $this->ajaxViewService->getRenderedSections($view, $this->request, $this->ajaxViewService::NO_CHECK);
        }

        Helper::getMetatags();

        return $view;
    }

    public function overview(): JsonResponse
    {
        $items = [];

        foreach (Cart::getContent() as $item) {
            $items[] = $item;
        }

        $cart = new \stdClass;
        $cart->items = $items;
        $cart->subtotal = number_format(Cart::getSubTotal(), 2);
        $cart->currency = config('settings.currency', 'USD');

        return response()->json($cart);
    }

    public function add(): JsonResponse
    {
        $this->request->validate([
            'orderable_id' => 'required|int',
            'orderable_type' => 'required|string|in:App\Models\Album,App\Models\Song',
        ]);

        $item = (new $this->request->orderable_type)::findOrFail($this->request->orderable_id);
        $item->key = $this->request->orderable_type . '\\' . $item->id;

        if (!array_key_exists($item->key, Cart::getContent()->toArray())) {
            Cart::add([
                'id' => $item->key,
                'name' => $item->title,
                'price' => $item->price,
                'quantity' => 1,
                'attributes' => [
                    'orderable_id' => $this->request->orderable_id,
                    'orderable_type' => $this->request->orderable_type,
                ],
                'associatedModel' => $item,
            ]);
        }

        return $this->overview();
    }

    public function remove(): JsonResponse
    {
        $this->request->validate([
            'id' => 'required|string',
        ]);

        Cart::remove($this->request->id);

        return $this->overview();
    }
}
