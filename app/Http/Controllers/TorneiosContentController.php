<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TorneiosContent;

class TorneiosContentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('toneios_content.admin.index');
    }

    public function create(Request $request)
    {
        $data = $request->torneios_info;
        $_Torneios = Torneios::create($data);
        return response()->json(compact('data'), 200);
    }

    public function getTorneiosList(Request $request) {
        // return $request->input("type");
        $user = Auth::user();
        $page_size = $request->page_size;
        if(empty($page_size)) {
            $page_size = 3;
        }
        $page_num = $request->page_num;
        if(empty($page_num)) {
            $page_num = 1;
        }
        $total_count = 0;
        $data = [];

        $type = $request->input('type');
        $where = [];

        // return $type;
        if($type=="team") {
            $where['is_team'] = 1;
        }else{
            $where['is_team'] = 0;
        }
        
        if($user->isAdmin()) {
            $total_count = Torneios::where($where)->count();
            $max_page = ceil($total_count / $page_size);
            if($max_page < $page_num) $page_num = $max_page;
            if($page_num > 0) {
                $data = Torneios::where($where)->skip(($page_num - 1) * $page_size)->latest()->take($page_size)->get();
            }
        } else {
            $total_count = Torneios::where($where)->count();
            $max_page = ceil($total_count / $page_size);
            if($max_page < $page_num) $page_num = $max_page;
            if($page_num > 0) {
                $data = Torneios::where($where)->latest()->skip(($page_num - 1) * $page_size)->take($page_size)->get();
            }
        }
        
        return response()->json(compact('page_size', 'page_num', 'total_count', 'data', 'max_page'), 200);
    }
}
