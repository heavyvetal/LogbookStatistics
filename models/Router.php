<?php

namespace app\models;

/**
 * RouterTestData is responsible for statistics delivery
 *
 * @package app\models
 */
class Router implements IRouter
{
    private $table_headers = '';
    private $groups_post = '';
    private $login_headers = '';
    private $login_post = '';

    public function __construct($obj, $login_headers, $login_post, $table_headers, $groups_post)
    {
        $this->browser = $obj;
        $this->table_headers = $table_headers;
        $this->groups_post = $groups_post;
        $this->login_headers = $login_headers;
        $this->login_post = $login_post;
    }

    /**
     * Connecting to the server with login and password
     *
     * @return string
     */
    public function login()
    {
        $this->browser->logIn('https://logbook.itstep.org/auth/login', $this->login_headers, $this->login_post);
    }

    /**
     * This method gets data about groups from the server via api
     *
     * @return json|string
     */
    public function groupSpec()
    {
        /**
         * Getting json_groups_spec
         * @var string
         */
        $json_groups_spec = $this->browser->getData('https://logbook.itstep.org/classwork/index', $this->table_headers, '');

        /**
         * Getting json_groups_list
         * @var string
         */
        $json_groups = $this->browser->getData('https://logbook.itstep.org/students/get-groups-list', $this->table_headers, $this->groups_post);

        return "$json_groups|||$json_groups_spec";
    }

    /**
     * This method gets the table of single group marks from the server via api
     *
     * @return json|string
     */
    public function tableMark()
    {
        /**
         * Getting json_students
         * @var string
         */
        $json_students = $this->browser->getData(
            'https://logbook.itstep.org/groups/get-students',
            $this->table_headers,
            '{"id_tgroups":"' . $_POST['groupId'] . '","spec":"00"}'
        );

        /**
         * Getting table marks in json
         * @var string
         */
        $json_table = $this->browser->getData(
            'https://logbook.itstep.org/groups/get-table',
            $this->table_headers,
            '{"id_tgroups":"' . $_POST['groupId'] . '","id_spec":"' . $_POST['groupSpec'] . '","limit":0,"offset":0}'
        );

        return "$json_students|||$json_table";
    }
}