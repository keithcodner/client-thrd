<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeBasePage;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use League\CommonMark\Util\ArrayCollection;

class GigBiznessKnowledgebaseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        Paginator::useBootstrap();
    }

    public function index(Request $request)
    {
        $pages = collect();
        $sections = collect();
        $document_page = null;
        $document_sections = collect();
        $active_document_list = collect();

        $active_document_list = KnowledgeBasePage::where(function($q){
            $q->where('status', 'active')->orWhere('status','set');
        })->get();

        if(KnowledgeBasePage::where('status', 'active')->exists()){
            $pages = KnowledgeBasePage::where('status', 'active')->orderBy('page_name', 'ASC')->get();
        }

        if(KnowledgeBase::where('kb_status', 'active')->exists() && $pages->isNotEmpty()){
            $sections = KnowledgeBase::where('kb_status', 'active')->whereIn('kb_parent_id', $pages->pluck('id'))->get();
        }

        if(isset($request->document)){
            $document_page = KnowledgeBasePage::where('id', $request->document)->where(function($q){
                $q->where('status', 'active')->orWhere('status','set');
            })->first();
            
            if($document_page) {
                $document_sections = KnowledgeBase::where('kb_status', 'active')->where('kb_parent_id', $request->document)->orderBy('kb_order', 'ASC')->get();
            }
        }

        return Inertia::render('Admin/Support/KnowledgeBase/KnowledgeBase', [
            'pages' => $pages,
            'sections' => $sections,
            'document_pages' => $document_page,
            'document_sections' => $document_sections,
            'document_lists' => $active_document_list,
        ]);
    }

    public function searchDocuments(Request $request)
    {
        $search = KnowledgeBasePage::where('page_name', 'like', '%' .  $request->value1 . '%')->where(function($q){
            $q->where('status', 'active')->orWhere('status','set');
        })->get()->toJson();

        return $search;
    }

    public function addOrUpdatePage(Request $request)
    {
        $an_id = uniqid().'-'.uniqid().'-'.uniqid().'-'.uniqid().'-'.now()->timestamp;

        if($request->value1 == 'ADD_PAGE_CODE'){

            $link = str_replace(' ', '-', $request->value2);
            $add = KnowledgeBasePage::create([
                'page_an_id' => $an_id,
                'page_name' => $request->value2,
                'link_name' => $link,
                'page_creator_user_id' => Auth::user()->id,
                'description' => $request->value4,
                'status' => 'active',
                'type' => $request->value3,
            ]);

            return 'Page has been created.';

        }else if($request->value1 == 'EDIT_PAGE_CODE'){
            
            $link = str_replace(' ', '-', $request->value2);
            $update = KnowledgeBasePage::where('id', $request->value5)->update([
                'page_name' => $request->value2,
                'link_name' => $link,
                'description' => $request->value4,
                'type' => $request->value3,
                'status' => $request->value6,
            ]);

            return 'Page has been updated.';

        }else if($request->value1 == 'DELETE_PAGE_CODE'){
            
            $delete = KnowledgeBasePage::where('id', $request->value2)->update([
                'status' => 'inactive',
            ]);

            return 'Page has been deleted.';
        }
    }

    public function addOrUpdateSection(Request $request)
    {
        $an_id = uniqid().'-'.uniqid().'-'.uniqid().'-'.uniqid().'-'.now()->timestamp;

        if($request->value1 == 'ADD_SECTION_CODE'){

            $add = KnowledgeBase::create([
                'user_added_id' => Auth::user()->id,
                'kb_an_id' => $an_id,
                'kb_parent_id' => $request->value2,
                'kb_type1' => $request->value4,
                'kb_status' => 'active',
                'kb_summary' => $request->value5,
                'kb_description' => $request->value6,
                'kb_order' => $request->value3,
            ]);

            return 'Section has been created.';
        }else if($request->value1 == 'EDIT_SECTION_CODE'){

            $update = KnowledgeBase::where('id', $request->value2)->update([
                'kb_parent_id' => $request->value3,
                'kb_type1' => $request->value7,
                'kb_summary' => $request->value5,
                'kb_description' => $request->value6,
                'kb_order' => $request->value4,
                'kb_status' => $request->value8,
            ]);

            return 'section has been updated';
            
        }else if($request->value1 == 'DELETE_SECTION_CODE'){
            
            $delete = KnowledgeBase::where('id', $request->value2)->update([
                'kb_status' => 'inactive',
            ]);

            return 'Section has been deleted.';
        }
    }

    public function getPageInfo(Request $request)
    {
        if(KnowledgeBasePage::where('id', $request->value1)->exists()){
            $page = KnowledgeBasePage::where('id', $request->value1)->first();
            return $page;
        }else{
            return 'Could not find page record!';
        }
    }

    public function getSectionInfo(Request $request)
    {
        if(KnowledgeBase::where('id', $request->value1)->exists()){
            $section = KnowledgeBase::where('id', $request->value1)->first();
            return $section;
        }else{
            return 'Could not find section record!';
        }
    }

    public function getMaxSectionOrder(Request $request)
    {
        if(KnowledgeBase::where('kb_status', 'active')->where('kb_parent_id', $request->value1)->exists()){
            $order = KnowledgeBase::where('kb_status', 'active')->where('kb_parent_id', $request->value1)->max('kb_order');
            return $order ?: 0;
        }else{
            return 0;
        }
    }

    public function updateInactiveDocumentsDropDown(Request $request)
    {
        if($request->value1 == 'CHECKED_INACTIVE_PAGE'){
            $data = KnowledgeBasePage::where('status', 'inactive')->orderBy('page_name', 'ASC')->get()->toJson();
            return $data;
        }else if($request->value1 == 'UNCHECKED_INACTIVE_PAGE'){
            $data = KnowledgeBasePage::where('status', 'active')->orderBy('page_name', 'ASC')->get()->toJson();
            return $data;
        }else if($request->value1 == 'CHECKED_INACTIVE_SECTION'){
            $data = KnowledgeBase::where('kb_status', 'inactive')->get()->toJson();
            return $data;
        }else if($request->value1 == 'UNCHECKED_INACTIVE_SECTION'){
            $data = KnowledgeBase::where('kb_status', 'active')->get()->toJson();
            return $data;
        }
    }

    public function updateSetDocumentsDropDown(Request $request)
    {
        if($request->value1 == 'CHECKED_SET_PAGE'){
            $data = KnowledgeBasePage::where('status', 'set')->orderBy('page_name', 'ASC')->get()->toJson();
            return $data;
        }else if($request->value1 == 'UNCHECKED_SET_PAGE'){
            $data = KnowledgeBasePage::where('status', 'active')->orderBy('page_name', 'ASC')->get()->toJson();
            return $data;
        }else if($request->value1 == 'CHECKED_SET_SECTION'){
            $pages = KnowledgeBasePage::where('status', 'set')->orderBy('page_name', 'ASC')->get();
            $data = KnowledgeBase::where('kb_status', 'active')->whereIn('kb_parent_id', $pages->pluck('id'))->get()->toJson();
            return $data;
        }else if($request->value1 == 'UNCHECKED_SET_SECTION'){
            $pages = KnowledgeBasePage::where('status', 'active')->orderBy('page_name', 'ASC')->get();
            $data = KnowledgeBase::whereIn('kb_parent_id', $pages->pluck('id'))->get();
            return $data;
        }
    }

}
