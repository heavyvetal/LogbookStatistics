<?php

namespace app\models;

use Yii;

/**
 * FileReader is resposible for an acquisition test data from the temporal local storage
 * In the future test data will store in database
 *
 * @package app\models
 */
class FileReader implements IReader
{
    /**
     * This method opens and reads a text file
     *
     * @param string
     * @return false|string
     */
    public function read($filename) {
        $file = new \SplFileObject(Yii::getAlias('@TEST_DATA_DIR').'/'.$filename, 'r');
        $res = $file->fread($file->getSize());
        $file = null;
        return $res;
    }
}