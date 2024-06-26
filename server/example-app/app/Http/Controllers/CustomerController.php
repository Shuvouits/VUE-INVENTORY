<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function AddCustomer(Request $request){

        $name = $request->input('name');
        $address = $request->input('address');

        $customer = new Customer();
        $customer->name = $name;
        $customer->address = $address;
        $customer->save();

        return response()->json(['message' => "New customer inserted"]);

    }

    public function AllCustomer(){

       $customer = Customer::orderBy('id','DESC')->get();

        return response()->json($customer);

    }

    public function EditCustomer(Request $request , $id){
        try{
            $data = Customer::where('id', $id)->first();
            return response()->json([
                'name' => $data->name,
                'address' => $data->address

            ],201);

        }catch(\Exception $error){
            dd($error->getMessage());

        }
    }

    public function UpdateCustomer(Request $request , $id){
        try{

            $name = $request->input('name');
            $address = $request->input('address');

            $customer_data = Customer::where('id', $id)->first();

            if(!$customer_data){

                return response()->json([
                    'message' => 'No supplier found'
    
                ],404);

            }

            $customer_data->name = $name;
            $customer_data->address = $address;
           
            $customer_data->save();

            return response()->json([
                'message' => 'Data updated successfully'

            ],201);

        }catch(\Exception $error){
            dd($error->getMessage());

        }
    }

    public function CustomerDetails($id){

        try{
            $sale_data = Sale::where('customer_id', $id)->with('product')->orderBy('id', 'DESC')->get();
            $customer_data = Customer::where('id', $id)->first();

            $total_quantity = Sale::where('customer_id', $id)->sum('quantity');
            $g_total = Sale::where('customer_id', $id)->sum('g_total');
            $p_amount = Sale::where('customer_id', $id)->sum('p_amount');
            $d_amount = Sale::where('customer_id', $id)->sum('d_amount');
    
            return response()->json([
                'sale_data' => $sale_data,
                'customer_data' => $customer_data,
                'total_quantity' => $total_quantity,
                'g_total' => $g_total,
                'p_amount' => $p_amount,
                'd_amount' => $d_amount
            ], 201);

        }catch (\Exception $error) {
            dd($error->getMessage());

        }
    }


    public function DeleteCustomer(Request $request, $id)
    {
        try {

            $data = Customer::where('id', $id)->first();

            if (!$data) {

                return response()->json([
                    'message' => 'No brand data found'

                ], 404);

            }

            Customer::where('id', $id)->delete();


            return response()->json([
                'message' => 'Data deleted successfully'

            ], 201);

        } catch (\Exception $error) {
            dd($error->getMessage());

        }
    }


}
