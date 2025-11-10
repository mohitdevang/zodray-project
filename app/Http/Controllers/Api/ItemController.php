<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/items",
     *     summary="List available items",
     *     tags={"Items"},
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Items list")
     * )
     */
    public function index(Request $request)
    {
        $query = Item::query()->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $items = $query->orderBy('name')->paginate(20);

        $limit = $items->perPage();
        $currentPage = $items->currentPage();
        $offset = ($currentPage - 1) * $limit;

        return response()->json([
            'status' => true,
            'count' => $items->count(),
            'total_count' => $items->total(),
            'limit' => $limit,
            'offset' => $offset,
            'data' => $items->items(),
        ]);
    }
}


