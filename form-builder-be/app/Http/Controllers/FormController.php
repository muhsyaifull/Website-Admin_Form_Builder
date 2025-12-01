<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Contracts\Service\Attribute\Required;

class FormController extends Controller
{
    public function store(Request $request)
    {
        $form = Form::create($request->only(['title', 'schema']));

        return response()->json($form, 201);
    }

    public function show($id)
    {
        return Form::findOrFail($id);
    }

    public function submit(Request $request, $Id)
    {
        $form = Form::findOrFail($Id);

        $rules = [];

        foreach ($form->schema as $field) {
            if (!empty($field['required'])) {
                $rules[$field['field']] = 'required';
            }
        }

        $validated = $request->validate($rules);

        $response = FormResponse::create([
            'form_id' => $Id,
            'data' => $validated,
        ]);

        return response()->json($response, 201);
    }
}