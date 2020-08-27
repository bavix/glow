<?php

namespace App\Services;

use App\Models\File;
use App\Models\Invite;
use Ramsey\Uuid\Uuid;

class InviteService
{

    /**
     * Checking expire invite
     *
     * @param Invite $invite
     * @return bool
     */
    public function checkInvite(Invite $invite): bool
    {
        return $invite->expires_at->lessThan(now());
    }

    /**
     * @param File $file
     * @param int $expiresAt
     * @return Invite
     */
    public function makeInvite(File $file, int $expiresAt = 30): Invite
    {
        do {
            try {
                $invite = new Invite();
                $invite->id = Uuid::uuid4()->toString();
                $invite->user_id = $file->user_id;
                $invite->setRelation('file', $file); // lazy load
                $invite->file_id = $file->getKey();
                $invite->bucket_id = $file->bucket_id;
                $invite->expires_at = $expiresAt;
                $invite->save();
            } catch (\Throwable $throwable) {
                $invite = null;
                continue;
            }
        } while (!$invite);

        return $invite;
    }

}
