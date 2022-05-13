<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller {

    public function fetchById($id) {
        return Response()->json(array(
                    'success' => true,
                    'data' => \App\Category::where('id', $id)->first()
        ));
    }

    public function create(Request $request) {
        $data = $request->all();

//        $filters = $data["filters"];
//        $filterslab = $data["filterslab"];
        
//        var_dump($filters);
//        return response()->json($data);
        if ($request->file('img')) {
            $photo = rand(5000, 9999) . $request->file('img')->getClientOriginalName();
            $destination = base_path() . '/public/img/product-images/' . $request->input('seller_id');
            $request->file('img')->move($destination, $photo);
            $data['img'] = $photo;
        } else {
            unset($data['img']);
        }

        

        if (isset($data['cat_id']) && $data['cat_id'] != 0) {
            $result = \App\Category::find($data['cat_id'])->update($data);
        } else {
            $data['cat_id'] = \App\Category::create($data)->id;
        }
        
        if ($result) {
            
            if(isset($data["filters"])){
                $filters = json_decode($data['filters']);
                foreach($filters as $val){
                    $updatearray = array(
                        'subcategory_id'=>$data['cat_id'],
                        'category_id'=>$data['parent_category_id'],
                        'filters'=>$val,
                    );
                    \App\ProductFilters::create($updatearray);
                }
            }
            
            return Response()->json(array(
                        'success' => true,
                        'data' => $result
            ));
        } else {
            return Response()->json(array(
                        'success' => false,
                        'data' => "No Data"
            )); // 400 being the HTTP code for an invalid request.
        }
    }

    public function update($id, Request $request) {
        \App\Category::find($id)->update($request->all());
    }

    public function delete($id) {
        \App\Category::find($id)->delete();
    }

    public function fetchCatFilters($CategoryId) {
        $data = \App\ProductsMeta::where('subcategory_id', $CategoryId)->select('field', 'value')->get();
        if ($data) {
            $myAr = array();
            foreach ($data as $p_row) {
                $key = $p_row['field'];
                $val = $p_row['value'];
                $myAr[$key] = [];
            }
            $data = $myAr;
        }
        return Response()->json(array("success" => true, "data" => $data));
    }

    public function fetchFilters($CategoryId) {
        $data = \App\ProductFilters::where('subcategory_id', $CategoryId)->get();
        $p_data = \App\ProductsMeta::where('subcategory_id', $CategoryId)->select('field', 'value')->get();
        $filters_array = array();

        if ($p_data) {
            $myArLab = array();
            foreach ($p_data as $p_row) {
                $key = $p_row['field'];
                $val = $p_row['value'];
                $filters_array[$key][] = $val;
                $myArLab[$key] = [];
            }
            $myArLab2 = array();
            foreach ($myArLab as $key => $val) {
                if (!in_array($key, $myArLab2))
                    $myArLab2[] = $key;
            }
        }
//        dd($filters_array);
        return Response()->json(array("success" => true, "data" => $filters_array, "labels" => $myArLab, "filterlab" => $myArLab2));
        /*
         * Marker
         * Pencils
         * Pouch
         * Sandal
         * 
         */
        if ($data) {
            $myAr = array();
            $myArLab = array();
            foreach ($data as $row) {
                $myAr[$row['filters']][] = $row['value'];
                $myArLab[$row['filters']] = [];
            }
            $data = $myAr;
        }
        return Response()->json(array("success" => true, "data" => $data, "labels" => $myArLab));
    }

    //
    public function fetch(Request $request) {
        $dataReq = $request->all();
        $p_cat = isset($dataReq['id']) ? $dataReq['id'] : 0;
        $data = \App\Category::where([['parent_category_id', $p_cat], ['status', 1]])->with('children')->get();
        if ($data) {
            $myAr = array();
            foreach ($data as $row) {
                $new_title = strtolower(preg_replace('/\s+/', '', $row['title']));
                $new_title = str_replace("&", "-", $new_title);
                $row['newtitle'] = $new_title;
                $myAr[] = $row;
            }
            $data = $myAr;
            return Response()->json(array(
                        'success' => true,
                        'data' => $data
            )); // 400 being the HTTP code for an invalid request.
        } else {
            return Response()->json(array(
                        'success' => false,
                        'data' => "No Data"
            )); // 400 being the HTTP code for an invalid request.
        }
        //return response()->json($data);
    }

    public function fetchSub() {
        $data = \App\Category::where([['parent_category_id', ">", 0], ['status', 1]])->with('parent')->get();
        if ($data)
            return Response()->json(array(
                        'success' => true,
                        'data' => $data));
        else
            return Response()->json(array(
                        'success' => false,
                        'data' => "No Data"
            )); // 400 being the HTTP code for an invalid request.
    }

    public function fetchWithLimit(Request $request) {
        $dataReq = $request->all();
        $p_cat = isset($dataReq['id']) ? $dataReq['id'] : 0;
        $data = \App\Category::where([['parent_category_id', $p_cat], ['status', 1]])->with('children')->limit(10)->get();
        if ($data) {
            $myAr = array();
            foreach ($data as $row) {
                $new_title = strtolower(preg_replace('/\s+/', '', $row['title']));
                $new_title = str_replace("&", "-", $new_title);
                $row['newtitle'] = $new_title;
                $myAr[] = $row;
            }
            $data = $myAr;
            return Response()->json(array(
                        'success' => true,
                        'data' => $data
            )); // 400 being the HTTP code for an invalid request.
        } else {
            return Response()->json(array(
                        'success' => false,
                        'data' => "No Data"
            )); // 400 being the HTTP code for an invalid request.
        }
        //return response()->json($data);
    }

    public function fetchAll() {
        $data = \App\Category::with('children')->get();
        if ($data) {
            return Response()->json(array(
                        'success' => true,
                        'data' => $data
            )); // 400 being the HTTP code for an invalid request.
        } else {
            return Response()->json(array(
                        'success' => false,
                        'data' => "No Data"
            )); // 400 being the HTTP code for an invalid request.
        }
    }

    public function fetchBrands(Request $request) {
        $data = $request->all();
        $products = \App\Product::where("sub_category", $data['sub_category'])->get();
        $myAr = array();
        if ($products) {

            foreach ($products as $row) {
                if ($row['brand'] == null) {
                    $row['brand'] = "No Brand";
                }
                $myAr[$row['brand']] = $row['brand'];
            }
        }
        return Response()->json(array(
                    'success' => true,
                    'data' => $myAr
        )); // 400 being the HTTP code for an invalid request.
    }

}
