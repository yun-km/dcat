<?php 
namespace App\Http\Controllers\Api;

use Auth;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\ProductItemType;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ProductItemTypeOption;
use App\Models\ProductOptionInventory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function getCategory()
    {
        $categories = ProductCategory::all();
        return response()->json($categories);
    }

    public function getProductInfo($productId)
    {
        try {
            $product = Product::find($productId);
    
            if (!$product) {
                return response()->json(['error' => 'No product found'], 404);
            }
    
            return response()->json(['product' => $product], 200);
        } catch (\Exception $e) {
            return response()->json([
                'result' => 'error',
                'error' => 'Failed to fetch product info',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getSellerProducts() {
        try {
            $userId = Auth::user()->id;

            $products = Product::where('user_id', $userId)->get();
    
            if ($products->isEmpty()) {
                return response()->json(['error' => 'No products found for this user'], 404);
            }
    
            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred', 'message' => $e->getMessage(),], 500);
        }
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

    public function storeProductTypeOptions(Request $request)
    {
        try {

            Log::info('Request Data: ' . json_encode($request->all()));

            $validatedData = $request->validate([
                'product_id' => 'required|integer',
                'types' => 'required|array',
                'types.*.id' => 'nullable|integer',
                'types.*.typeName' => 'required|string|max:255',
                'types.*.options' => 'nullable|array',
                'types.*.options.*.id' => 'nullable|integer', 
                'types.*.options.*.optionName' => 'required|string|max:255',
            ]);

            $createdTypes = [];
    
            foreach ($validatedData['types'] as $typeData) {
                $productItemType = ProductItemType::updateOrCreate(
                    ['id' => $typeData['id'] ?? null], 
                    [
                        'type_name' => $typeData['typeName'],
                        'order' => 0,
                        'product_id' => $request->product_id,
                    ]
                );
    
                $options = [];

                if (isset($typeData['options'])) {
                    foreach ($typeData['options'] as $optionData) {
                        $option = ProductItemTypeOption::updateOrCreate(
                            ['id' => $optionData['id'] ?? null],
                            [
                                'product_item_types_id' => $productItemType->id,
                                'option_name' => $optionData['optionName'],
                                'is_active' => 1,
                                'product_id' => $request->product_id,
                            ]
                        );
                        $options[] = [
                            'id' => $option->id,
                            'optionName' => $option->option_name,
                            'product_item_types_id' => $option->product_item_types_id,
                        ];
                    }
                }
                
                $createdTypes[] = [
                    'id' => $productItemType->id,
                    'typeName' => $productItemType->type_name,
                    'options' => $options,
                ];
            }

            return response()->json([
                'result' => 'success',
                'message' => 'Product item types and options created successfully!',
                'types' => $createdTypes
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
    public function getProductTypeOptions($productId)
    {
        $productItemTypes = ProductItemType::where('product_id', $productId)->get();

        $result = [];

        foreach ($productItemTypes as $type) {
            $options = $type->options()->get();

            $result[] = [
                'id' => $type->getAttribute('id'),
                'typeName' => $type->getAttribute('type_name'),
                'options' => $options->map(function ($option) {
                    return [
                        'id' => $option->getAttribute('id'),
                        'optionName' => $option->getAttribute('option_name'),
                        'product_item_types_id' => $option->getAttribute('product_item_types_id'),
                    ];
                }),
            ];
        }

        return response()->json($result);
    }
    public function getInventories($productId)
    {
        try {
            $inventories = ProductOptionInventory::where('product_id', $productId)->get();

            $formattedInventories = $inventories->map(function($inventory) {
                $parsedOptions = json_decode($inventory->product_item_type_option_id, true);

                $optionDetails = collect($parsedOptions)->map(function($option) {
                    $type = ProductItemType::find($option['type']);
                    $optionName = ProductItemTypeOption::find($option['option']);
                    
                    return [
                        'typeName' => $type ? $type->type_name : '未知規格',
                        'optionName' => $optionName ? $optionName->option_name : '未知選項'
                    ];
                });

                return [
                    'id' => $inventory->id,
                    'productId' => $inventory->product_id,
                    'price' => $inventory->price,
                    'totalQuantity' => $inventory->total_quantity,
                    'productItemTypeOptionId' => $optionDetails
                ];
            });

            return response()->json($formattedInventories, 200);
        } catch (\Exception $e) {
            return response()->json(['result' => 'error','error' => 'Failed to fetch inventories', 'message' => $e->getMessage()], 500);
        }
    }


    public function saveInventory(Request $request)
    {
        try {
            Log::info('Request Data: ' . json_encode($request->all()));

            $commonRules = [
                'id' => 'nullable|integer',
                'price' => 'required|integer',
                'totalQuantity' => 'required|integer',
                'productId' => 'required|integer',
            ];

            if (!$request->has('id')) {
                $createRules = [
                    'productItemTypeOptionId' => 'required|array',
                    'productItemTypeOptionId.*.type' => 'required|integer',
                    'productItemTypeOptionId.*.option' => 'required|integer',
                ];

                $rules = array_merge($commonRules, $createRules);
                $validatedData = $request->validate($rules);

                $inventory = ProductOptionInventory::updateOrCreate(
                    [
                        'product_id' => $validatedData['productId'],
                        'product_item_type_option_id' => json_encode($validatedData['productItemTypeOptionId']),
                    ],
                    [
                        'price' => $validatedData['price'],
                        'total_quantity' => $validatedData['totalQuantity'],
                    ]
                );
            } else {
                $validatedData = $request->validate($commonRules);

                $inventory = ProductOptionInventory::where('id', $validatedData['id'])
                ->where('product_id', $validatedData['productId'])
                ->first();

                if ($inventory) {
                    $inventory->update([
                        'price' => $validatedData['price'],
                        'total_quantity' => $validatedData['totalQuantity'],
                    ]);
                } else {
                    return response()->json(['result' => 'error', 'error' => 'Inventory not found'], 404);
                }
            }

            return response()->json(['result' => 'success', 'inventory' => $inventory], 201);
        } catch (\Exception $e) {
            return response()->json(['result' => 'error','error' => 'Failed to save inventory', 'message' => $e->getMessage()], 500);
        }
    }

    public function getSellerProductsAndInventories(Request $request) {
        try {
            $productsResponse = $this->getSellerProducts();
            $products = $productsResponse->getData();
            if (isset($products->error)) {
                return $productsResponse;
            }

            $search = $request->input('search');
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);
            $userId = Auth::user()->id;
    
            $query = Product::where('user_id', $userId);
            if ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('summary', 'like', "%{$search}%");
            }

            $products = $query->paginate($perPage, ['*'], 'page', $currentPage);
            Log::info('Paginated products with inventories:', $products->toArray());
            $productsWithInventories = $products->getCollection()->transform(function($product) {
                $inventoriesResponse = $this->getInventories($product->id);
                $inventories = $inventoriesResponse->getData();
                return [
                    'product' => $product,
                    'inventories' => $inventories
                ];
            });
    
            return response()->json([$productsWithInventories, $products], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred', 'message' => $e->getMessage()], 500);
        }
    }
    
}