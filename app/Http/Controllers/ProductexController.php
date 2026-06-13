<?php

namespace App\Http\Controllers;
use App\Models\Productex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductexController extends Controller
{
   /* public function index (Request $request){
    
      $data  =  Product::all();
      return  json_decode($data);
    
      $query = DB::table('products');

    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    if ($request->filled('category')) {
        $query->where('category', $request->category);
    }

    if ($request->filled('min_price') && is_numeric($request->min_price)) {
        $query->where('price', '>=', $request->min_price);
    }

    if ($request->filled('max_price') && is_numeric($request->max_price)) {
        $query->where('price', '<=', $request->max_price);
    }

    // Select only necessary columns
    $query->select('id', 'name', 'price', 'category');

    // Pagination
    $products = $query->paginate(10);

    return response()->json($products);
    
   }*/
public function index(Request $request)
{
    $query = Product::query();

    if ($request->filled('search')) {
        $query->where('product', 'like', '%' . $request->search . '%');
    }
 
    

    $products = $query->select('id', 'product', 'price', 'category', 'image')->get();

    return response()->json($products);
}




 public function store(Request $request)
{
    $field = $request->validate([
        'product' => 'required|string|max:250',
        'price' => 'required',
        'category' => 'required|string',
        'image' => 'required|url',
    ]);

    $data = Product::create($field);

    return response()->json([
        'message' => 'Product created successfully',
        'data' => $data
    ]);
}
     
   
  
    public function show(Product $product)
    {
       return  $product ;
    }
       

    public function update(Request $request, Product $product)
    {
         $data = $request->validate([
            'product' => 'required|string|max:250',
            'price' => 'required|max:100',
            'category' => 'required|string|min:50',
            'image' => 'required_without:image|url',

            ]);
          
        $product->update($data);

        return $product;
    }

    
     public function destroy(Product $product){
         
         $product->delete();
         return "the post has been deleted ";
    }
     

    
}



  