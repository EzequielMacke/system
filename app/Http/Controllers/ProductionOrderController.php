<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductionOrderRequest;
use App\Models\Articulo;
use App\Models\Branch;
use App\Models\BudgetProductionDetail;
use App\Models\Presentation;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderDetail;
use App\Models\Provider;
use App\Models\PurchaseBudget;
use App\Models\RawMaterial;
use App\Models\User;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ProductionOrderController extends Controller
{
    public function index()
    {
        $purchases_providers = Provider::Filter();
        $order           = ProductionOrder::with('branch')
            ->orderBy('id', 'desc');

        if (request()->o)
        {
            $order = $order->where('ruc', 'LIKE', '%' . request()->o . '%')
                ->orWhere('number', 'LIKE', '%' . request()->o . '%');
        }

         $order = $order->paginate(20);
         return view('pages.production-order.index', compact('order', 'purchases_providers'));
    }

    public function create()
    {
        $users                  = User::filter();
        $branches               = Branch::where('status', true)->pluck('name', 'id');
        $articulos               = Articulo::Filter();
        $product_presentations  = Presentation::Filter();
        $provider_suggesteds    = NULL;
        return view('pages.production-order.create', compact('users' , 'branches', 'articulos', 'product_presentations','provider_suggesteds'));
    }

    public function store(CreateProductionOrderRequest $request)
    {
        if(request()->ajax())
        {
            DB::transaction(function() use ($request, & $production_order)
            {
                
                $production_order = ProductionOrder::create([
                    'date'              => $request->date,
                    'status'            => 1,
                    'client_id'         => $request->client_id,
                    'team_work_id'      => 1,
                    'branch_id'         => $request->branch_id,
                    'user_id'           => auth()->user()->id
                ]);

                // Grabar los Productos
                foreach($request->detail_product_id as $key => $value)
                {
                    foreach ($request->{"selected_materials_$value"} as $key1 => $value1) 
                    {
                        $production_order->production_order_details()->create([
                            'articulo_id'              => $value1,
                            'articulo_id'              => $value,
                            'quantity_material'        => $request->{"selected_materials_quantity_$value"}[$key1],
                            'quantity'                 => $request->detail_product_quantity[$key],
                            'production_order_id'      => $production_order->id,
                        ]);
                    }
                }
            });

            return response()->json([
                'success'            => true,
            ]);
        }
        abort(404);
    }

    public function show(ProductionOrder $production_order)
    {
       
        
        return view('pages.production-order.show', compact('production_order'));
    }
    public function edit(ProductionOrder $production_order)
    {

        return view('pages.production-order.edit',compact('production_order'));
    }

    public function update(ProductionOrder $production_order)
    {
        if(request()->all())
        {
            DB::transaction(function() use ($production_order)
            {
                
                $production_order->update([
                    'date'              => request()->date,
                    'status'            => 1,
                    'client_id'         => request()->client_id,
                    'team_work_id'      => 1,
                    'branch_id'         => request()->branch_id,
                    'user_id'           => auth()->user()->id
                ]);

                // Grabar los Productos
            $production_order->production_order_details()->delete();
                foreach(request()->detail_product_id as $key => $value)
                {
                    foreach (request()->{"detail_articulo_id_$value"} as $key1 => $value1) 
                    {
                        $production_order->production_order_details()->create([
                            'articulo_id'              => $value1,
                            'articulo_id'              => $value,
                            'quantity_material'        => request()->{"detail_material_quantity_$value"}[$key1],
                            'quantity'                 => request()->detail_product_quantity[$key],
                            'production_order_id'      => $production_order->id,
                        ]);
                    }
                }
            });
    
            return redirect('production-order');

        }
    }

    public function charge_purchase_budgets(PurchaseOrder $wish_purchase)
    {
        return view('pages.wish-purchase.purchase_budgets',compact('wish_purchase'));
    }

    public function ajax_order_production()
    {
        if(request()->ajax())
        {
            $results = [];        
            $order_productions = BudgetProductionDetail::with('budget_production', 'articulo')
                                                            ->select("budget_production_details.*")
                                                            ->join('budget_productions', 'budget_production_details.budget_production_id', '=', 'budget_productions.id')
                                                            ->where('budget_productions.status', true)
                                                            ->where('budget_productions.id', request()->number_budget)
                                                            ->get();
            foreach ($order_productions as $key => $order_detail)
            {
                $results['items'][$key]['id']           = $order_detail->id;
                $results['items'][$key]['product_id']   = $order_detail->articulo_id;
                $results['items'][$key]['product_name'] = $order_detail->articulo->name;
                $results['items'][$key]['quantity']     = $order_detail->quantity;
                $results['items'][$key]['client_id']    = $order_detail->budget_production->client_id;
                $results['items'][$key]['client']       = $order_detail->budget_production->client->first_name.' '.$order_detail->budget_production->client->last_name;
                $results['items'][$key]['branch_id']    = $order_detail->budget_production->branch_id;
                $results['items'][$key]['branch']       = $order_detail->budget_production->branch->name;
                $results['items'][$key]['date']         = $order_detail->budget_production->date->format('d/m/Y');
                // $results['ruc']                 = $order_detail->purchase_order->ruc;
                // $results['provider_id']         = $order_detail->purchase_order->provider_id;
                // $results['provider_fullname']   = $order_detail->purchase_order->provider->name;
                // $results['phone']               = $order_detail->purchase_order->phone;
                // $results['social_reason']       = $order_detail->purchase_order->razon_social;
                // $results['address']             = $order_detail->purchase_order->address;
                $results['branch_id']           = $order_detail->budget_production->branch_id;
            }         
            return response()->json($results);
        }
        abort(404);
    }

    public function ajax_modal_material()
    {
        if(request()->ajax())
        {
            $results = [];        
            $articulo = Articulo::where('id',request()->product_id)->first();
            $order = BudgetProductionDetail::where('budget_production_id',request()->number_budget)->where('articulo_id',request()->product_id)->first();
            foreach ($articulo->setting_product as $key => $setting)
            {
                if($setting->raw_materials_id)
                {
                    $results['items'][$key]['id']           = $setting->id;
                    $results['items'][$key]['articulo_id']   = $setting->articulo_id;
                    $results['items'][$key]['articulo_name'] = $setting->articulo->name;
                    $results['items'][$key]['raw_articulo_id']     = $setting->raw_materials_id;
                    $results['items'][$key]['raw_material']     = $setting->raw_material->description;
                    $results['items'][$key]['quantity']        = $setting->quantity * $order->quantity;
                }
            }         
            return response()->json($results);
            
        }
        abort(404);
    }
    
}
