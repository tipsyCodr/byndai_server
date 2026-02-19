<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\SmsTemplate;

class SmsTemplateController extends Controller
{
    public function index()
    {
        $templates = \App\Models\SmsTemplate::all();
        // Decode message for view
        foreach ($templates as $template) {
            $data = json_decode($template->message, true);
            $template->message_content = $data['message'] ?? '';
            $template->dlt_template_id = $data['DLTTemplateId'] ?? '';
        }
        return view('sms-templates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'section' => 'required|unique:sms_templates,section',
            'dlt_template_id' => 'required',
            'message_content' => 'required',
            'status' => 'boolean'
        ]);

        $messageJson = json_encode([
            'message' => $request->message_content,
            'DLTTemplateId' => $request->dlt_template_id
        ]);

        \App\Models\SmsTemplate::create([
            'section' => $request->section,
            'message' => $messageJson,
            'status' => $request->status ?? true,
        ]);

        return redirect()->back()->with('success', 'Template created successfully.');
    }

    public function update(Request $request, $id)
    {
        $template = \App\Models\SmsTemplate::findOrFail($id);

        $request->validate([
            'section' => 'required|unique:sms_templates,section,' . $id,
            'dlt_template_id' => 'required',
            'message_content' => 'required',
            'status' => 'boolean'
        ]);

        $messageJson = json_encode([
            'message' => $request->message_content,
            'DLTTemplateId' => $request->dlt_template_id
        ]);

        $template->update([
            'section' => $request->section,
            'message' => $messageJson,
            'status' => $request->status ?? true,
        ]);

        return redirect()->back()->with('success', 'Template updated successfully.');
    }

    public function destroy($id)
    {
        \App\Models\SmsTemplate::destroy($id);
        return redirect()->back()->with('success', 'Template deleted successfully.');
    }
}
