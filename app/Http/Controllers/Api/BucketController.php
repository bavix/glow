<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\BucketStore;
use App\Http\Resources\BucketResource;
use App\Models\Bucket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BucketController extends BaseController
{

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return BucketResource::collection(
            $this->queryBuilder($request)
                ->paginate()
        );
    }

    /**
     * @param Request $request
     * @param Bucket $bucket
     * @return BucketResource
     */
    public function show(Request $request, Bucket $bucket): BucketResource
    {
        return BucketResource::make($bucket);
    }

    /**
     * @param BucketStore $bucketRequest
     * @return JsonResponse
     */
    public function store(BucketStore $bucketRequest): JsonResponse
    {
        $validated = $bucketRequest->validated();
        $bucket = Bucket::create(\array_merge($validated, [
            'user_id' => Auth::id(),
        ]));

        return BucketResource::make($bucket)
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @param Request $request
     * @param Bucket $bucket
     * @return Response
     * @throws
     */
    public function destroy(Request $request, Bucket $bucket): Response
    {
        // fixme: locale
        \abort_if(!$bucket->delete(), 404, 'Bucket not found');
        return \response()->noContent();
    }

    /**
     * @inheritdoc
     */
    protected function query(Request $request): Builder
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        return $user->buckets()->getQuery();
    }

}
