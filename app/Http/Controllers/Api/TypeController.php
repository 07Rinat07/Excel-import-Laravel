<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Type\TypeResource;
use App\Models\Type;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

class TypeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/types",
     *     tags={"Types"},
     *     summary="List project types",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Type::class);

        $types = Type::query()
            ->orderBy('title')
            ->get();

        return TypeResource::collection($types);
    }
}
