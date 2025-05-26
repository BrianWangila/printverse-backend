<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class ProductImportController extends Controller
{
    public function showImportForm()
    {
        return view('admin.import');
    }


    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        
        // Process CSV
        $csv = Reader::createFromPath($request->file('csv_file')->getRealPath(), 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();


        foreach ($records as $record) {
            // Create product
            $product = Product::create([
                'title' => $record['title'],
                'price' => $record['price'],
                'description' => $record['description'],
                'category' => $record['category'],
                'is_best_seller' => $record['is_best_seller'] === 'true' ? 1 : 0,
                'features' => json_encode(explode(';', $record['features'])),
            ]);


            // Handle images
            $imageNames = explode(';', $record['image_names']);
            foreach ($imageNames as $index => $imageName) {
                if ($request->hasFile("images.$imageName")) {
                    $path = $request->file("images.$imageName")->store('images', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_main' => $index === 0, // First image is main
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Products imported successfully!');
    }
}