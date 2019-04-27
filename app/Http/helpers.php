<?php
/**
 * @package     ccms
 * @copyright   2017 A&M Digital Technologies
 * @author      John Muchiri
 * @link        https://amdtllc.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
use Illuminate\Support\Facades\Auth;

if (!function_exists('str_clean')) {
    function str_clean($string)
    {
        $string = str_replace(array('[\',\']'), '', $string);
        $string = preg_replace('/\[.*\]/U', '', $string);
        $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
        $string = htmlentities($string, ENT_COMPAT, 'utf-8');
        $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string);
        $string = preg_replace(array('/[^A-Za-z0-9-.]/i', '/[-]+/'), '-', $string);
        $string = strip_tags($string);
        return strtolower(trim($string, '-'));
    }
}

if (!function_exists('has_role')) {
    function has_role($role = '')
    {

        if (empty($role)) {
            return false;
        }

        if (is_array($role)) {
            if (in_array(auth()->user()->role, $role)) {
                return true;
            }
        }

        if ($role == auth()->user()->role) {
            return true;
        }

        return false;
    }
}
