<?php

namespace App\Http\Controllers;

use App\Models\Contacts\ContactGroups;
use App\Models\Contacts\Contacts;
use App\Tools;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContactsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:read contacts',['only'=>['index','ajaxViewGroups']]);
        $this->middleware('permission:create contacts', ['only' => ['store','createGroup','updateGroup']]);
        $this->middleware('permission:Update contacts', ['only' => ['edit','update','assignContactToGroup']]);
        $this->middleware('permission:delete contacts', ['only' => ['destroy','destroyGroup']]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index(){
        $contacts = Contacts::get();
        $groups = DB::table('contact_groups');
        if(isset($_GET['s']) && $_GET['s'] !==""){
            $s=$_GET['s'];
            $contacts =Contacts::where('first_name','LIKE','%'.$s.'%')
                ->orWhere('last_name','LIKE','%'.$s.'%')
                ->orWhere('cell','LIKE','%'.$s.'%')
                ->orWhere('email','LIKE','%'.$s.'%')
                ->orWhere('phone','LIKE','%'.$s.'%')
                ->orWhere('company','LIKE','%'.$s.'%')
                ->orWhere('address','LIKE','%'.$s.'%')
                ->orWhere('job_title','LIKE','%'.$s.'%')
                ->get();
        }

        return view('contacts.index',compact('contacts','groups'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function viewByGroup($id){
        $group = ContactGroups::findOrFail($id);


        if(isset($_GET['s']) && $_GET['s'] !==""){
            $s=$_GET['s'];
            $contacts =$group->contacts()->where('first_name','LIKE','%'.$s.'%')
                ->orWhere('last_name','LIKE','%'.$s.'%')
                ->orWhere('cell','LIKE','%'.$s.'%')
                ->orWhere('email','LIKE','%'.$s.'%')
                ->orWhere('phone','LIKE','%'.$s.'%')
                ->orWhere('company','LIKE','%'.$s.'%')
                ->orWhere('address','LIKE','%'.$s.'%')
                ->orWhere('job_title','LIKE','%'.$s.'%')
                ->get();
        }else{
            $contacts = $group->contacts;
        }

        $groups = DB::table('contact_groups');
        return view('contacts.index',compact('contacts','groups'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function store(Request $request){
        $rules = [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email'=>'required|email|unique:contacts'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = Input::all();

        $contact = new Contacts();
        if($request->hasFile('photo'))
        {
            Storage::makeDirectory('public/contacts');
            $photo= Storage::putFile('public/contacts',$request->file('photo'),'public');
            $photo = str_replace('public/','',$photo);
            $data['photo'] = $photo;
        }

        $contact = $contact->create($data);

        if($request->has('group_id')){
            if(is_array($request->group_id)){
                foreach($request->group_id as $group){
                    self::assignContactToGroup($contact->id,$group);
                }
            }else{
                self::assignContactToGroup($contact->id,$request->group_id);
            }
        }
        flash()->success(__("Contact added"));
        return redirect()->back();
    }

    /**
     * @param $contactID
     * @param $groupID
     */
    function assignContactToGroup($contactID,$groupID){
        $data = array(
            'contact_id'=>$contactID,
            'group_id'=>$groupID
        );
        DB::table('contact_group')->insert($data);
    }


    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function edit($id){
        if(request()->ajax()) {
            $contact = Contacts::findOrFail($id);
            $groups = DB::table('contact_groups');
            $cg = DB::table('contact_group')->where('contact_id', $id)->get();
            $cGroups = array();
            foreach ($cg as $c) {
                $cGroups[] = $c->group_id;
            }
            return view('contacts.edit-contact', compact('contact', 'groups', 'cGroups'));
        }
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function update(Request $request,$id){
        $rules = [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email'=>'unique:contacts,email,' . $id,
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = Input::all();

        $contact = Contacts::findOrFail($id);
        if($request->hasFile('photo'))
        {
            Storage::makeDirectory('public/contacts');
            $photo= Storage::putFile('public/contacts',$request->file('photo'),'public');
            $photo = str_replace('public/','',$photo);
            if($photo !==false){
                Storage::delete($contact->photo);
            }
            $data['photo'] = $photo;
        }

        if($request->has('group_id')){
            DB::table('contact_group')->where('contact_id',$id)->delete();
            if(is_array($request->group_id)){
                foreach($request->group_id as $group){
                    self::assignContactToGroup($contact->id,$group);
                }
            }else{
                self::assignContactToGroup($contact->id,$request->group_id);
            }
        }

        $contact->update($data);
        flash()->success(__("Contact updated"));
        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function destroy($id){
        $contact= Contacts::findOrFail($id);
        if($contact->photo !==null){
            @unlink('uploads/contacts/'.$contact->photo);
        }

        DB::table('contact_group')->where('contact_id',$id)->delete();

        $contact->delete();
        flash()->success(__("Contact deleted"));
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function createGroup(Request $request){
        $rules = [
            'group_name' => 'required|max:50',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = array(
            'group_name'=>$request->group_name,
            'desc'=>$request->desc
        );
        DB::table('contact_groups')->insert($data);
        flash()->success(__("Contacts group created"));
        return redirect()->back();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function ajaxViewGroups(){
        $cGroups = DB::table('contact_groups')->get();
        if(request()->ajax())
            return view('contacts.view-groups',compact('cGroups'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function editGroup($id){
        $group = DB::table('contact_groups')->whereId($id)->first();
        if(request()->ajax())
            return view('contacts.edit_group',compact('group'));
    }
    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function updateGroup(Request $request,$id){
        $rules = [
            'group_name' => 'required|max:50',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = array(
            'group_name'=>$request->group_name,
            'desc'=>$request->desc
        );
        DB::table('contact_groups')->whereId($id)->update($data);
        flash()->success(__("Contacts group updated"));
        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function destroyGroup($id){
        DB::table('contact_groups')->whereId($id)->delete();
        flash()->success(__("Contact group deleted"));
        return redirect()->back();
    }
}
