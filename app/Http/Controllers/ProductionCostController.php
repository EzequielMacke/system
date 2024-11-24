<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateMermaRequest;
use App\Http\Requests\CreateProductionOrderRequest;
use App\Http\Requests\CreateProductionQualityRequest;
use App\Models\Articulo;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Losse;
use App\Models\Merma;
use App\Models\Presentation;
use App\Models\ProductionControl;
use App\Models\ProductionControlDetail;
use App\Models\ProductionControlQuality;
use App\Models\ProductionCost;
use App\Models\ProductionQualityControl;
use App\Models\User;
use App\Models\SettingProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ProductionCostController extends Controller
{
    public function index()
    {
        $costs = ProductionCost::with('branch','quality_control')->orderBy('id', 'desc');

         $costs = $costs->paginate(20);
         return view('pages.production-cost.index', compact('costs'));
    }


    public function show(ProductionCost $production_cost)
    {
        return view('pages.production-cost.show', compact('production_cost'));
    }

    public function ajax_control_calidad()
    {
        if(request()->ajax())
        {
            $results = [];        
            $order_productions = ProductionControlDetail::with('production_control', 'articulo')
                                                            ->select("production_control_details.*")
                                                            ->join('production_controls', 'production_control_details.production_control_id', '=', 'production_controls.id')
                                                            ->where('production_controls.status', true)
                                                            ->where('production_controls.id', request()->number_control)

                                                            ->groupBy('production_control_details.articulo_id')
                                                            ->get();
            foreach ($order_productions as $key => $order_detail)
            {
                $results['items'][$key]['id']           = $order_detail->id;
                $results['items'][$key]['product_id']   = $order_detail->articulo_id;
                $results['items'][$key]['product_name'] = $order_detail->articulo->name;
                $results['items'][$key]['quantity']     = $order_detail->quantity;
                $results['items'][$key]['client_id']    = $order_detail->production_control->client_id;
                $results['items'][$key]['client']       = $order_detail->production_control->client->first_name.' '.$order_detail->production_control->client->last_name;
                $results['items'][$key]['branch_id']    = $order_detail->production_control->branch_id;
                $results['items'][$key]['branch']       = $order_detail->production_control->branch->name;
                $results['items'][$key]['date']         = Carbon::createFromFormat('Y-m-d',$order_detail->production_control->date)->format('d/m/Y');
                $results['branch_id']                   = $order_detail->production_control->branch_id;
                $control = SettingProduct::join('production_qualities','setting_products.production_qualities_id','=','production_qualities.id')->where('articulo_id',$order_detail->articulo_id)->whereNotNull('production_qualities_id')->where('production_qualities.number',request()->sesion)->first();
                if($control)
                {
                    $results['items'][$key]['production_qualities_id']           = $control->production_qualities_id;
                    $results['items'][$key]['qualities_name']         = $control->name;
                }

            }         
            return response()->json($results);
        }
        abort(404);
    }
    
}
