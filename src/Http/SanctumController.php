<?php

namespace Jeffbeltran\SanctumTokens\Http;

use Illuminate\Http\Request;
use Laravel\Nova\Nova;

class SanctumController
{
    public function tokens($resourceName, $id)
    {
        return Nova::modelInstanceForKey($resourceName)
            ->with('tokens')
            ->find($id);
    }

    public function createToken(Request $request, $resourceName, $id)
    {
        $user = Nova::modelInstanceForKey($resourceName)->find($id);

        $abilities = collect(explode(',', $request->abilities))
            ->map(function ($item) {
                return trim($item);
            })
            ->toArray();

        $token = $user->createToken($request->name, $abilities)->plainTextToken;
        $tokenValue = explode('|', $token, 2)[1];

        return $tokenValue;
    }

    public function revoke(Request $request, $resourceName, $id)
    {
        $user = Nova::modelInstanceForKey($resourceName)
            ->with('tokens')
            ->find($id);

        return $user
            ->tokens()
            ->whereKey($request->token_id)
            ->delete();
    }
}
