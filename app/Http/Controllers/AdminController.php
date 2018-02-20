<?php

namespace App\Http\Controllers;

use App\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin', ['only' => ['settings', 'backupEnv', 'updateEnv', 'uploadLogo']]);
        $this->middleware('permission:read-logs', ['only' => ['debug']]);
        $this->middleware('permission:delete-logs', ['only' => ['emptyDebug']]);
    }

    /**
     * @return mixed
     */
    function index()
    {
        $user = Auth::user();
        if ($user->hasRole('admin') || $user->hasRole('manager')) {
            return view('admin.dashboard');
        } else {
            return redirect('account');
        }

    }

    /**
     * @return mixed
     */
    function settings()
    {
        $envFile = "../.env";
        $fhandle = fopen($envFile, "rw");
        $size = filesize($envFile);
        $envContent = "";
        if ($size == 0) {
            flash()->error(__("Your file is empty"));
        } else {
            $envContent = fread($fhandle, $size);
            fclose($fhandle);
        }
        return view('admin.settings', compact('envContent'));


    }

    /**
     * @param Request $request
     * @return mixed
     */
    function backupEnv(Request $request)
    {
        $envFile = "../.env";
        return response()->download($envFile, config('app.name') . '-ENV-' . date('Y-m-d_H-i') . '.txt');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    function updateEnv(Request $request)
    {
        $envFile = "../.env";
        $fhandle = fopen($envFile, "w");
        fwrite($fhandle, $request->envContent);
        fclose($fhandle);
        flash()->success(__("Settings have been update. Please verify that your application is working properly"));
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function uploadLogo(Request $request)
    {
        if ($request->logo !== null) {
            $path = 'img/';
            $file = Input::file('logo');
            $extension = $file->getClientOriginalExtension();
            if($extension == "jpg" || $extension =="JPG" || $extension=="png" || $extension=="PNG"){
                $fileName = 'logo.' . strtolower($extension);
                $file->move($path, $fileName);
                flash()->success(__("Logo uploaded updated!"));
            }else{
                flash()->error(__("Invalid image!"));
            }
        }else{
            flash()->error(__("Invalid image!"));
        }

        return redirect()->back();
    }

    /**
     * @return View
     */
    function debug()
    {

        $dir = "../storage/logs/";
        $logs =array();
        foreach (glob($dir."*.*") as $filename) {
            $logs[]= basename($filename,'.log');
        }


        if(isset($_GET['log']) && $_GET['log'] !==""){
            $logFile = "../storage/logs/".$_GET['log'].'.log';
            if(!is_file($logFile)){
                flash()->error(__("Your log file is empty"));
                return redirect()->back();
            }

            $fhandle = fopen($logFile, "rw");
            $size = filesize($logFile);
            $logContent = "";
            if ($size == 0) {
                flash()->error(__('Your log file is empty'));
            } else {
                $logContent = fread($fhandle, $size);
                fclose($fhandle);
            }
        }


        return view('admin.debug-logs', compact('logs','logContent'));
    }

    /**
     * @return mixed
     */
    function emptyDebugLog(Request $request)
    {

       if($request->has('log_date')) {
           $logFile = "../storage/logs/" . $request->log_date . '.log';
           if(!is_file($logFile)){
               flash()->error(__("Your log file is empty"));
           }
           @unlink($logFile);
       }
        flash()->success(__("Debug log has been emptied"));
        return redirect('debug-log');
    }


}
