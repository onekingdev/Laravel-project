<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App;
use App\File;

class FileController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Fetch files by Type or Id
     * @param string $type File type
     * @param integer $id File Id
     * @return object        Files list, JSON
     */
    public function index(string $type, $id = null)
    {
        $model = new File();

        if (!is_null($id)) {
            $response = $model::findOrFail($id);
        } else {
            $records_per_page = ($type == 'video') ? 6 : 15;

            $files = $model::where('type', $type)
                            ->where('user_id', Auth::id())
                            ->orderBy('id', 'desc')->paginate($records_per_page);

            $response = [
                'pagination' => [
                    'total' => $files->total(),
                    'per_page' => $files->perPage(),
                    'current_page' => $files->currentPage(),
                    'last_page' => $files->lastPage(),
                    'from' => $files->firstItem(),
                    'to' => $files->lastItem()
                ],
                'data' => $files
            ];
        }

        return response()->json($response);
    }


    /**
     * Upload new file and store it
     * @param  Request $request Request with form data: filename and file info
     * @return \Illuminate\Http\JsonResponse          True if success, otherwise - false
     */
    public function store(Request $request)
    {
        if($request->hasFile('files')){
            $files = $request->file('files');
            foreach ($files as $key => $item) {
                $file = new File();
                
                if($item->getClientOriginalExtension() == 'jpeg'){
                    $imgLocation = self::get_image_location($item);
                    $imgLat = $imgLocation['latitude'];
                    $imgLng = $imgLocation['longitude'];
                }else{
                    $imgLat = null;
                    $imgLng = null;
                }

                //latitude & longitude

                $filename = $item->getClientOriginalName();
                $original_ext = $item->getClientOriginalExtension();
                $type = $file->getType($original_ext);
                if (!empty($file->upload($type, $item, $request['filenames'][$key], $original_ext))) {
                    $file::create([
                        'name' => $request['filenames'][$key],
                        'type' => $type,
                        'extension' => $original_ext,
                        'address_latitude' => $imgLat,
                        'address_longitude' => $imgLng,
                        'user_id' => Auth::id()
                    ]);
                }else{
                    echo "error";
                }
            }
        }

        return response()->json(true);
    }

    /**
     * Edit specific file
     * @param  integer  $id      File Id
     * @param  Request $request  Request with form data: filename
     * @return \Illuminate\Http\JsonResponse           True if success, otherwise - false
     */
    public function edit($id, Request $request)
    {
        $file = File::where('id', $id)->where('user_id', Auth::id())->first();

        if ($file->name == $request['name']) {
            return response()->json(false);
        }

        $this->validate($request, [
            'name' => 'required|unique:files'
        ]);

        $old_filename = $file->getName($file->type, $file->name, $file->extension);
        $new_filename = $file->getName($request['type'], $request['name'], $request['extension']);

        if (Storage::disk('local')->exists($old_filename)) {
            if (Storage::disk('local')->move($old_filename, $new_filename)) {
                $file->name = $request['name'];
                return response()->json($file->save());
            }
        }

        return response()->json(false);
    }


    /**
     * Delete file from disk and database
     * @param  integer $id  File Id
     * @return \Illuminate\Http\JsonResponse      True if success, otherwise - false
     */
    public function destroy($id)
    {
        $file = File::findOrFail($id);

        if (Storage::disk('local')->exists($file->getName($file->type, $file->name, $file->extension))) {
            if (Storage::disk('local')->delete($file->getName($file->type, $file->name, $file->extension))) {
                return response()->json($file->delete());
            }
        }

        return response()->json(false);
    }

    public function gps2Num($coordPart){
        $parts = explode('/', $coordPart);
        if(count($parts) <= 0)
            return 0;
        if(count($parts) == 1)
            return $parts[0];
        return floatval($parts[0]) / floatval($parts[1]);
    }

    public function get_image_location($image = ''){

        $exif = exif_read_data($image, 0, true);

        if($exif && isset($exif['GPS'])){
            $GPSLatitudeRef = $exif['GPS']['GPSLatitudeRef'];
            $GPSLatitude    = $exif['GPS']['GPSLatitude'];
            $GPSLongitudeRef= $exif['GPS']['GPSLongitudeRef'];
            $GPSLongitude   = $exif['GPS']['GPSLongitude'];

            $lat_degrees = count($GPSLatitude) > 0 ? self::gps2Num($GPSLatitude[0]) : 0;
            $lat_minutes = count($GPSLatitude) > 1 ? self::gps2Num($GPSLatitude[1]) : 0;
            $lat_seconds = count($GPSLatitude) > 2 ? self::gps2Num($GPSLatitude[2]) : 0;

            $lon_degrees = count($GPSLongitude) > 0 ? self::gps2Num($GPSLongitude[0]) : 0;
            $lon_minutes = count($GPSLongitude) > 1 ? self::gps2Num($GPSLongitude[1]) : 0;
            $lon_seconds = count($GPSLongitude) > 2 ? self::gps2Num($GPSLongitude[2]) : 0;

            $lat_direction = ($GPSLatitudeRef == 'W' or $GPSLatitudeRef == 'S') ? -1 : 1;
            $lon_direction = ($GPSLongitudeRef == 'W' or $GPSLongitudeRef == 'S') ? -1 : 1;

            $latitude = $lat_direction * ($lat_degrees + ($lat_minutes / 60) + ($lat_seconds / (60*60)));
            $longitude = $lon_direction * ($lon_degrees + ($lon_minutes / 60) + ($lon_seconds / (60*60)));

            return array('latitude'=>$latitude, 'longitude'=>$longitude);
        }else{
            return false;
        }
    }

}
