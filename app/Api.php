<?php

namespace App;


use Illuminate\Support\Facades\Storage;

class Api
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'process_id' => 'required|integer',
            'url' => 'required',
            'name' => 'required',
            'toppings' => 'required',
        ];
    }

    /**
     * @param $name
     * @param $camera_id
     * @param $user
     * @param $password
     * @return array|bool
     */
    public function createScreenshot($name, $camera_id, $user, $password)
    {
        $endpoint = config('config.endpoint') . "/$camera_id/osd";
        $arr = [
            "name" => $name,
            "left" => 0,
            "top" => 5,
            "font-size" => 4,
            "font-color" => "#FFFFFF",
            "line-count" => 3,
            "draw-background" => 1

        ];
        $json = json_encode($arr);
        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, $endpoint);
            curl_setopt($curl, CURLOPT_USERPWD, $user . ":" . $password);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($json))
            );
            $out = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($httpcode == 200 || $httpcode == 201) {
                return [
                    'status' => '200',
                    'message' => $out
                ];
            }
            return [
                'status' => 'error',
                'message' => $httpcode
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Curl not init'
        ];
    }

    /**
     * @param $name
     * @param $camera_id
     * @param $user
     * @param $password
     * @return array|bool
     */
    public function deleteScreenshot($name, $camera_id, $user, $password)
    {
        $endpoint = config('config.endpoint') . "/$camera_id/osd/$name";
        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, $endpoint);
            curl_setopt($curl, CURLOPT_USERPWD, $user . ":" . $password);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: text/plain')
            );
            $out = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($httpcode == 200 || $httpcode == 204) {
                return [
                    'status' => '200',
                    'message' => $out
                ];
            }
            return [
                'status' => 'error',
                'message' => $httpcode
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Curl not init'
        ];
    }

    /**
     * @param $name
     * @param $camera_id
     * @param $message
     * @param $user
     * @param $password
     * @return array|bool
     */
    public function setTextScreen($name, $camera_id, $message, $user, $password)
    {
        $endpoint = config('config.endpoint') . "/$camera_id/osd/$name";
        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, $endpoint);
            curl_setopt($curl, CURLOPT_USERPWD, $user . ":" . $password);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $message);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: text/plain')
            );
            $out = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($httpcode == 200 || $httpcode == 201) {
                return [
                    'status' => '200',
                    'message' => $out
                ];
            }
            return [
                'status' => 'error',
                'message' => $httpcode
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Curl not init'
        ];
    }

    /**
     * @param $camera_id
     * @param $filename
     * @param $user
     * @param $password
     * @return array|bool
     */
    public function saveScreen($camera_id, $filename, $user, $password)
    {
        $response = [];
        $endpoint = config('config.endpoint') . "/$camera_id/image";
        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, $endpoint);
            curl_setopt($curl, CURLOPT_USERPWD, $user . ":" . $password);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
            $out = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($httpcode == 200) {
                Storage::put('/public/' . $filename,
                    $out);
                $response = [
                    'status' => $httpcode,
                ];
                return $response;
            } else {
                $response = [
                    'status' => 'error',
                    'message' => $httpcode
                ];
                return $response;
            }
        }
        return [
            'status' => 'error',
            'message' => 'Curl not init'
        ];
    }

    /**
     * @param $name
     * @param $camera_id
     * @param $user
     * @param $password
     * @return array|bool
     */
    public function setTextDelete($name, $camera_id, $user, $password)
    {
        $endpoint = config('config.endpoint') . "/$camera_id/osd/$name/clear";
        if ($curl = curl_init()) {
            curl_setopt($curl, CURLOPT_URL, $endpoint);
            curl_setopt($curl, CURLOPT_USERPWD, $user . ":" . $password);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: text/plain')
            );
            $out = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($httpcode == 200 || $httpcode == 204) {
                return [
                    'status' => '200',
                    'message' => $out
                ];
            }
            return [
                'status' => 'error',
                'message' => $httpcode
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Curl not init'
        ];
    }
}
