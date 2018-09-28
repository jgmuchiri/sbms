<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use NumberFormatter;

class Tools extends Model
{
    /**
     * @param $file
     * @param $dir
     * @return bool|string
     */
    public static function uploadFile($file, $destination, $fileName = null)
    {
        if ($file == null) {
            return false;
        }
        if (!File::isDirectory($destination)) {
            File::makeDirectory($destination, 493, true);
        }

        $extension = $file->getClientOriginalExtension();
        if ($fileName == null)
            $fileName = $file->getClientOriginalName();
        else
            $fileName = $fileName . '.' . $extension;

        $file->move($destination, $fileName);
        return $fileName;
    }

    /**
     * @param $file
     * @param $destination
     * @param null $name
     * @return bool|string
     */
    public static function uploadImage($file, $destination, $name = null, $width = null, $height = null)
    {
        if ($file == null) {
            return false;
        }

        if ($file->isValid()) {
            $image = Image::make($file);

            if (!\File::isDirectory($destination)) {
                \File::makeDirectory($destination, 493, true);
            }

            $extension = $file->getClientOriginalExtension();
            $fileName = time() . rand(11111, 99999) . '.' . $extension;

            //resize
            if ($width !== null && $height !== null) {
                $image->resize($width, $height)->save($destination . $fileName);
            } else {
                $image->save($destination . $fileName);
            }

            return $fileName;
        }
        return false;
    }

    /**
     * Timezones list with GMT offset
     *
     * @return array
     * @link http://stackoverflow.com/a/9328760
     */
    public static function timezone()
    {
        $zones_array = array();
        $timestamp = time();
        foreach (timezone_identifiers_list() as $key => $zone) {
            date_default_timezone_set($zone);
            $zones_array[$key]['zone'] = $zone;
            $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
        }
        return $zones_array;
    }

    /**
     * convert first image in posted text to fit theme
     * @param $content
     * @return mixed
     */
    public static function formatFirstImageInText($content)
    {
        //format top image
        $imgCount = (substr_count($content, "<img")); //find occurence
        if ($imgCount > 0)//there is image
        {
            $pos = strpos(trim($content), '<img');
            //check if is in the beginning
            if ($pos == 0 | $pos == 3)//after <p> or is first
            {
                $text = preg_replace('/<img/i', '<img style="height:300px;width:100%"', $content, 1);
            } else {
                $text = $content;
            }
        } else {
            $text = $content;
        }

        return $text;
    }

    /**
     * @param $base64_string
     * @param $output_file
     * @return mixed
     */
    public static function base64_to_jpeg($base64_string, $output_file)
    {
        $ifp = fopen($output_file, "wb");

        $data = explode(',', $base64_string);

        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);

