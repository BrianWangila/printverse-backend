<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        $uploadedFiles = $request->file('images') ?: [];
        Log::info('Uploaded file names:', array_map(fn($file) => $file->getClientOriginalName(), $uploadedFiles));

        $csv = Reader::createFromPath($request->file('csv_file')->getRealPath(), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $record) {
            $this->processProductRecord($record, $uploadedFiles);
        }

        return redirect()->back()->with('success', 'Products imported successfully!');
    }



    private function processProductRecord(array $record, array $uploadedFiles)
    {
        try {
            Log::info('Processing CSV record:', $record);

            $isBestSeller = strtolower($record['is_best_seller']) === 'true' || $record['is_best_seller'] == 1;
            $product = Product::create([
                'title' => $record['title'],
                'price' => $record['price'],
                'description' => $record['description'],
                'category' => $record['category'],
                'is_best_seller' => $isBestSeller,
                'features' => json_encode(explode(';', $record['features'])),
            ]);

            Log::info("Created product: {$product->title} (ID: {$product->id})");

            $imageNames = array_map('trim', explode(';', $record['image_names']));
            $this->processImages($product, $imageNames, $uploadedFiles);
        } catch (\Exception $e) {
            Log::error("Error processing product {$record['title']}: " . $e->getMessage());
        }
    }



    private function processImages(Product $product, array $imageNames, array $uploadedFiles)
    {
        Log::info("Image names from CSV for product {$product->title}: ", $imageNames);

        foreach ($imageNames as $index => $imageName) {
            $file = $this->findMatchingFile($imageName, $uploadedFiles);

            if (!$file) {
                Log::warning("No uploaded file matched image: $imageName for product {$product->title}");
                continue;
            }

            try {
                $relativePath = $file->store('images', 'public'); // Returns 'images/filename.jpg'
                $fullPath = 'storage/' . $relativePath; // Prepend 'storage/' to match symlink structure
                Log::info("Stored image $imageName at: $fullPath for product {$product->title}");

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $fullPath,
                    'is_main' => $index === 0,
                ]);

                Log::info("Saved image record for $imageName for product {$product->title}");
           
            } catch (\Exception $e) {
                Log::error("Failed to store image $imageName for product {$product->title}: " . $e->getMessage());
            }
        }
    }



    private function findMatchingFile(string $imageName, array $uploadedFiles): ?\Illuminate\Http\UploadedFile
    {
        foreach ($uploadedFiles as $file) {
            if ($file->getClientOriginalName() === $imageName) {
                Log::info("Matched image: $imageName with uploaded file");
                return $file;
            }
        }
        return null;
    }
}