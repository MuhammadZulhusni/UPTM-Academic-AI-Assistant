<?php

namespace App\Http\Controllers\Backend\Admin;

use Illuminate\Http\Request;
use App\Models\GeneratedContent;
use App\Http\Controllers\Controller;

class DocumentController extends Controller
{
    public function AdminDocument(){
        // $id = Auth::user()->id;
        $document = GeneratedContent::orderBy('id','desc')->get();
        return view('admin.backend.document.all_document',compact('document'));
    }
}
