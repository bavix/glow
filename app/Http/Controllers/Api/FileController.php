<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Files\FileDrop;
use App\Http\Requests\Files\FileIndex;
use App\Http\Requests\Files\FileInvite;
use App\Http\Requests\Files\FileShow;
use App\Http\Requests\Files\FileStore;
use App\Http\Requests\Files\FileEdit;
use App\Http\Resources\FileResource;
use App\Http\Resources\InviteResource;
use App\Models\Bucket;
use App\Models\File;
use App\Services\FileService;
use App\Services\InviteService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends BaseController
{

    /**
     * @param FileIndex $request
     * @param Bucket $bucket
     * @return AnonymousResourceCollection
     */
    public function index(FileIndex $request, Bucket $bucket): AnonymousResourceCollection
    {
        return FileResource::collection(
            $this->queryBuilder($request)
                ->allowedIncludes(['colors', 'palette'])
                ->paginate()
        );
    }

    /**
     * @param FileShow $request
     * @param Bucket $bucket
     * @param string $route
     * @return FileResource
     */
    public function show(FileShow $request, Bucket $bucket, string $route): FileResource
    {
        return FileResource::make(
            $this->queryBuilder($request)
                ->allowedIncludes(['colors', 'palette'])
                ->where('route', $bucket->name . '/' . \ltrim($route, '/'))
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
                'force' => $fileRequest->input('force', false),
            ]);

            if ($route) {
                $diskName = app(FileService::class)
                    ->getDisk($visibility);

                $models[] = File::firstOrCreate([
                    'user_id' => Auth::id(),
                    'route' => $route,
                ], [
                    'bucket_id' => $bucket->getKey(),
                    'visibility' => $visibility,
                    'type' => $type,
                    'extra' => Storage::disk($diskName)
                        ->getMetadata($route),
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
     * @param FileEdit $fileRequest
     * @param Bucket $bucket
     * @param string $route
     */
    public function update(FileEdit $fileRequest, Bucket $bucket, string $route): FileResource
    {
        /**
         * @var File $file
         */
        $file = $this->query($fileRequest)
            ->where('route', $bucket->name . '/' . \ltrim($route, '/'))
            ->firstOrFail();

        abort_if(
            !app(FileService::class)
                ->moveTo($file, $fileRequest->input('visibility')),
            423,
            'Locked'
        );

        return FileResource::make($file);
    }

    /**
     * @param FileDrop $request
     * @param Bucket $bucket
     * @param string $route
     * @return Response
     * @throws
     */
    public function destroy(FileDrop $request, Bucket $bucket, string $route): Response
    {
        $file = $this->query($request)
            ->where('route', $bucket->name . '/' . \ltrim($route, '/'))
            ->firstOrFail();

        $file->delete();

        return \response()->noContent();
    }

    /**
     * @param FileInvite $request
     * @param Bucket $bucket
     * @param string $route
     * @return InviteResource
     */
    public function invite(FileInvite $request, Bucket $bucket, string $route): InviteResource
    {
        /**
         * @var File $file
         */
        $file = $this->query($request)
            ->where('route', $bucket->name . '/' . \ltrim($route, '/'))
            ->firstOrFail();

        \abort_if($file->visibility, 406, 'Public access file');
        $expires_at = Carbon::parse($request->expires_at)->timestamp;

        return InviteResource::make(
            app(InviteService::class)
                ->makeInvite($file, $expires_at)
        );
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
