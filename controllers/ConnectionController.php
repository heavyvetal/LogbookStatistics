<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\CurlConnector;
use app\models\RequestData;
use app\models\Router;
use app\models\RouterTestData;
use app\models\RouterTestDataDB;
use app\models\FileReader;

/**
 * ConnectionController is responsible for connection and statistics delivery
 *
 * @package app\controllers
 *
 * @property string $test_login
 * @property string $test_password
 * @property bool $is_test
 */
class ConnectionController extends Controller
{
    private $test_login;
    private $test_password;
    private $is_test = false;

    public function actionPull()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;

        $this->test_login = Yii::getAlias('@test_login');
        $this->test_password = Yii::getAlias('@test_password');

        $session->open();

        // Creating a special api connector, file readers to get test data from the temporal local storage
        $curl_connector = new CurlConnector;
        $file_reader = new FileReader;

        // Getting static headers from a local storage
        $request_data = new RequestData();

        $login_headers = $request_data->getLoginHeaders();
        $table_headers = $request_data->getTableHeaders();
        $groups_post = $request_data->getGroupsPost();

        // This check if user entered test login and password
        $this->is_test = ($session['login'] == $this->test_login && $session['password'] == $this->test_password) ? true: false;

        // Login and password can't be empty
        if ($session['login'] != null  && $session['password'] != null) {

            // Case of a real user
            if (!$this->is_test) {
                $login_post = '{"LoginForm":{"id_city":null,"username":"'.$session['login'].'","password":"'.$session['password'].'"}}';

                $router = new Router($curl_connector, $login_headers, $login_post, $table_headers, $groups_post);

                // Connecting to the server with login and password
                try {
                    $router->login();
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
            }
            // Case of a test user
            else {
                $group_spec = $request->post('groupSpec') ? $request->post('groupSpec') : '';
                $group_id = $request->post('groupId') ? $request->post('groupId') : '';

                $router = new RouterTestData($file_reader, $group_spec, $group_id);
                //$router = new RouterTestDataDB($group_spec, $group_id);
            }

            // Polymorphic router usage
            if ($request->post('getGroupSpec')) {
                $group_spec = $router->groupSpec();
                return $group_spec;
            }

            if ($request->post('groupId') && $request->post('groupSpec')) {
                $table_mark = $router->tableMark();
                return $table_mark;
            }
        }

        return '';
    }
}