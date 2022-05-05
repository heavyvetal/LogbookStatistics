<?php

namespace app\models;

/**
 * RouterTestData is responsible for test statistics delivery
 *
 * @package app\models
 */
class RouterTestData implements IRouter
{
    /**
     * RouterTestData constructor.
     *
     * @param object $obj
     * @param integer $id_spec
     * @param integer $group_id
     */
    public function __construct($obj, $id_spec, $group_id)
    {
        $this->test_data_reader = $obj;
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
        return $this->test_data_reader->read('group_spec.txt');
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
        $stud_source = 'students.txt';

        if ($this->group_id == $group_id) {
            $mark_source = $spec_mark[$this->id_spec];
            $json_students = $this->test_data_reader->read($stud_source);
            $json_table = $this->test_data_reader->read($mark_source);
        }

        return "$json_students|||$json_table";
    }
}