<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactsGroup;
use App\Http\Requests\ContactsRequest;
use App\Models\Contacts\ContactGroups;
use App\Models\Contacts\Contacts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContactsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index()
    {

        $groups = ContactGroups::get();

        if(isset($_GET['s']) && !empty($_GET['s'])) {
            $s = $_GET['s'];
            $contacts = Contacts::search($s);
        } else {
            $contacts = Contacts::get();
        }

        return view('contacts.index', compact('contacts', 'groups'));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function viewByGroup($id)
    {
        $group = ContactGroups::findOrFail($id);

        if(isset($_GET['s']) && $_GET['s'] !== "") {
            $s = $_GET['s'];
            $contacts = $group->contacts()->where('first_name', 'LIKE', '%'.$s.'%')
                ->orWhere('last_name', 'LIKE', '%'.$s.'%')
                ->orWhere('cell', 'LIKE', '%'.$s.'%')
                ->orWhere('email', 'LIKE', '%'.$s.'%')
                ->orWhere('phone', 'LIKE', '%'.$s.'%')
                ->orWhere('company', 'LIKE', '%'.$s.'%')
                ->orWhere('address', 'LIKE', '%'.$s.'%')
                ->orWhere('job_title', 'LIKE', '%'.$s.'%')
                ->get();
        } else {
            $contacts = $group->contacts;
        }

        $groups = DB::table('contact_groups');
        return view('contacts.index', compact('contacts', 'groups'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    function store(Request $request)
    {
        $rules = [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'required|email|unique:contacts',
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = Input::all();

        $contact = new Contacts();
        if($request->hasFile('photo')) {
            Storage::makeDirectory('public/contacts');
            $photo = Storage::putFile('public/contacts', $request->file('photo'), 'public');
            $photo = str_replace('public/', '', $photo);
            $data['photo'] = $photo;
        }

        $contact = $contact->create($data);

        if($request->has('group_id')) {
            if(is_array($request->group_id)) {
                foreach ($request->group_id as $group) {
                    self::assignContactToGroup($contact->id, $group);
                }
            } else {
                self::assignContactToGroup($contact->id, $request->group_id);
            }
        }
        flash()->success(__("Contact added"));
        return redirect()->back();
    }

    /**
     * @param $contactID
     * @param $groupID
     */
    function assignContactToGroup($contactID, $groupID)
    {
        $data = [
            'contact_id' => $contactID,
            'group_id' => $groupID,
        ];
        DB::table('contact_group')->insert($data);
    }


    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function edit($id)
    {
        if(request()->ajax()) {
            $contact = Contacts::findOrFail($id);
            $groups = DB::table('contact_groups');
            $cg = DB::table('contact_group')->where('contact_id', $id)->get();
            $cGroups = [];
            foreach ($cg as $c) {
                $cGroups[] = $c->group_id;
            }
            return view('contacts.edit-contact', compact('contact', 'groups', 'cGroups'));
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    function update(ContactsRequest $request, $id)
    {

        $data = Input::all();

        $contact = Contacts::findOrFail($id);

        if($request->hasFile('photo')) {
            Storage::makeDirectory('public/contacts');
            $photo = Storage::putFile('public/contacts', $request->file('photo'), 'public');
            $photo = str_replace('public/', '', $photo);
            if($photo !== FALSE) {
                Storage::delete($contact->photo);
            }
            $data['photo'] = $photo;
        }

        if($request->has('group_id')) {

            DB::table('contact_group')->where('contact_id', $id)->delete();
            if(is_array($request->group_id)) {
                foreach ($request->group_id as $group) {
                    self::assignContactToGroup($contact->id, $group);
                }
            } else {
                self::assignContactToGroup($contact->id, $request->group_id);
            }
        }

        $contact->update($data);

        flash()->success(__("Contact updated"));

        return redirect()->back();
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    function destroy($id)
    {
        $contact = Contacts::findOrFail($id);

        //delete the contact photo if there
        if($contact->photo !== NULL) {
            @unlink('uploads/contacts/'.$contact->photo);
        }

        $contact = ContactGroups::findOrFail($id);

        $contact->delete();

        flash()->success(__("Contact deleted"));

        return redirect()->back();
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    function createGroup(ContactGroups $request)
    {

        ContactGroups::create($request->all());

        flash()->success(__("Contacts group created"));

        return redirect()->back();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function ajaxViewGroups()
    {
        $cGroups = DB::table('contact_groups')->get();
        if(request()->ajax())
            return view('contacts.view-groups', compact('cGroups'));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function editGroup($id)
    {
        $group = DB::table('contact_groups')->whereId($id)->first();
        if(request()->ajax())
            return view('contacts.edit_group', compact('group'));
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    function updateGroup(ContactGroups $request, $id)
    {

        $contactGroup = ContactGroups::findOrFail($id);

        $contactGroup->fill($request->all());

        flash()->success(__("Contacts group updated"));

        return redirect()->back();
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    function destroyGroup(Contacts $contacts, $id)
    {

        $contacts->deleteContact($id);

        flash()->success(__("Contact group deleted"));

        return redirect()->back();
    }
}
