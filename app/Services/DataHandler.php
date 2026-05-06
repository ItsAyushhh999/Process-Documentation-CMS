<?php

namespace App\Services;

class DataHandler
{
    public static function returnDataArray($collection, $val)
    {
        foreach ($collection as $data) {
            $value[] = $data->$val; //$val may be 'id','name' etc. anything you want to get
        }

        return $value;
    }

    public static function returIdRelation($collection, $relation)
    {
        foreach ($collection->$relation as $data) {
            $value[] = $data->id;
        }

        return $value;
    }
}
