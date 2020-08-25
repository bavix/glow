<?php

namespace App\Http\Controllers\Sharing;

use App\Http\Requests\Sharing\FileInvite;
use Illuminate\Routing\Controller;
use App\Services\InviteService;
use App\Services\FileService;
use Illuminate\Http\Request;
use App\Models\Invite;
use App\Models\File;

class FileController extends Controller
{

    /**
     * @param Request $request
     * @param string $capsule
     * @param string $thumbnail
     * @param string $route
     */
    public function available(Request $request, string $capsule, string $thumbnail, string $route): void
    {
        $regex = '/^[^\.]+\.(?<ext>\w+)(?<webp>\.webp)?$/i';
        \preg_match($regex, basename($route), $matches);

        $webp = false;
        $ephemeralRoute = $route;
        if (isset($matches['webp'])) {
            $webp = true;
            $ephemeralRoute = \mb_substr($route, 0, \mb_strrpos($route, '.webp'));
        }

        $file = File::whereRoute($capsule . '/' . $ephemeralRoute)
            ->firstOrFail();

        \abort_if(!$file->visibility, 403);
        \abort_if(!isset($file->thumbs_urn[$thumbnail]), 404);
        \abort_if($file->type !== File::TYPE_IMAGE, 400);

        $urn = $capsule . '/' . $thumbnail . '/' . $route;
        $mimeType = $file->extra['mime'] ?? 'image/jpeg';
        if ($webp) {
            $mimeType = 'image/webp';
        }

        \header('Expires: ' . \gmdate('D, d M Y H:i:s \G\M\T', now()->addWeek()->timestamp));
        \header('Content-Disposition: inline;filename="' . \basename($urn) . '"');
        \header('X-Accel-Redirect: /thumbs/' . $urn);
        \header('Content-Type: ' . $mimeType);
        die;
    }

    /**
     * Downloading private files using an invite
     *
     * @param FileInvite $request
     * @param string $capsule
     * @param string $route
     */
    public function invite(FileInvite $request, string $capsule, string $route): void
    {
        // get invite info
        $key = $request->input('key');
        $invite = Invite::findOrFail($key);
        $file = $invite->file;

        // check file
        \abort_if($file->route !== $capsule . '/' . $route, 404);
        \abort_if(!app(FileService::class)->exists($file), 404);
        \abort_if($file->visibility, 404);

        // validate invite
        \abort_if(app(InviteService::class)->checkInvite($invite), 403);

        \header('Expires: ' . \gmdate('D, d M Y H:i:s \G\M\T', $invite->expires_at->timestamp));
        \header('Content-Disposition: inline;filename="' . \basename($file->route) . '"');
        \header('Content-Type: ' . ($file->extra['mime'] ?? 'plain/text'));
        \header('X-Accel-Redirect: /private/' . $file->route);
        die;
    }

}