        return $output_file;
    }

    /**
     * extract first image from post for thumbnail
     * @param $content
     * @return int|mixed|string
     */
    public static function postThumb($content, $width = "100%", $height = "100px", $noImg = false)
    {
        $img = preg_match_all('/<img[^>]+>/i', $content, $result);
        if ($img > 0) {
            $img = preg_replace('/<img/i', '<img class="image-responsive" style="height:' . $height . ';width:' . $width . '"', $result[0][0], 1);
            return $img;
        }
        //return no image photo
        if ($noImg == false)
            return '<img class="image-responsive" style="height:' . $height . ';width:' . $width . '" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHoAAABjCAYAAABUgBS3AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4AcSATM0Rr0JlAAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAFPUlEQVR42u2dzWvyQBDGp6VmKwmBqAgJWihUvEQPfen/f+ypHsSLVCzUYCFgFhZD3fTge0rRNqax3SS7yTwXD0bMzo99MvuR2Yv9fr8HVOV1iSFA0KgK6SrLRYwxCMMQNpsNvL+/Y9QkUbPZhHa7Dbqug2maqddepD2jOefw/PwM6/Uaoyq5HMeBwWAAhJDzQDPGYDabwXa7xSgqIsMwwHXdxN6dCJoxBpPJBKIowugpJk3T4P7+/hvsyyS7ns1mCFlRRVEEs9kMOOfpoFerFdq14tput7BarU6D5pyD53kYqQrI87yjXn00vAqCINWyDcOAbreLUZREvu+fdN8oiiAIArBtO/s4On7I//v372T6jipe/X4fHh8fM+VTR9a92WxOXqjrOkKWTIQQ0HX95PeHPHEKtCZC0AgahaBRCBolp66K/kPOOXDOIQzDz0wes/mKgGaMge/7iQN8TdNA13WwLAv6/T5CVxE05xxWqxV4nndyUB9FEURRBJRS8DwPer0e3N3dIRlVQDPGYD6fA6U082+iKILlcgm73S51ER0lSTLGOYfJZHIW5EOt12uYTqdIR2bQ8fajv65nU0phsVggIVlBB0EgbI/ZcrkExhhSkg005xxeXl6E3uB8PkdKsoEOgkD47hRKKfZq2UCnLXP+Rb7vIymZQOfV83a7nXSBi2f4aj28Ei3Z3g7hnMN0OoXpdKoM7EuRja/DFuEYMqUUKKXKwBYGmhACmqblcpPNZlM6yIfJogqwhVp3o9HI5Savr6+lhKwSbKGgLcvK5SbL3mKcBlkV2JeigYi2b8MwSl3cyAJZBdhCQZumCb1eT+gN3t7elgb6HMiywxY+vOr3+2AYhrBHQfymgQqQZYYtHDQhBFzXFWLZ4/FYOciyws5lwsQ0TRiNRr/u2ZZlgeu6pVi2CMgyws5tZsy2bXBd9+xM3HEcGI/HP9bkkB2ybLBz3TNmmiY8PDzA29sbeJ4HYRgmzp5pmgadTgdubm5KAZwX5K+wx+NxaYllIbtAbdsG27aBcw5BEEAYhgAAny+ItVotZYZQqsIudF83IaS0LPqUiizKUybsWr+pERflKbKUR1nP7MygPz4+Kgm5jBW3MmBf1rknl7msWjTsI9B1KP8oUw21ImEfgZZl3bcOkIuGXRvrlrkaYhGwpbVukY1WoeRl3rCltO7FYiGs0SrVNc0TtnTWvVgsYLlcCmm0isVr84ItlXXHkEU0WuUKxXnAlsa6v0L+S6OrUIY6bnelrPsU5N/ArlKtcUqpsF5dunX/BPkc2PECBdYal8y6s0LOAruMBQplQcsMOQ02Hg0hqXX/FnISbIScTVdfrTvPXRYiIB/Cfnp6AgBAyOeClr0nfxU+jyW0btGQURJm3Qi5Blk3Qq6BdSPkGlg3Qq6BdSPkGlg3Qq6BdSPkGlg3Ywwh1zXrRtUg60bVIOtGoXWj0LpRaN0otG4UWjcKrRutGyNSUQnbHEgIgdFohBFNka7rn6fsZvmM4yoc9F8kY2kpGRUXzMv6idaNwqwbhVk3gkbrRutGVQ102rFDURThYaCSiTH2OQxLUrvdTh5exWWVT4GeTCbQ6XQwwgXpp/O+fN/P/IJhZtAxbFGHgKPylaZp0Gq1kq3bNE1wHAejVAF1Op2jWbVvw6vBYJDbGZOo4nrzYDBIH0cTQmA4HCJshSEPh8Nvc+SJEya2bSNshSEnrTlc7Pf7fVr6/vr6igmYAnIcJ/WUoVTQh8B93wdKaeWOXFBZjUYDLMuCbrf742pXJtAo9YWLGggaVSX9B3OPKDPN8n/wAAAAAElFTkSuQmCC"/>';
        else
            return "";
    }

    /**
     * @param $item
     * @return string
     */
    public static function settings($item)
    {
        $settings = DB::table('settings')->where('option', $item)->first();
        if (count($settings))
            return $settings->value;
        else
            return "";
    }

    /**
     * @param $perms
     * @return bool
     */
    static function allow($perms)
    {
        $thisUser = User::find(auth()->user()->id);

        if ($thisUser->can($perms)) {
            return true;
        }else{
            flash()->error('You are not authorized to ' . $perms);
            return false;
        }
    }

    /**
     * @param $number
     * @return bool|null|string
     */
    public static function NumberToText($number)
    {

        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' and ';
        $dictionary  = array(
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'fourty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . self::NumberToText(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . self::NumberToText($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = self::NumberToText($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= self::NumberToText($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= $fraction.'/100';
        }else{
            $string .=' ';
        }

        return $string;
    }
    /**
     * @param $bytes
     * @param int $precision
     * @return string
     */
    public static function formatBytes($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024) . ' kB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
