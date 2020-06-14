<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Manager;

class Controller extends BaseController
{
    /**
     * @var JWTAuth
     */
    protected $jwt;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * Controller constructor.
     *
     * @param JWTAuth $jwt
     * @param Manager $manager
     */
    public function __construct(JWTAuth $jwt, Manager $manager)
    {
        $this->jwt = $jwt;
        $this->manager = $manager;
    }

    protected function success($data, $code, $message = '')
    {
        return response()->json(['result' => $data, 'message' => $message, 'code' => $code], $code);
    }

    protected function error($message, $code)
    {
        return response()->json(['message' => $message], $code);
    }

    /**
     * send response to ajax request
     *
     * @param string $message
     * @param null $data
     * @param int $statusCode
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseApi($data = null, $message = '', $statusCode = 200, $headers = []){

        $isEnkrip = false;

        if ($isEnkrip){
            $datax = json_encode($data);
            $encryption_key = env('KEY_API');
            $iv = env('KEY_API_IV');
            $data = openssl_encrypt($datax, 'aes-128-cbc', $encryption_key, 0, $iv);
        }

        $d = [
            'message' => $message,
            'data' => $data,
        ];

        return response($d,$statusCode,$headers);
    }

    /**
     * send ok response
     *
     * @param string $message
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseOkApi($data = [],string $message = "Resource found.", array $headers = [])
    {
        return $this->sendResponseApi($data,$message,200,$headers);
    }

    /**
     * send a not found response
     *
     * @param string $message
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseNotFoundApi(string $message = "Resource not found.", array $headers = [])
    {
        return $this->sendResponseApi([],$message,404,$headers);
    }

    /**
     * send a bad request response
     *
     * @param string $message
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseBadRequestApi(string $message = "Bad Request.", array $headers = [])
    {
        return $this->sendResponseApi([],$message,400,$headers);
    }

    /**
     * send created response
     *
     * @param string $message
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseCreatedApi($data = [], string $message = "Tambah data berhasil.", array $headers = [])
    {
        return $this->sendResponseApi($data,$message,201,$headers);
    }

    /**
     * send updated response
     *
     * @param string $message
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseUpdatedApi($data = [],string $message = "Update data berhasil.", array $headers = [])
    {
        return $this->sendResponseApi($data,$message,200,$headers);
    }

    /**
     * send deleted response
     *
     * @param string $message
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseDeletedApi(string $message = "Delete data berhasil.",array $headers = [])
    {
        return $this->sendResponseApi([],$message,200,$headers);
    }

    /**
     * send Unprocessable Entity
     *
     * @param array $data
     * @param string $message
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseUnproccessApi($data = [], string $message = "Gagal memproses.",array $headers = [])
    {
        return $this->sendResponseApi($data,$message,422,$headers);
    }

    /**
     * send forbidden response
     *
     * @param string $message
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseForbiddenApi(string $message = "Action forbidden.",array $headers = [])
    {
        return $this->sendResponseApi([],$message,403,$headers);
    }

    /**
     * send unauthorized response
     *
     * @param string $message
     * @param array $headers
     * @return \Illuminate\Http\Response
     */
    public function sendResponseUnauthorizedApi(string $message = "Unauthorized.",array $headers = [])
    {
        return $this->sendResponseApi([],$message,401,$headers);
    }

    /**
     * send no content
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponseNoContentApi()
    {
        return $this->sendResponseApi(null,204);
    }
}