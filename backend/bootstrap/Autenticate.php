<?php

class Authenticate
{
    public function handle($request, $next)
    {
        $token = $request->header('Authorization');
        if (!$token) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        try {
            $decoded = JWT::decode($token, env('SECRET_KEY'), ['HS256']);
            $user = User::find($decoded->id);
            if (!$user) {
                return response()->json(['message' => 'Non authentifié'], 401);
            }
            if ($user->status === 'blocked') {
                return response()->json(['message' => 'Compte bloqué'], 403);
            }
            $request->user = $user;
            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Non authentifié'], response()->json(['message' => 'Non authentifié'], 401);
        }
    }
}

?>
