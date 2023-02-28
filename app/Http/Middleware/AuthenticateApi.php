<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class AuthenticateApi extends Middleware
{
    protected function unauthenticated($request, array $guards)
    {
        throw new AuthenticationException(
            'Invalid Token', $guards, $this->redirectTo($request)
        );
    }

    protected function authenticate($request, array $guards)
    {
        $token = $request->query('token');

        if (empty($token)) {
            $token = $request->bearerToken();
        }

        $api_user = User::where('token', $token)->get();
        if (count($api_user) === 1) {
            $request->user = collect([
                "id" => $api_user[0]->id,
                "export_type" => $api_user[0]->is_full_export,
            ]); 
            return;
        }
        $this->unauthenticated($request, $guards);
        
    }
}
