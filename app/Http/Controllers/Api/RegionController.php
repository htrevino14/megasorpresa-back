<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RegionResource;
use App\Models\Region;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RegionController extends Controller
{
    /**
     * Lista las regiones activas para el selector multi-mercado.
     */
    public function index(): AnonymousResourceCollection
    {
        return RegionResource::collection(
            Region::active()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
        );
    }
}
