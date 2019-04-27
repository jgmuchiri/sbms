<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,manager');
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * @return mixed
     */
    public function settings()
    {
        $envFile = '../.env';
        $fhandle = fopen($envFile, 'rw');
        $size = filesize($envFile);
        $envContent = '';
        if($size == 0) {
            flash()->error(__('Your file is empty'));
        } else {
            $envContent = fread($fhandle, $size);
            fclose($fhandle);
        }
        return view('admin.settings', compact('envContent'));
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function backupEnv(Request $request)
    {
        $envFile = '../.env';
        return response()->download($envFile, config('app.name').'-ENV-'.date('Y-m-d_H-i').'.txt');
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function updateEnv(Request $request)
    {
        $envFile = '../.env';
        $fhandle = fopen($envFile, 'w');
        fwrite($fhandle, $request->envContent);
        fclose($fhandle);
        flash()->success(__('Settings have been update. Please verify that your application is working properly'));
        return redirect()->back();
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function uploadLogo(Request $request)
    {
        $error = 1;

        if($request->logo !== NULL) {

            $file = Input::file('logo');

            $extension = $file->getClientOriginalExtension();

            if($extension == 'jpg'
                || $extension == 'JPG'
                || $extension == 'png'
                || $extension == 'PNG') {

                $file->move('img/', 'logo.'.strtolower($extension));

                $error = 0;
            }
        }

        ($error == 0)
            ? flash()->success(__('Logo uploaded updated!'))
            : flash()->error(__('Invalid image!'));

        return redirect()->back();
    }

    public function debug()
    {
        $dir = '../storage/logs/';
        $logs = [];
        foreach (glob($dir.'*.*') as $filename) {
            $logs[] = basename($filename, '.log');
        }

        if(isset($_GET['log']) && $_GET['log'] !== '') {

            $logFile = '../storage/logs/'.$_GET['log'].'.log';

            if(!is_file($logFile)) {

                flash()->error(__('Your log file is empty'));

                return redirect()->back();
            }

            $fhandle = fopen($logFile, 'rw');

            $size = filesize($logFile);

            $logContent = '';

            if($size == 0) {
                flash()->error(__('Your log file is empty'));
            } else {
                $logContent = fread($fhandle, $size);
                fclose($fhandle);
            }
        }

        return view('admin.debug-logs', compact('logs', 'logContent'));
    }

    /**
     * @return mixed
     */
    public function emptyDebugLog(Request $request)
    {
        if($request->has('log_date')) {

            $logFile = '../storage/logs/'.$request->log_date.'.log';

            if(!is_file($logFile)) flash()->error(__('Your log file is empty'));

            @unlink($logFile);
        }

        flash()->success(__('Debug log has been emptied'));

        return redirect('debug-log');
    }
}
