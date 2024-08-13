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
            $user = Auth::user();

            $validatedData = $request->validate([
                'title' => 'required|string|max:191',
                'summary' => 'required|string|max:191',
                'description' => 'required|string|max:191',
                'cover' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'product_category_id' => 'required|exists:product_categories,id',
                'tags' => 'required|string|max:191',
                'pictures.*' =>  'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $slug = Str::slug($validatedData['title'], '-');
            $validatedData['slug'] = $slug;

            $path = $request->file('cover')->store('images', 'products');
            $validatedData['cover'] = $path;

            Log::info('Has pictures: ' . json_encode($request->hasFile('pictures')));
            Log::info('Pictures Files: ' . json_encode($request->file('pictures')));

            $picturesPaths = [];
            if ($request->hasFile('pictures')) {
                foreach ($request->file('pictures') as $key => $picture) {
                    if ($picture->isValid()) {
                        $path = $picture->store('images', 'products');
                        $picturesPaths[] = $path;
                    } else {
                        Log::warning("Picture {$key} is not valid.");
                    }
                }
            } else {
                Log::warning('No pictures were uploaded.');
            }
            Log::info('picturesPaths: ' .json_encode($picturesPaths));
            $validatedData['pictures'] = json_encode($picturesPaths); 

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