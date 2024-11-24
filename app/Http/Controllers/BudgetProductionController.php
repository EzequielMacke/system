<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBudgetProductionRequest;
use App\Models\Articulo;
use App\Models\Branch;
use App\Models\BudgetProduction;
use App\Models\BudgetProductionDetail;
use App\Models\WishProductionDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BudgetProductionController extends Controller
{
    public function index()
    {
        $budget_productions = BudgetProduction::with('branch')
                                                ->Active()
                                                ->orderBy('id', 'desc');
        $branches = Branch::where('status',1)->pluck('name','id');
        if(request()->s)
        {
            $budget_productions = $budget_productions->where('id', request()->s);
        }

        if (request()->wish_production_number)
        {
            $budget_productions = $budget_productions->whereHas('budget_production_details', function($query){
                $query->where('wish_production_id', request()->wish_production_number);
            });
        }

        if(request()->branch_id)
        {
            $budget_productions = $budget_productions->where('branch_id', request()->branch_id);
        }
        $budget_productions = $budget_productions->paginate(20);
        return view('pages.budget-production.index', compact('budget_productions','branches'));
    }

    public function create()
    {
        $branches       = Branch::getAllCached()->pluck('name', 'id');
        $articulos       = Articulo::Filter();

        return view('pages.budget-production.create', compact('branches','articulos'));
    }

    public function store(CreateBudgetProductionRequest $request) 
    {
        if(request()->ajax())
        {
            DB::transaction(function() use ($request)
            { 
                $budget_production = BudgetProduction::create([ 'client_id'          => $request->client_id,   
                                                                 'total_amount'       => $request->total_amount, 
                                                                 'status'             => true,
                                                                 'date'               => $request->date,
                                                                 'branch_id'          => $request->branch_id,
                                                                 'user_id'            => auth()->user()->id,
                                                                ]);
                foreach($request->detail_product_id as $key => $product_id)
                {
                    $budget_proction_details        = $budget_production->budget_production_details()->create([ 
                            'quantity'              => $request->quantity_product[$key],
                            'amount'                => $request->detail_product_amount[$key],
                            'budget_production_id'  => $budget_production->id,                      
                            'wish_production_id'    => $request->wish_production_id,                      
    						'articulo_id'           => $product_id
                    ]);
                }
                
                toastr()->success('Agregado exitosamente');
            });

            return response()->json([
                'success' => true
            ]);
        }
        abort(404);
    }

    public function edit(BudgetProduction $budget_production)

    {
        $articulos       = Articulo::Filter();

        return view('pages.budget-production.edit',compact('budget_production','articulos'));
    }

    public function update(BudgetProduction $request, $id)
    {
        if($request->ajax())
        {
            DB::transaction(function() use ($request, $id)
            {
                $detail = BudgetProductionDetail::findOrFail($id);
    
                $detail->update([
                                  'articulo_id'              => $request->detail_product_id,
                                  'quantity'                 => $request->detail_product_quantity,
                                  'quantity'                 => $request->quantity_product,
                                  'amount'                   => $request->detail_product_amount,
                                  'wish_production_id'       => $request->wish_production_id,                      
                                  'articulo_id'              => $request->product_id,
                ]);
            });
    
            return response()->json(['success' => true]);
        }
    }
    public function show(BudgetProduction $budget_production)
    {
        
        return view('pages.budget-production.show', compact('budget_production'));
    }

    public function delete(PurchaseMovement $purchases_movement)
    {
        return view('pages.purchases-movements.delete', compact('purchases_movement'));
    }

    public function delete_submit(DeletePurchasesMovementsRequest $request, PurchaseMovement $purchases_movement)
    {
        DB::transaction(function() use ($purchases_movement, $request)
        {
            $purchases_movement->update([ 'status'         => 2,
                                          'date_deleted'   => date('Y-m-d H:i:s'),
                                          'reason_deleted' => $request->motive,
                                          'user_deleted'   => auth()->user()->id ]);

            foreach($purchases_movement->purchases_movement_details as $details) 
            {            
                if($details->purchases_order_detail)
                {
                    $details->purchases_order_detail->decrement('quantity_received', $details->quantity);
                    $details->purchases_order_detail->decrement('residue', $details->quantity);
                }   
                
                if($details->affects_stock)
                {
                    $purchases_existence = PurchasesExistence::where([ 'id' => $details->purchases_existence_id ])->first();
                    if($purchases_existence)
                    {
                        $purchases_existence->update(['residue' => $purchases_existence->residue - $details->quantity]);
                    }

                    //ACTUALIZACION DE COSTO PROMEDIO 31/08/2021
                    $existences = PurchasesExistence::where('purchases_product_id',  $details->purchases_product_id)
                                        ->where('social_reason_id', $details->purchases_existence->social_reason_id)
                                        ->where('residue', '>', 0)  
                                        ->get();
                    $product_quantity = 0;
                    $total_product_cost  = 0;
                    foreach($existences as $key => $existence)
                    {
                        $total_product_cost += $existence->price_cost * $existence->residue;
                        $product_quantity += intVal($existence->residue);
                    }

                    if($product_quantity > 0)
                    {
                        $purchase_product_cost = PurchasesProductCost::where('purchases_product_id',  $details->purchases_product_id)
                        ->where('social_reason_id', $details->purchases_existence->social_reason_id)
                        ->update([
                            'price_cost' => ($total_product_cost / $product_quantity),
                            'quantity' => $product_quantity]);
                    }

                }

                $details->update([ 'purchases_order_detail_id' => NULL ]);
            }

            //ELIMINAR SI TIENE ANCLADO LA PREFACTURA
            if ($purchases_movement->purchase)
            {
                $purchases_movement->purchase->update([
                                                        'status'         => 2,
                                                        'date_deleted'   => date('Y-m-d H:i:s'),
                                                        'reason_deleted' => $request->motive,
                                                        'user_deleted'   => auth()->user()->id
                                                      ]);
            }

            $purchases_movement->purchase->calendar_payments()->update(['status' => 10]);

            toastr()->success('Eliminado exitosamente');
        });
        return redirect('purchases-movements');
    }

    public function ajax_budget_production()
    {
        if(request()->ajax())
        {
            $results = [];        
            $wish_productions = WishProductionDetail::with('wish_production', 'articulo')
                                                            ->select("wish_production_details.*")
                                                            ->join('wish_productions', 'wish_production_details.wish_production_id', '=', 'wish_productions.id')
                                                            ->where('wish_productions.status', true)
                                                            ->where('wish_productions.id', request()->number_ped)
                                                            ->get();
            foreach ($wish_productions as $key => $order_detail)
            {
                $results['items'][$key]['id']                           = $order_detail->id;
                $results['items'][$key]['product_id']                   = $order_detail->articulo_id;
                $results['items'][$key]['product_name']                 = $order_detail->articulo->name;
                $results['items'][$key]['quantity']                     = $order_detail->quantity;
                $results['items'][$key]['amount']                       = $order_detail->articulo->price;
                $results['items'][$key]['subtotal']                     = $order_detail->articulo->price * $order_detail->quantity;
                $results['items'][$key]['client_id']                    = $order_detail->wish_production->client_id;
                $results['items'][$key]['client']                       = $order_detail->wish_production->client->first_name.' '.$order_detail->wish_production->client->last_name;
                $results['items'][$key]['branch_id']                    = $order_detail->wish_production->branch_id;
                $results['items'][$key]['branch']                       = $order_detail->wish_production->branch->name;
                $results['items'][$key]['date']                         = Carbon::createFromFormat('Y-m-d',$order_detail->wish_production->date)->format('d/m/Y');
                $results['items'][$key]['wish_production_id']           = $order_detail->wish_production->id;

                // $results['ruc']                 = $order_detail->purchase_order->ruc;
                // $results['provider_id']         = $order_detail->purchase_order->provider_id;
                // $results['provider_fullname']   = $order_detail->purchase_order->provider->name;
                // $results['phone']               = $order_detail->purchase_order->phone;
                // $results['social_reason']       = $order_detail->purchase_order->razon_social;
                // $results['address']             = $order_detail->purchase_order->address;
                $results['branch_id']                   = $order_detail->wish_production->branch_id;
            }         
            return response()->json($results);
        }
        abort(404);
    }

    public function ajax_purchases_products_order_details()
    {
        if(request()->ajax())
        {
            $results = []; 
            $count   = 0;       
            $purchases_order_details = PurchaseOrderDetail::with('purchases_order', 'purchases_product', 'purchases_product_presentation')
                                                            ->select("purchases_order_details.*")
                                                            ->join('purchases_order', 'purchases_order_details.purchase_order_id', '=', 'purchases_order.id')
                                                            ->where('purchases_order.status', true)
                                                            ->where('purchases_order.number', request()->number_oc)
                                                            ->get();
            foreach ($purchases_order_details as $key => $order_detail)
            {
                if($order_detail->quantity_received > 0)
                {
                    $existence         = 0;                    
                    $product_existence = PurchasesExistence::where('residue', '>', 0)
                                                            ->where('deposit_id', request()->deposit_id)
                                                            ->where('raw_articulo_id', $order_detail->raw_articulo_id);
                    if($product_existence)
                    {
                        $existence = $product_existence->get()->sum('residue');
                    }

                    if($existence > 0)
                    { 
                        $results['items'][$key]['id']                    = $order_detail->purchases_product_id;                        
                        $results['items'][$key]['name']                  = $order_detail->purchases_product->name;                        
                        $results['items'][$key]['quantity']              = $order_detail->quantity_received > $existence ? $existence : $order_detail->quantity_received;
                        $count++;
                    }
                }                
            }

            $results['total_count'][0] = $count ;
            
            return response()->json($results);
        }
        abort(404);
    }

    public function ajax_product_existences()
    {
        if(request()->ajax())
        {
            $results           = [];
            $product_existence = PurchasesExistence::Select('id','purchases_product_id', 'expiration_date', DB::raw('SUM(residue) as total_residue'))->where('residue', '>', 0)
                                                    ->where('deposit_id', request()->deposit_id)
                                                    ->GroupBy('id','expiration_date')
                                                    ->where('purchases_product_id', request()->purchases_product_id)->limit(10);

            $deposit = Deposit::find(request()->deposit_id);
            if(request()->expiration_date)
            {
                foreach($product_existence->orderBy('expiration_date','DESC')->get() as $purchases_existence)
                {
                    if($purchases_existence->purchases_product)
                    {

                        $results['items'][$purchases_existence->id] = 
                        [
                            'id' => $purchases_existence->id,
                            'product_id' => $purchases_existence->purchases_product_id,
                            'product_name' => $purchases_existence->purchases_product ? $purchases_existence->purchases_product->name : '',
                            'alert_string' => $purchases_existence->expiration_date < now() ? 'alert-danger' : (now()->diffInDays($purchases_existence->expiration_date, false) < 8 ? 'alert-warning' : ''),
                            'expiration_date' => $purchases_existence->expiration_date ? $purchases_existence->expiration_date->format('d/m/Y') : '',
                            'total_residue' => $purchases_existence->total_residue,
                        ];
                    }

                }
            }
            else
            {
                if($product_existence)
                {
                    $product_cost = PurchasesProductCost::where('purchases_product_id',  request()->purchases_product_id)
                                                                                ->where('social_reason_id', $deposit->enterprise->social_reason_id)
                                                                                ->first();
                    $results['items'][0]['price_cost']  = $product_cost ? $product_cost->price_cost : 0;
                    $results['items'][0]['existence']   = $product_existence->get()->sum('total_residue');
                }else
                {
                    $results['items'][0]['price_cost'] = 0;
                    $results['items'][0]['existence']  = 0;
                }
            }
                                   
            return response()->json($results);
        }
        abort(404);
    }

    public function ajax_change_requesting_department()
    {
        if(request()->ajax())
        {
            $results           = [];
            $deposit = Deposit::find(request()->deposit_id);
            $requesting_departments = DepositRequestingDepartment::where('deposit_id',$deposit->id)->get();
            //dd($requesting_departments);
            if($requesting_departments)
            {
                foreach ($requesting_departments as $key => $requesting_department)
                {
                    //dd($requesting_department->purchases_requesting_department->name);
                    $results[$key]['id']   = $requesting_department->requesting_department_id;
                    $results[$key]['name'] = $requesting_department->purchases_requesting_department->name;
                }
            }

            return response()->json($results);
        }
        abort(404);
    }

    private function parse($value)
    {
        return intVal(str_replace(',', '.',str_replace('.', '', $value)));
    }

    private function array_sum($array)
    {
        $total = 0;
        foreach ($array as $key => $value)
        {
            $total += intVal(str_replace(',', '.',str_replace('.', '', $value)));
        }
        return $total;
    }

    public function ajax_purchases_movements()
    {
        // if(request()->ajax())
        // {
        //     $results = [];       
        //     $purchases = Purchase::where('purchases_provider_id', request()->provider_id)
        //                                             ->where('social_reason_id', request()->social_reason_id)
        //                                             ->where('amount', request()->amount)
        //                                             ->where('type', 1)
        //                                             ->whereIn('status',[3,4])
        //                                             ->get();
        //     foreach ($purchases as $key => $purchase)
        //     {
        //         $results['items'][$key]['id']           = $purchase->id;
        //         $results['items'][$key]['product_id']   = $purchase->purchases_product_id;
        //         $results['items'][$key]['number']   = $purchase->number;
        //         $results['items'][$key]['stamped']   = $purchase->stamped;
        //         $results['items'][$key]['stamped_validity']   = $purchase->stamped_validity->format('d-m-Y');
        //         $results['items'][$key]['date']   = $purchase->date->format('d-m-Y');
        //     }         
    
        //     return response()->json($results);
        // }
        // abort(404);
    }
}