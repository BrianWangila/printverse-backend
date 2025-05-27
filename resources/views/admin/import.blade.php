<!DOCTYPE html>
<html>
    <head>
        <title>Import Products</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    </head>

    <body class="bg-gray-100">
        <div class="container mx-auto p-4">
            <h1 class="text-2xl font-bold mb-4">Import Products</h1>
            @if (session('success'))
                <div class="bg-green-500 text-white p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            <form method="POST" action="{{ route('admin.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700">CSV File</label>
                    <input type="file" name="csv_file" accept=".csv" class="border p-2 w-full">
                    @error('csv_file')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                
                <div class="mb-4">
                    <label class="block text-gray-700">Images (Select all images)</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="border p-2 w-full">
                    @error('images.*')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Import</button>
            </form>
            <p class="mt-4">Download <a href="/sample.csv" class="text-blue-500">sample CSV</a> for correct format.</p>
        </div>
    </body>
</html>