<?php

class IsAdmin
{
    public function handle($request, $next)
    {
        if ($request->user->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }
        return $next($request);
    }
}

class IsTeacher
{
    public function handle($request, $next)
    {
        if ($request->user->role !== 'teacher') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }
        return $next($request);
    }
}

?>