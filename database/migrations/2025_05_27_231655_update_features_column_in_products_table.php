<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;



class UpdateFeaturesColumnInProductsTable extends Migration
{
    public function up()
    {
        // Change features column to text
        Schema::table('products', function (Blueprint $table) {
            $table->text('features')->change();
        });

        // Convert existing JSON features to comma-separated string
        Product::all()->each(function ($product) {
            if (is_array($product->features)) {
                $product->features = implode(', ', $product->features);
                $product->save();
            }
        });
    }



    public function down()
    {
        // Revert features to JSON
        Schema::table('products', function (Blueprint $table) {
            $table->json('features')->change();
        });

        // Convert back to JSON (optional, adjust as needed)
        Product::all()->each(function ($product) {
            $features = array_map('trim', explode(',', $product->features));
            $product->features = $features;
            $product->save();
        });
    }
}