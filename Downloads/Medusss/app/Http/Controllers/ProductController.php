<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ProductController extends Controller
{
    public function index()
    {
        $products = DB::table('products')->get();

        if (count($products) > 0) {
            $response = [
                'success' => true,
                'data' => $products,
            ];
        } else {
            $response = [
                'success' => true,
                'data' => 'No product found',
            ];
        }
        return  response()->json($response);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qty' => 'required',
            'name' => 'required|string|min:2|max:100',
            'price' => 'required',
            'picture' => 'required|file',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $id=JWTAuth::parseToken()->authenticate()->id;

        if ($request->hasFile('picture')) {
            $image = $request->file('picture');
            $name = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/product');
            $image->move($destinationPath, $name);
            $path='product/'.$name;
        }
        $data= Product::create([
            'store_id'=>$id,
            'qty'=>$request->qty,
            'name'=>$request->name,
            'price'=>$request->price,
            'picture'=>$path
        ]);
        if($data){
            $response = [
                'success' => true,
                'data' => 'Record save successfully',
            ];
        } else {
            $response = [
                'success' => false,
                'data' => 'There is some error',
            ];
        }
        return  response()->json($response);

    }
}

