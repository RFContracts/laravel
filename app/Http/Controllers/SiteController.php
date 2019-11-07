<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App;


class SiteController extends Controller
{

    /**
     * @param Request $request
     * @return array|string
     */
    public function actionUpdate(Request $request)
    {
        $post = $request->post();
        $statusCode = 400;
        $process = \config('config.process');
        $response = [];
        if ($post == null) {
            return $this->MessageResponse($statusCode);
        } else {
            $statusCode = 200;
            $task = new App\Task();
            $api = new App\Api();
            $telegram = new App\Telegram();
            $validator = Validator::make($request->all(), $api->rules());
            if ($validator->fails()) {
                $statusCode = 422;
                return $this->MessageResponse($statusCode);

            }

            $cameras = null;
            if (!$this->isValidProcess($process, $post)) {
                $statusCode = 422;
                return $this->MessageResponse($statusCode);
            }

            $cameras = $process[$post['process_id']];
            foreach ($cameras as $camera) {
                $user = config('config.user');
                $password = config('config.password');
                $camera_id = $camera;
                $name = 'pizzaApi';
                $message = $post['name'] . '. ' . $post['toppings'];
                $filename = sha1(time()) . '.jpg';

                try {
                    $screenshot = $api->createScreenshot($name, $camera_id, $user, $password);

                    if ($screenshot['status'] != '200') {
                        $response[$camera_id]['createScreenshot'] = $screenshot;
                    };

                    $textscreen = $api->setTextScreen($name, $camera_id, $message, $user, $password);
                    if ($textscreen['status'] != '200') {
                        $response[$camera_id]['setTextScreen'] = $textscreen;
                    }
                    usleep(200000);
                    $saved = $api->saveScreen($camera_id, $filename, $user, $password);

                    if ($saved['status'] == 200) {
                        $telegram->setMessage($filename);
                        $api->setTextDelete($name, $camera_id, $user, $password);
                        $api->deleteScreenshot($name, $camera_id, $user, $password);
                        $response[$camera_id]['response'] = ['status' => '200', 'message' => 'File name: ' . $filename];

                    } else {
                        $statusCode = $saved['message'];
                        $response[$camera_id]['response'] = [
                            'status' => $statusCode,
                            'message' => ['code' => $saved['message'], 'error' => $saved['status']]];
                    }
                } catch (\Exception $ex) {
                    $statusCode = 422;
                    $response[$camera_id] = [
                        'status' => $statusCode,
                        'message' => ['code' => $ex->getCode(), 'error' => $ex->getMessage()]];
                }
                $task->create(['process_id' => $request->process_id, 'url' => $request->url,
                    'post' => json_encode($request->all()), 'headers' => json_encode($request->headers->all()),
                    'status' => json_encode($response[$camera_id]['response']['status']),
                    'message' => json_encode($response[$camera_id]['response']['message'])]);
            }

            return response(json_encode($response), $statusCode);

        }

    }

    /**
     * @param $statusCode
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function MessageResponse($statusCode)
    {
        return response([
            'status' => $statusCode,
            "message" => "Unknown post",
        ], $statusCode);
    }

    /**
     * @param $process
     * @param $post
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function isValidProcess($process, $post)
    {
        if (isset($process[$post['process_id']])) {
            return true;
        }
        return false;

    }
}
