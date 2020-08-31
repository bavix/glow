<?php

namespace App\Observers;

use App\Jobs\BucketDrop;
use App\Models\Bucket;

class BucketObserver
{

    /**
     * @param Bucket $bucket
     * @return bool
     */
    public function deleting(Bucket $bucket): bool
    {
        BucketDrop::dispatch($bucket);
        return false;
    }

}
