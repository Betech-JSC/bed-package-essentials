<?php

namespace JamstackVietnam\Contact\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JamstackVietnam\Contact\Models\Contact;
use JamstackVietnam\Support\Models\File;

class ContactController extends Controller
{
    public $model = Contact::class;

    public function store(Request $request)
    {
        $data = $request->input('contact')['data'];
        $requestData = $request->all()['contact'];
        $requestData['type'] = $requestData['type'] ?? key(config('contact.types'));
        $rules = config('contact.types.' . $requestData['type'] . '.rules');
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }
        
        if (isset($requestData['data']['File CV'])) {
            $files = $requestData['data']['File CV'];
            $file = new File($request->input('path', '/'));

            $fileUploaded = $file->store($files);

            unset($requestData['data']['File CV']);

            $requestData['data']['File CV'] = [];

            if(isset($fileUploaded['successFiles'])) {
                foreach ($fileUploaded['successFiles'] as $item) {
                    $requestData['data']['File CV'][] = static_url($item);
                }
            }
        }

        $this->model::create($requestData);

        return redirect()->back()->withSuccess('success');
    }
}
