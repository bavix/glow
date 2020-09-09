<?php

namespace App\Http\Controllers;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class Welcome extends Controller
{

    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Welcome/Index', [
            'user' => [
                'name' => 'my friend',
            ]
        ]);
    }

}
