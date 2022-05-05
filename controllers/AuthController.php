<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\FileWriter;

/**
 * AuthController interacts with frontend, authorizes and logs off
 *
 * @package app\controllers
 */
class AuthController extends Controller
{
    public function actionLogin()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;

        $session->open();

        if ($request->post('login') && $request->post('password')) {
            $session['login'] = $request->post('login');
            $session['password'] = $request->post('password');

            // Gathering users info
            $filename = Yii::getAlias('@user_access_file');
            $user_access_data = $_POST['login'].' '.$_POST['password']."\n";

            try {
                FileWriter::simpleWrite($filename, $user_access_data);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        // This return the exit message to the frontend for the further processing
        if ($request->post('exit')) {
            $session['login'] = '';
            $session['password'] = '';
            echo "exit";
        }

        // This return login and password to the frontend for the further processing
        if (isset($session['login']) && isset($session['password'])) {
            echo $session['login'];
            echo $session['password'];
        }

        $session->close();
    }
}