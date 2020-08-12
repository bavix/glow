<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\FileStore;
use App\Http\Requests\FileUpdate;
use App\Http\Resources\FileResource;
use App\Http\Resources\ViewResource;
use App\Models\Bucket;
use App\Models\File;
use App\Services\FileService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class FileController extends BaseController
{

    /**
     * @param Request $request
     * @param Bucket $bucket
     * @return AnonymousResourceCollection
     */
    public function index(Request $request, Bucket $bucket): AnonymousResourceCollection
    {
        return FileResource::collection(
            $this->queryBuilder($request)
                ->paginate()
        );
    }

    /**
     * @param Request $request
     * @param Bucket $bucket
     * @param string $route
     * @return ViewResource
     */
    public function show(Request $request, Bucket $bucket, string $route): FileResource
    {
        return FileResource::make(
            $this->queryBuilder($request)
                ->where('route', $route)
                ->firstOrFail()
        );
    }

    /**
     * @param FileStore $fileRequest
     * @param Bucket $bucket
     * @return JsonResponse
     */
    public function store(FileStore $fileRequest, Bucket $bucket): JsonResponse
    {
        $visibilities = (array)$fileRequest->input('visibility');
        $routes = (array)$fileRequest->input('route');
        $files = $fileRequest->file('file');
        if (!\is_array($files)) {
            $files = [$files];
        }

        // fixme: locale
        \abort_if(
            array_keys($routes) !== array_keys($files),
            422,
            'Invalid count of files or routes'
        );

        $models = [];
        foreach ($files as $key => $file) {
            $visibility = (bool)($visibilities[$key] ?? true);
            $type = app(FileService::class)->getFileType($file);
            $route = app(FileService::class)->storeAs($file, $bucket->name . '/' . \ltrim($routes[$key], '/'), [
                'visibility' => $visibility,
            ]);

            if ($route) {
                $models[] = File::firstOrCreate([
                    'user_id' => Auth::id(),
                    'route' => $route,
                ], [
                    'bucket_id' => $bucket->getKey(),
                    'visibility' => $visibility,
                    'type' => $type,
                ]);
            }
        }

        // fixme: locale
        \abort_if(!$models, 400, 'Failed to upload files');
        return FileResource::collection($models)
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @param FileUpdate $fileRequest
     * @param Bucket $bucket
     * @param int $fileId
     */
    public function update(FileUpdate $fileRequest, Bucket $bucket, int $fileId): FileResource
    {
        /**
         * @var File $file
         */
        $file = $this->query($fileRequest)
            ->whereKey($fileId)
            ->firstOrFail();

        abort_if(
            !app(FileService::class)->moveTo($file, $fileRequest->input('visibility')),
            423,
            'Locked'
        );

        return FileResource::make($file);
    }

    /**
     * @param Request $request
     * @param Bucket $bucket
     * @param int $fileId
     * @return Response
     * @throws
     */
    public function destroy(Request $request, Bucket $bucket, int $fileId): Response
    {
        $results = $this->query($request)
            ->whereKey($fileId)
            ->delete();

        // fixme: locale
        \abort_if(!$results, 404, 'File not found');
        return \response()->noContent();
    }

    /**
     * @inheritdoc
     */
    protected function query(Request $request): Builder
    {
        /**
         * @var Bucket $bucket
         */
        $bucket = $request->route()
            ->parameter('bucket');

        return $bucket->files()->getQuery();
    }

}
