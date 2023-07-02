<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\DB;

class MiddlewareProtice
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure $next
     * @param string[] $guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authorizationHeader = $request->header('Authorization');
        $authorization = substr($authorizationHeader, 7);
        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return response(['mensaje' => 'No se proporcionó el token Bearer'], 401);
        }
        try {
            JWT::decode($authorization, new Key(getenv('JWT_SECRET'), 'HS256'));
        } catch (\Firebase\JWT\SignatureInvalidException $th) {
            return response(['mensaje' => $th->getMessage()], 401);
        }
        $pase_temporal = DB::table('claves_authorizadas')->where('token', $authorization)->first();
        $return_info_user = [
            'id' => $pase_temporal->id,
            'sms_disponible' => $pase_temporal->usos_disponible,
            'token'=>$pase_temporal->token
        ];

        $request->merge((['token_info' => $return_info_user]));
   
        return $next($request);
    }
}