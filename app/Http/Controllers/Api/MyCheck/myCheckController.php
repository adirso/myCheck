<?php

namespace App\Http\Controllers\Api\MyCheck;

use GuzzleHttp\Client;
use App\Http\Requests\myCheckRequest;
use Illuminate\Routing\Controller as BaseController;

class myCheckController extends BaseController
{
    /**
     * Get item recommended
     *
     * @param myCheckRequest $request
     *
     * @return array
     */
    public function index(myCheckRequest $request): array
    {
        return $this->fetchItems ( $request->get('itemId'), $request->get('depth'));
    }

    /**
     * Fetch item recursively by depth.
     *
     * @param int $id
     * @param int $depth
     *
     * @return array
     */
    private function fetchItems(int $id, int $depth): array
    {
        if ($depth <= 1)
            return $this->fetchItem ( $id );

        $items = [];
        foreach ($this->fetchItem ( $id ) as $item) {
            $item['recommended'] = $this->fetchItems ( $item['itemID'], $depth - 1 );
            array_push ( $items, $item );
        }

        return $items;
    }

    /**
     * Fetch data from API for the requested id.
     *
     * @param int $id
     *
     * @return array
     */
    private function fetchItem(int $id): array
    {
        $baseUrl = config('api.url');
        $client = new Client();
        $res = $client->get ( "{$baseUrl}?item={$id}" );
        return json_decode ( $res->getBody (), 1 );
    }
}
