<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

Route::get('/', function () {
    return view('welcome');
});

Route::post('sms', function (Request $request) {
    $valor_requeridos_inicializador = array('numero', 'sms');
    // inpunts obligatorios
    $numero = $request->input('numero');
    $sms = $request->input('sms');
    $sms = mb_substr($sms, 0, 264);
    //utilidades para la api 
    $id = $request->token_info['id'];
    $table = 'claves_authorizadas';
    $column = 'usos_disponible';
    $ip = $request->ip();
    $caracteres = mb_strlen($sms);
    $valorRestar = ($caracteres > 160) ? 2 : 1;

    if ($request->token_info['sms_disponible'] < $valorRestar) {
        return response(['mensaje' => 'Lamentablemente, tu saldo actual no es suficiente para enviar este mensaje. Te recomendamos contratar otro paquete para poder completar el envío.'], 402);
    }
    if (getenv('white_list')) {
        $jwt_decode = JWT::decode($request->token_info['token'], new Key(getenv('JWT_SECRET'), 'HS256'));
        if ($ip === $jwt_decode->ip_autorizada) {
            return response(['mensaje' => 'Acceso denegado'], 403);
        }
    }
    if ($request->filled($valor_requeridos_inicializador)) {
        $url = getenv('URI_SMS_API');
        $headers = [
            "Content-Type: application/json",
            "Authorization: " . getenv('Authorization_SMS_API')
        ];
        $data = [
            "apiKey" => getenv('ApiKey_SMS_API'),
            "country" => getenv('country_SMS_API'),
            "dial" => 26262,
            "message" => $sms,
            "msisdns" => array($numero),
            "tag" => getenv('tag_SMS_API')
        ];
        $jsonData = json_encode($data);
        $options = array(
            "http" => array(
                "method" => "POST",
                "header" => implode("\r\n", $headers),
                "content" => $jsonData
            )
        );
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        if ($response === false) {
            return response('Error en la solicitud', 500);
        } else {
            $response = json_decode($response, true);
            if ($response["code"] == 0) {
                DB::table($table)
                    ->where('id', $id)
                    ->decrement($column, $valorRestar);
                DB::insert('insert into registro_de_envios_sms (sistema_id,sms,caracteres,numero_envio,ip_api) values (?, ?, ?, ?, ?)', [$id, $sms, $caracteres, $numero, $ip]);
                return response(['mensaje' => 'envío exitoso', 'caracteres' => $caracteres, 'coste_envio' => $valorRestar], 200);
            } else {
                $data = [
                    "code" => $response["code"],
                    "message" => $response["message"]
                ];
                return response()->json($data, 401);
            }
        }
    } else {
        return response(['mensaje' => 'Faltan campos obligatorios: número y mensaje a enviar.'], 401);
    }

})->middleware('protected');

Route::post('balance', function (Request $request) {
    return response(['SMS_DISPONIBLES' => $request->token_info['sms_disponible']], 200);
})->middleware('protected');

Route::post('historial', function (Request $request) {
    $id = $request->token_info['id'];
    $registro_de_envios_sms = DB::table('registro_de_envios_sms')->where('sistema_id',$id)->get();
    return response()->json($registro_de_envios_sms, 200);
})->middleware('protected');