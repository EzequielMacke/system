<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBudgetsServiceRequest;
use App\Http\Requests\CreateWishServiceRequest;
use App\Models\Branch;
use App\Models\BudgetService;
use App\Models\BudgetServiceDetail;
use App\Models\Client;
use App\Models\ConstructionSite;
use App\Models\Input;
use App\Models\Service;
use App\Models\WishService;
use App\Models\WishServiceDetail;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class BudgetServiceController extends Controller
{
    public function index()
    {
        $budgetservices = BudgetService::with('client','construction_site')->orderBy('id');
        if(request()->s)
        {
            $budgetservices = $budgetservices->whereHas('construction_site', function($query){
                $query->where('description','LIKE', '%'. request()->s . '%');
            })->OrwhereHas('client', function($query2){
                $query2->where('razon_social','LIKE', '%'. request()->s . '%');
            });
        }

        $budgetservices = $budgetservices->paginate(20);
        return view('pages.budget-service.index', compact('budgetservices'));
    }

    public function create()
    {
        $construction_sites      = ConstructionSite::pluck('description', 'id');
        $branches               = Branch::where('status', true)->pluck('name', 'id');
        $services               = Service::pluck('description', 'id');
        return view('pages.budget-service.create', compact('construction_sites' , 'branches', 'services'));
    }

    public function store(CreateBudgetsServiceRequest $request)
    {
        if(request()->ajax())
        {
            DB::transaction(function() use ($request)
            {

                $budget = BudgetService::create([
                        'description'           => request()->observation,
                        'user_id'               => auth()->user()->id,
                        'client_id'             => request()->client_id,
                        'wish_service_id'       => request()->wish_id,
                        'constructionsite_id'   => request()->site_id,
                        'date_budgets'          => Carbon::createFromFormat('d/m/Y', request()->date)->format('Y-m-d'),
                        'tax'                   => request()->tax,
                        'currency'              => request()->currency,
                        'branch_id'             => request()->branch_id,
                        'status'                => 1
                ]);
                foreach ($request->input_id as $key => $value) {
                    $input_value = explode("-",$value);
                    $precio = str_replace([',', '.'], '', $request->price[$key]);
                    $budget->budget_service_detail()->create([
                        'budget_service_id' => $budget->id, // ID del presupuesto
                        'service_id' => $input_value[0], // ID del servicio
                        'quantity' => $request->new_metro[$key], // Cantidad del servicio
                        'price' => intval($precio), // Precio del servicio
                        'level' => $request->new_level[$key], // Nivel
                        'total_price' => ($request->new_metro[$key] * intval($precio)) * $request->new_level[$key], // Precio total
                        'quantity_per_meter' => $request->quantity_per_meter[$key] ?? 0, // Cantidad por metro
                        'input_id' => $input_value[1] // ID de entrada convertido a entero
                    ]);
                }
                $wish_service = WishService::find($request->wish_id);
                if ($wish_service) {
                    $wish_service->status = 2;
                    $wish_service->save();
                }
            });

            return response()->json([
                'success'            => true,
            ]);
        }
        abort(404);
    }
    public function changeStatus($id)
    {
        $budgetService = BudgetService::find($id);
        if ($budgetService && $budgetService->status != 3) {
            $budgetService->status = 3;
            $budgetService->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function edit($id)
    {
        $budgetService = BudgetService::findOrFail($id);
        $clients = Client::pluck('razon_social', 'id');
        $construction_sites = ConstructionSite::pluck('description', 'id');
        $services = Service::pluck('description', 'id');
        return view('pages.budget-service.edit', compact('budgetService', 'clients', 'construction_sites', 'services'));
    }



    public function ajax_wish()
    {
        if(request()->ajax())
        {
            $results   = [];
            if(request()->client_id && request()->site_id && request()->type == 'presupuesto')
            {
                $sites = BudgetService::where('client_id',request()->client_id)->where('constructionsite_id',request()->site_id)->where('status',1)->get();
                foreach ($sites as $key => $site) {
                    $results['items'][$key]['id']               = $site->id;
                    $results['items'][$key]['date_budget']      = $site->id.' - '.Carbon::createFromFormat('Y-m-d',$site->date_budgets)->format('d/m/Y');
                }
            }
            else if(request()->budget_id)
            {
                $sites = BudgetServiceDetail::where('budget_service_id',request()->budget_id)->get();
                foreach ($sites as $key => $site) {
                    $results['items'][$key]['id']               = $site->id;
                    $results['items'][$key]['service_id']       = $site->service_id;
                    $results['items'][$key]['service_name']     = $site->service->description;
                    $results['items'][$key]['quantity']         = $site->quantity;
                    $results['items'][$key]['level']            = $site->level;
                    $results['items'][$key]['description']      = '';
                }
            }
            else if(request()->client_id && request()->site_id)
            {
                $sites = WishService::where('client_id',request()->client_id)->where('construction_site_id',request()->site_id)->where('status',1)->get();
                foreach ($sites as $key => $site) {
                    $results['items'][$key]['id']               = $site->id;
                    $results['items'][$key]['date_budget']      = $site->id.' - '.Carbon::createFromFormat('Y-m-d',$site->date_wish)->format('d/m/Y');
                }
            }
            else if(request()->wish_id)
            {
                $sites = WishServiceDetail::where('wish_services_id',request()->wish_id)->get();
                foreach ($sites as $key0 => $site)
                {
                    $services = Input::where('typeofservice',$site->services_id)->get();
                    foreach ($services as $key => $service)
                    {
                        $results['items'][$site->id.' '.$key]['id']               = $site->id;
                        $results['items'][$site->id.' '.$key]['service_id']       = $site->services_id;
                        $results['items'][$site->id.' '.$key]['service_name']     = $site->service->description;
                        $results['items'][$site->id.' '.$key]['quantity']         = $site->quantity;
                        $results['items'][$site->id.' '.$key]['level']            = $site->level;
                        $results['items'][$site->id.' '.$key]['input']            = $service->description;
                        $results['items'][$site->id.' '.$key]['input_id']         = $service->id;
                        $results['items'][$site->id.' '.$key]['input_price']      = $service->price;
                        $results['items'][$site->id.' '.$key]['measurement']      = $service->measurement;
                        $results['items'][$site->id.' '.$key]['description']      = '';
                    }
                }
            }
            return response()->json($results);
        }
        abort(404);
    }

}
