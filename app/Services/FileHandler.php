<?php

namespace App\Services;

use Storage;

class FileHandler
{
    public static function file_store($attachments)
    {
        foreach ($attachments as $attachment) {
            $extension = $attachment->getClientOriginalExtension();
            $name = 'Task';
            $name = $name . '-' . rand(0, 999999) . '-' . date('ymdhis') . '-image.' . $extension;
            $attachment->move(storage_path('app/public/tasks/'), $name);
            $data[] = $name;
        }

        return $data;
    }

    public static function file_delete($file)
    {
        if (Storage::exists(('public/tasks/' . $file->name))) {
            Storage::delete('public/tasks/' . $file->name);

            return true;
        } else {
            return false;
        }
    }
}
