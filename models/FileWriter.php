<?php

namespace app\models;

class FileWriter
{
    public static function simpleWrite($filename, $data)
    {
        $file = new \SplFileObject($filename,"a");
        $file->fwrite("$data");
        $file = null;
    }
}