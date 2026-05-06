<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Services\FileHandler;
use Illuminate\Support\Facades\Auth;
use Session;

class AttachmentController extends Controller
{
    public function index($id)
    {
        $attachments = Attachment::with(['task'])->where('task_id', $id)->get();

        return view('attachment.index', ['attachments'=>$attachments]);
    }

    public static function store($attachments, $task_id, $flag = '0', $commentId = 0) //flag:0 task attachment , 1: comment attachment, 2: reply attachment
    {
        $userId = Auth::id();

        foreach ($attachments as $attachment) {
            Attachment::create([
                'task_id'   => $task_id,
                'name'      => $attachment,
                'createdBy' => $userId,
                'updatedBy' => $userId,
                'flag'      => $flag,
                'commentId' => $commentId,
            ]);
        }
    }

    public function destroy(Attachment $attachment)
    {
        $delete = FileHandler::file_delete($attachment);
        if ($delete) {
            $attachment->delete();
            Session::flash('status', 'Attachment Deleted Successfully.');

            return redirect()->back();
        } else {
            Session::flash('status', 'Delete Unsuccessfull.');

            return redirect()->back();
        }

    }
}
