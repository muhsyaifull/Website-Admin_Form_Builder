<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormResponse;
use Illuminate\Http\Request;

class FormController extends Controller
{
    /**
     * Get all forms
     */
    public function index()
    {
        $forms = Form::orderBy('created_at', 'desc')->get();
        return response()->json($forms);
    }

    /**
     * Store a new form
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'schema' => 'required|array',
        ]);

        $form = Form::create($validated);

        return response()->json($form, 201);
    }

    /**
     * Get a single form
     */
    public function show($id)
    {
        $form = Form::findOrFail($id);
        return response()->json($form);
    }

    /**
     * Update a form
     */
    public function update(Request $request, $id)
    {
        $form = Form::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'schema' => 'required|array',
        ]);

        $form->update($validated);

        return response()->json($form);
    }

    /**
     * Delete a form
     */
    public function destroy($id)
    {
        $form = Form::findOrFail($id);

        // Also delete related responses
        FormResponse::where('form_id', $id)->delete();

        $form->delete();

        return response()->json(['message' => 'Form deleted successfully']);
    }

    /**
     * Submit form response
     */
    public function submit(Request $request, $id)
    {
        $form = Form::findOrFail($id);

        $rules = [];

        foreach ($form->schema as $field) {
            if (!empty($field['required'])) {
                $fieldName = $field['model'] ?? $field['field'] ?? null;
                if ($fieldName) {
                    $rules[$fieldName] = 'required';
                }
            }
        }

        $validated = $request->validate($rules);

        $response = FormResponse::create([
            'form_id' => $id,
            'data' => $request->all(),
        ]);

        return response()->json($response, 201);
    }

    /**
     * Get form responses
     */
    public function responses($id)
    {
        $form = Form::findOrFail($id);
        $responses = FormResponse::where('form_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'form' => $form,
            'responses' => $responses,
        ]);
    }
}