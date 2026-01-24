<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Template\ExcelTemplateResource;
use App\Models\ExcelTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Annotations as OA;

class TemplateController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/templates",
     *     tags={"Templates"},
     *     summary="List templates with columns",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="type_id",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="active",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="boolean")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $data = $request->validate([
            'type_id' => 'nullable|integer|exists:types,id',
            'active' => 'nullable|boolean',
        ]);

        $active = $data['active'] ?? null;
        if ($active !== null) {
            $active = filter_var($active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        $templates = ExcelTemplate::query()
            ->with('columns')
            ->when(isset($data['type_id']), fn ($query) => $query->where('type_id', $data['type_id']))
            ->when($active !== null, fn ($query) => $query->where('is_active', $active))
            ->orderBy('name')
            ->get();

        return ExcelTemplateResource::collection($templates);
    }
}
