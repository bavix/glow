<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ViewStore;
use App\Http\Resources\ViewResource;
use App\Models\Bucket;
use App\Models\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ViewController extends BaseController
{

    /**
     * @param Request $request
     * @param Bucket $bucket
     * @return AnonymousResourceCollection
     */
    public function index(Request $request, Bucket $bucket): AnonymousResourceCollection
    {
        return ViewResource::collection(
            $this->queryBuilder($request)
                ->paginate()
        );
    }

    /**
     * @param Request $request
     * @param Bucket $bucket
     * @param string $name
     * @return ViewResource
     */
    public function show(Request $request, Bucket $bucket, string $name): ViewResource
    {
        return ViewResource::make(
            $this->queryBuilder($request)
                ->where('name', $name)
                ->first()
        );
    }

    /**
     * @param ViewStore $viewRequest
     * @param Bucket $bucket
     * @return JsonResponse
     */
    public function store(ViewStore $viewRequest, Bucket $bucket): JsonResponse
    {
        $view = View::create(\array_merge($viewRequest->validated(), [
            'bucket_id' => $bucket->getKey(),
        ]));

        return ViewResource::make($view)
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @param Request $request
     * @param Bucket $bucket
     * @param string $name
     * @return Response
     * @throws
     */
    public function destroy(Request $request, Bucket $bucket, string $name): Response
    {
        $results = $this->query($request)
            ->where('name', $name)
            ->delete();

        // fixme: locale
        \abort_if(!$results, 404, 'View not found');
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

        return $bucket
            ->views()
            ->getQuery();
    }

}
