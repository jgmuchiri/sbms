<?php

namespace App\Http\Controllers;

use App\Models\Billing\Expenses;
use App\Models\Log;
use App\Models\Projects\ProjectFiles;
use App\Models\Projects\ProjectMembers;
use App\Models\Projects\ProjectMessages;
use App\Models\Projects\ProjectMilestones;
use App\Models\Projects\Projects;
use App\Models\Projects\ProjectTasks;
use App\Tools;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProjectsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,manager,staff');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function index()
    {
        if (isset($_GET['status'])) {
            $status = $_GET['status'];
            if ($status == 'all') {
                $projects = Projects::get();
            } elseif ($status == 'behind') {
                $projects = Projects::where('p_status', '!=', 'completed')
                    ->where('p_status', '!=', 'cancelled')
                    ->where('p_end', '<', date('Y-m-d'))->get();
            } else {
                $projects = Projects::where('p_status', $status)->get();
            }
        } else {
            $projects = Projects::where('p_status', 'due')->get();
        }

        return view('projects.index', compact('projects'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function view($id)
    {
        $project = Projects::findOrFail($id);
        $milestones = ProjectMilestones::whereProjectId($id)->get();
        $files = ProjectFiles::whereProjectId($id)->get();
        $messages = ProjectMessages::whereProjectId($id)->get();
        $members = ProjectMembers::whereProjectId($id)->get();
        return view('projects.view', compact('project', 'milestones', 'files', 'messages', 'members'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function createProject(Request $request)
    {
        $rules = [
            'title' => 'required',
            'client' => 'required',
            'p_start' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $p = new Projects();
        $p->title = $request->title;
        $p->details = $request->details;
        $p->client = $request->client;
        $p->p_start = $request->p_start;
        $p->p_end = $request->p_end;
        $p->p_status = $request->p_status;
        $p->created_by = auth()->user()->id;
        $p->created_at = date('Y-m-d H:i:s');
        $p->save();
        flash()->success(__("Project has been created"));
        return redirect()->back();

    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function editProject($id)
    {
        $project = Projects::find($id);
        return view('projects.edit-project', compact('project'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function updateProject(Request $request, $id)
    {
        $rules = [
            'title' => 'required',
            'client' => 'required',
            'p_start' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $p = Projects::findOrFail($id);
        $p->title = $request->title;
        $p->details = $request->details;
        $p->client = $request->client;
        $p->p_start = $request->p_start;
        $p->p_end = $request->p_end;
        $p->p_status = $request->p_status;
        $p->created_at = date('Y-m-d H:i:s');
        $p->save();
        flash()->success(__("Project has been updated"));
        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function deleteProject($id)
    {
        $p = Projects::findOrFail($id);
        foreach (ProjectFiles::whereProjectId($id)->get() as $pf) {
            @unlink('uploads/projects/' . $pf->path);
        }
        $p->delete();
        flash()->success(__("Project has been deleted"));
        return redirect('projects');
    }

    /**
     * @param $id
     * @param null $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function milestones($id, $mid = null)
    {
        $project = Projects::findOrFail($id);
        if ($mid !== null) {
            $milestones = ProjectMilestones::whereId($mid)->orderBy('m_end', 'DESC')->get();
        } else {
            $milestones = ProjectMilestones::whereProjectId($id)->orderBy('m_end', 'DESC')->get();
        }
        return view('projects.milestones', compact('project', 'milestones'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function createMilestone(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
            'm_start' => 'required',
            'm_end' => 'required',
            'm_status' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $m = new ProjectMilestones();
        $m->project_id = $id;
        $m->name = $request->name;
        $m->m_start = $request->m_start;
        $m->m_end = $request->m_end;
        $m->created_at = date('Y-m-d H:i:s');
        $m->created_by = auth()->user()->id;
        $m->m_status = $request->m_status;
        $m->save();

        flash()->success(__("Milestone has been created"));
        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function editMilestone($id)
    {
        $mms = ProjectMilestones::find($id);
        $project = Projects::find($mms->project_id);
        return view('projects.create-milestone', compact('mms', 'project'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function updateMilestone(Request $request, $id)
    {
        $rules = [
            'name' => 'required',
            'm_start' => 'required',
            'm_end' => 'required',
            'm_status' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $m = ProjectMilestones::find($id);
        $m->name = $request->name;
        $m->m_start = $request->m_start;
        $m->m_end = $request->m_end;
        $m->m_status = $request->m_status;
        $m->save();
        flash()->success(__("Milestone has been updated"));
        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function deleteMilestone($id)
    {
        $ms = ProjectMilestones::findOrFail($id);
        $ms->delete();
        flash()->success(__("Milestone has been deleted"));

        return redirect()->back();
    }

    /**
     * @param $id
     * @param null $mid
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function tasks($id, $mid = null)
    {

        $project = Projects::findOrFail($id);
        $milestone = array();
        if ($mid == null) {
            $tasks = ProjectTasks::whereProjectId($id)->orderBy('created_at', 'DESC')->get();
        } else {
            $tasks = ProjectTasks::whereMilestoneId($mid)->orderBy('created_at', 'DESC')->get();
            $milestone = ProjectMilestones::findOrFail($mid);
        }
        return view('projects.tasks', compact('project', 'tasks', 'milestone'));
    }

    function createTask(Request $request)
    {

        $rules = [
            'task_name' => 'required|max:50',
            't_start' => 'required',
            't_end' => 'required',
            'milestone_id' => 'required',
            'est_cost' => 'required',
            'actual_cost' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $t = new ProjectTasks();
        $t->task_name = $request->task_name;
        $t->project_id = $request->project_id;
        if ($request->has('milestone_id'))
            $t->milestone_id = $request->milestone_id;
        $t->desc = $request->desc;
        if ($request->has('assigned_to'))
            $t->assigned_to = $request->assigned_to;
        $t->t_start = $request->t_start;
        $t->t_end = $request->t_end;
        $t->t_status = $request->t_status;
        $t->created_at = date('Y-m-d H:i:s');

        if ($request->has('est_hours'))
            $t->est_hours = $request->est_hours;
        if ($request->has('est_cost'))
            $t->est_cost = $request->est_cost;
        if ($request->has('actual_hours'))
            $t->actual_hours = $request->actual_hours;
        if ($request->has('actual_cost'))
            $t->actual_cost = $request->actual_cost;

        $t->save();
        flash()->success(__("Task has been created"));

        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function editTask($id)
    {
        $myTask = ProjectTasks::find($id);
        $project = $myTask->project;
        $milestone = $myTask->milestone;
        return view('projects.create-task', compact('myTask', 'project', 'milestone'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function updateTaskStatus(Request $request)
    {
        $t = ProjectTasks::find($request->task_id);
        $t->t_status = $request->t_status;
        $t->save();
        flash()->success(__("Task has been updated"));
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function updateTask(Request $request)
    {
        $id = $request->task_id;
        $rules = [
            'task_name' => 'required|max:50',
            't_start' => 'required',
            't_end' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $t = ProjectTasks::findOrFail($id);
        $t->task_name = $request->task_name;
        $t->desc = $request->desc;
        $t->assigned_to = $request->assigned_to;
        $t->t_start = $request->t_start;
        $t->t_end = $request->t_end;
        $t->t_status = $request->t_status;
        $t->updated_at = date('Y-m-d H:i:s');

        if ($request->has('milestone_id'))
            $t->milestone_id = $request->milestone_id;
        if ($request->has('est_hours'))
            $t->est_hours = $request->est_hours;
        if ($request->has('est_cost'))
            $t->est_cost = $request->est_cost;
        if ($request->has('actual_hours'))
            $t->actual_hours = $request->actual_hours;
        if ($request->has('actual_cost'))
            $t->actual_cost = $request->actual_cost;

        $t->save();
        flash()->success(__("Task has been updated"));
        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    function payTask($id)
    {
        $task = ProjectTasks::findOrFail($id);

        if ($task->actual_cost == null) {
            flash()->error(__("Please specify the amount for the task"));
            return redirect()->back();
        }

        $exp = new Expenses();
        $exp->user_id = auth()->user()->id;
        $exp->name = $task->task_name;
        $exp->task_id = $id;
        $exp->amount = $task->actual_cost;
        $exp->category = 1;
        $exp->notes = $task->desc;
        $exp->client = $task->assigned_to;
        $exp->created_at = date('Y-m-d H:i:s');
        $exp->save();

        $task->t_status = 'completed';
        $task->save();

        flash()->success(__("Expense added for task payment"));
        return redirect('expenses/' . $exp->id . '/edit');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function deleteTask($id)
    {
        $task = ProjectTasks::findOrFail($id);
        $task->delete();
        flash()->success(__("Task has been deleted"));
        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function messages($id)
    {
        $project = Projects::findOrFail($id);
        $messages = ProjectMessages::whereProjectId($id)->whereNull('parent_id')->orderBy('created_at', 'DESC')->simplePaginate(15);
        return view('projects.messages', compact('project', 'messages'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function createMessage(Request $request)
    {
        $rules = [
            'message' => 'required',
            'project_id' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $msg = new ProjectMessages();
        $msg->user_id = auth()->user()->id;
        $msg->message = $request->message;
        $msg->created_at = date('Y-m-d H:i:s');
        $msg->project_id = $request->project_id;
        $msg->save();
        flash()->success(__("Message has been posted"));
        return redirect()->back();
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function replyMessage(Request $request, $id)
    {
        $rules = [
            'message' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $msg = new ProjectMessages();
        $msg->parent_id = $id;
        $msg->user_id = auth()->user()->id;
        $msg->message = $request->message;
        $msg->created_at = date('Y-m-d H:i:s');
        $msg->project_id = $request->project_id;
        $msg->save();
        flash()->success(__("Message has been posted"));
        return redirect()->back();
    }


    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    function deleteMessage($id)
    {
        $msg = ProjectMessages::findOrFail($id);
        $msg->delete();
        flash()->success(__("Message has been deleted"));
        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function files($id)
    {
        $project = Projects::find($id);
        $files = ProjectFiles::whereProjectId($id)->orderBy('created_at', 'DESC')->simplePaginate(20);
        return view('projects.files', compact('project', 'files'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    function uploadFile(Request $request)
    {
        $rules = [
            'filename' => 'required',
            'file' => 'required|mimes:jpeg,jpg,png,xsl,xslx,doc,docx,pdf,zip,gz'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $file = new ProjectFiles();
        $file->filename = $request->filename;
        $file->project_id = $request->project_id;
        $file->desc = $request->desc;

        if(!is_dir('storage/project-files')){
            Storage::makeDirectory('public/project-files');
        }
        $pFile = Storage::putFile('public/project-files', $request->file('file'), 'public');
        $file->path = str_replace('public/', '', $pFile);

        $file->size = $request->file->getClientSize();
        $file->user_id = auth()->user()->id;
        $file->save();

        flash()->success(__("File has been uploaded"));
        return redirect()->back();
    }

    /**
     * @return mixed
     */
    function downloadFile()
    {
        $id = $_GET['dl'];
        $file = ProjectFiles::wherePath($id)->first();
        $path = 'storage/' . $file->path;
        if (!file_exists($path)) {
            flash()->error(__('File not found'));
            return redirect()->back();
        }
        Log::add('Downloaded project file ID:' . $id . ' for project ID' . $file->project_id, 'update', 'general');
        return Response::download($path, $file->filename . '.' . pathinfo($file->path, PATHINFO_EXTENSION));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    function deleteFile()
    {
        $id = $_GET['file'];
        $file = ProjectFiles::wherePath($id)->first();
        @unlink('storage/' . $file->path);
        $file->delete();
        flash()->success(__("File has been deleted"));
        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function members($id)
    {
        $members = ProjectMembers::whereProjectId($id)->get();
        $project = Projects::find($id);
        return view('projects.members', compact('project', 'members'));
    }
}
