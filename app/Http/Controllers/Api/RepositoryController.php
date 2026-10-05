<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkripsiResource;
use App\Services\RepositoryService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RepositoryController extends Controller
{
    public function __construct(
        protected RepositoryService $repositoryService
    ) {}

    /**
     * Display a listing of approved skripsi repository.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['q', 'kategori_id', 'program_studi_id', 'tahun', 'per_page']);

        $skripsi = $this->repositoryService->getSkripsi($filters);

        return SkripsiResource::collection($skripsi);
    }

    /**
     * Display the specified approved skripsi.
     */
    public function show(string $id): SkripsiResource
    {
        $skripsi = $this->repositoryService->findSkripsi((int) $id);

        return new SkripsiResource($skripsi);
    }
}
