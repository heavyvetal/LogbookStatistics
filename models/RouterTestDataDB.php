<?php

namespace app\models;

use Yii;
use yii\db\Command;

/**
 * RouterTestDataDB is responsible for test statistics delivery from datebase
 *
 * @package app\models
 */
class RouterTestDataDB implements IRouter
{
    /**
     * RouterTestDataDB constructor.
     *
     * @param integer $id_spec
     * @param integer $group_id
     */
    public function __construct($id_spec, $group_id)
    {
        $this->id_spec = $id_spec;
        $this->group_id = $group_id;
    }

    /**
     * This method gets data about groups from the local storage
     *
     * @return json|string
     */
    public function groupSpec()
    {
        $res = Yii::$app->db->createCommand('SELECT * FROM groups')->queryAll()[0]['spec'];
        return $res;
    }

    /**
     * This method gets the table of single group marks from the local storage
     *
     * @return json|string
     */
    public function tableMark()
    {
        $group_id = '2320'; // Group В2811
        $spec_mark = array(6 => 'itstart.txt', 48 => '3d.txt', 230 => 'python.txt');

        if ($this->group_id == $group_id) {
            if ($this->id_spec == '6') $mark_source = 'itstart';
            if ($this->id_spec == '48') $mark_source = '3d';
            if ($this->id_spec == '230') $mark_source = 'python';

            $json_students = Yii::$app->db->createCommand("SELECT * FROM students WHERE `group_id`='В2811'")->queryAll()[0]['studs'];
            $json_table = Yii::$app->db->createCommand("SELECT * FROM disciplines WHERE `group_id`='В2811' AND `name`='".$mark_source."'")->queryAll()[0]['marks'];
        }

        return "$json_students|||$json_table";
    }
}