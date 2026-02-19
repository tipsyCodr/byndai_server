<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SMS Templates Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=Outfit:300,400,500,600,700" rel="stylesheet" />
    <style>
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">

    <div class="max-w-7xl mx-auto px-6 py-8">
        <h1 class="text-3xl font-bold mb-8 text-[#1d213e]">SMS Templates Manager</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Form Section -->
            <div class="md:col-span-1">
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                    <h2 class="text-xl font-bold mb-4" id="formTitle">Create New Template</h2>
                    <form action="{{ route('sms-templates.store') }}" method="POST" id="templateForm">
                        @csrf
                        <input type="hidden" name="_method" value="POST" id="methodField">
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Section / Key</label>
                            <input type="text" name="section" id="section" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="e.g. login_otp" required>
                            <p class="text-xs text-gray-500 mt-1">Unique identifier for code reference.</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">DLT Template ID</label>
                            <input type="text" name="dlt_template_id" id="dlt_template_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="DLT ID from provider" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Message Content</label>
                            <textarea name="message_content" id="message_content" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Your message here with {#var#}" required></textarea>
                            <p class="text-xs text-gray-500 mt-1">Use <code>{#var#}</code> for dynamic variables.</p>
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="status" id="status" value="1" checked class="form-checkbox h-5 w-5 text-blue-600">
                                <span class="ml-2 text-gray-700">Active</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-[#1ea9ff] hover:bg-[#1890db] text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full transition duration-300">
                                Save Template
                            </button>
                            <button type="button" onclick="resetForm()" id="cancelBtn" class="hidden ml-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List Section -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Section</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">DLT ID</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Message</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templates as $template)
                            <tr>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <span class="font-bold text-gray-900">{{ $template->section }}</span>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">{{ $template->dlt_template_id }}</span>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap truncate max-w-xs" title="{{ $template->message_content }}">{{ $template->message_content }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    @if($template->status)
                                        <span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                            <span aria-hidden="true" class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                            <span class="relative">Active</span>
                                        </span>
                                    @else
                                        <span class="relative inline-block px-3 py-1 font-semibold text-red-900 leading-tight">
                                            <span aria-hidden="true" class="absolute inset-0 bg-red-200 opacity-50 rounded-full"></span>
                                            <span class="relative">Inactive</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                    <div class="flex gap-2">
                                        <button onclick="editTemplate({{ json_encode($template) }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                        <form action="{{ route('sms-templates.destroy', $template->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editTemplate(template) {
            document.getElementById('formTitle').innerText = 'Edit Template';
            document.getElementById('templateForm').action = "/sms-templates/" + template.id;
            document.getElementById('methodField').value = "PUT";
            document.getElementById('section').value = template.section;
            document.getElementById('dlt_template_id').value = template.dlt_template_id;
            document.getElementById('message_content').value = template.message_content;
            document.getElementById('status').checked = template.status;
            document.getElementById('cancelBtn').classList.remove('hidden');
        }

        function resetForm() {
            document.getElementById('formTitle').innerText = 'Create New Template';
            document.getElementById('templateForm').action = "{{ route('sms-templates.store') }}";
            document.getElementById('methodField').value = "POST";
            document.getElementById('section').value = "";
            document.getElementById('dlt_template_id').value = "";
            document.getElementById('message_content').value = "";
            document.getElementById('status').checked = true;
            document.getElementById('cancelBtn').classList.add('hidden');
        }
    </script>
</body>
</html>
