<?php

namespace Jamstackvietnam\Contact\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Jamstackvietnam\Support\Traits\ApiResponse;
use Jamstackvietnam\Support\Traits\HasApiCrudActions;
use Illuminate\Support\Facades\Validator;
use Jamstackvietnam\Contact\Models\Contact;

class ContactController extends Controller
{
    use HasApiCrudActions, ApiResponse;

    public $model = Contact::class;

    public function store(Request $request)
    {
        $errors = $this->validateRequest(__FUNCTION__);

        $data = $request->input('contact')['data'];
        $requestData = $request->all()['contact'];
        $requestData['type'] = $requestData['type'] ?? key(config('contact.types'));
        $rules = config('contact.types.' . $requestData['type'] . '.rules');
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $this->model::create($requestData);

        return redirect()->back()->withSuccess('success');
    }
}
