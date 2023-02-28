<?php

namespace App\Http\Controllers\DeleteAttach;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Orchid\Attachment\Models\Attachment;

class DeleteDocumentsController extends Controller
{
    public function delete(Request $request)
    {
        $attachID = $request->get('attachID');

        return Attachment::find($attachID)->delete();
    }
}
