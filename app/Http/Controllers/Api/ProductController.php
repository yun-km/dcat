<?php 
namespace App\Http\Controllers\Api;

use Auth;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function getCategory()
    {
        $categories = ProductCategory::all();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        try {
            Log::info('Request Data: ' . json_encode($request->all()));

            $commonRules = [
                'title' => 'required|string|max:191',
                'summary' => 'required|string|max:191',
                'description' => 'required|string|max:191',
                'product_category_id' => 'required|exists:product_categories,id',
                'tags' => 'required|string|max:191',
            ];
            
            if (!$request->has('product_id')) {
                $specificRules = [
                    'cover' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'pictures.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                ];
            } else {
                $specificRules = [
                    'cover' => 'required',
                    'pictures.*' => 'required',
                ];
            }
            $rules = array_merge($commonRules, $specificRules);
            $validatedData = $request->validate($rules);

            $picturesPaths = [];
            $path = "";
            if ($request->hasFile('pictures')) {
                foreach ($request->file('pictures') as $key => $picture) {
                    if ($picture->isValid()) {
                        $path = $picture->store('images', 'products');
                        $picturesPaths[] = $path;
                    } else {
                        Log::warning("Picture {$key} is not valid.");
                    }
                }
            } 
            if ($request->hasFile('cover')) {
                $path = $request->file('cover')->store('images', 'products');
            } 
            $validatedData['cover'] = $path;
            $validatedData['pictures'] = json_encode($picturesPaths); 

            if ($request->has('product_id')) {
                $product = Product::findOrFail($request->input('product_id'));

                if ($request->hasFile('pictures')) {
                    $existingPictures = json_decode($product->pictures, true);
                    foreach ($existingPictures as $picture) {
                        Storage::disk('products')->delete($picture);
                    }
                }  else {
                    $validatedData['pictures'] = $product->pictures;
                }
                if ($request->hasFile('cover')) {
                    Storage::disk('products')->delete($product->cover);
                } else {
                    $validatedData['cover'] = $product->cover;
                }

                $product->update($validatedData);
    
                return response()->json([
                    'result' => 'success',
                    'message' => 'Product updated successfully',
                    'product' => $product
                ], 200);
            } 

            $user = Auth::user();
            $validatedData['user_id'] = $user->id;
            $product = Product::create($validatedData);

            return response()->json([
                'result' => 'success',
                'message' => 'Product created successfully',
                'product' => $product
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([ 
                'result' => 'error','errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Product Creation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'result' => 'error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}