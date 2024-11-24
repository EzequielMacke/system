<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderServiceRequest;
use App\Models\Branch;
use App\Models\BudgetService;
use App\Models\ConstructionSite;
use App\Models\OrderService;
use App\Models\Service;
use App\Models\WishService;
use App\Models\WishServiceDetail;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class OrderServiceController extends Controller
{
    public function index()
    {
        $order_services = OrderService::with('client','construction_site')->orderBy('id');
        if(request()->s)
        {
            $order_services = $order_services->whereHas('construction_site', function($query){
                $query->where('description','LIKE', '%'. request()->s . '%');
            })->OrwhereHas('client', function($query2){
                $query2->where('razon_social','LIKE', '%'. request()->s . '%');
            });
        }

        $order_services = $order_services->paginate(20);
        return view('pages.order-service.index', compact('order_services'));
    }

    public function create()
    {
        $construction_sites      = ConstructionSite::pluck('description', 'id');
        $branches               = Branch::where('status', true)->pluck('name', 'id');
        $services               = Service::pluck('description', 'id');
        return view('pages.order-service.create', compact('construction_sites' , 'branches', 'services'));
    }

    public function store(CreateOrderServiceRequest $request)
    {
        if(request()->ajax())
        {
            DB::transaction(function() use ($request)
            {

                $order = OrderService::create([
                    'user_id'               => auth()->user()->id,
                    'construction_site_id'  => request()->site_id,
                    'budget_id'             => request()->budget_id,
                    'client_id'             => request()->client_id,
                    'branch_id'              => request()->branch_id,
                    'date'                  => Carbon::createFromFormat('d/m/Y', request()->date)->format('Y-m-d'),
                    'start_date'            => Carbon::createFromFormat('Y-m-d', request()->start_date)->format('Y-m-d'),
                    'observation'           => request()->observation,
                    'status'                => 1
                ]);

                // Grabar los Productos
                foreach($request->service_id as $key => $value)
                {
                    $order->order_service_details()->create([
                        'order_service_id'          => $order->id,
                        'service_id'                => $value,
                        'quantity'                  => $request->quantity[$key],
                    ]);
                }
            });

            return response()->json([
                'success'            => true,
            ]);
        }
        abort(404);
    }

    public function show(WishProduction $wish_production)
    {

        return view('pages.wish-production.show', compact('wish_production'));
    }
    public function edit(WishProduction $wish_production)
    {
        $productions_client = Client::Filter();
        $articulos          = Articulo::Filter();
        $branches           = Branch::where('status', true)->pluck('name', 'id');

        return view('pages.wish-production.edit',compact('wish_production','articulos','productions_client','branches'));
    }

    public function update(WishProduction $request, $id)
    {
        if($request->ajax())
        {
            DB::transaction(function() use ($request, $id)
            {
                $detail = WishProductionDetail::findOrFail($id);

                $detail->update([
                                  'articulo_id'              => $request->detail_product_id,
                                  'quantity'                 => $request->detail_product_quantity,
                ]);
            });

            return response()->json(['success' => true]);
        }
    }

    public function charge_purchase_budgets(WishPurchase $wish_purchase)
    {
        return view('pages.wish-purchase.purchase_budgets',compact('wish_purchase'));
    }

    public function charge_purchase_budgets_store(WishPurchase $wish_purchase, CreatePurchaseImageRequest $request)
    {

        if (request()->ajax()) {
            if ($request->hasFile('files')) {
                $wish_purchase->purchase_budgets()->delete();

                foreach ($request->file('files') as $key => $input_file) {
                    $file = $input_file;

                    $dir = 'storage/wish_purchases_budgets';
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }

                    if ($file) {
                        $filename = $this->uploadSignature($file);
                    }

                    $wish_purchase->purchase_budgets()->create([
                        'name' => $filename,
                        'original_name' => $file->getClientOriginalName(),
                    ]);
                }
            }

            return response()->json(['success' => true]);
        }
    }

    private function uploadSignature($file)
    {
        $signature_name = Str::random(40) . '.' . $file->getClientOriginalExtension();

        $destinationPath = 'storage/wish_purchases_budgets/' . $signature_name;

        if ($file->move(public_path('storage/wish_purchases_budgets'), $signature_name)) {
            Image::make($destinationPath)
                ->orientate()
                ->save($destinationPath);
        }

        return $signature_name;
    }
    public function confirm_purchase_budgets(WishPurchase $wish_purchase)
    {

        $wish_purchases = $wish_purchase->purchase_budgets()->get();

        return view('pages.wish-purchase.confirm-purchase-budgets',compact('wish_purchase'));
    }

    public function confirm_purchase_budgets_store(PurchaseBudget $purchase_budget)
    {
        $text = 'Presupuesto Aprobado';
        // APROBAR EL PRESUPUESTO
        if(request()->type == 1)
        {
            $purchase_budget->update(['confirmation_user_id'=> auth()->user()->id,'confirmation_date'=> now(),'status'=>2]);
            $purchase_budget->wish_purchase->update(['status' => 5]);
        }
        //BORRAR EL PRESUPUESTO
        elseif(request()->type == 2)
        {
            $purchase_budget->update(['confirmation_user_id'=> auth()->user()->id,'confirmation_date'=> now(),'status'=>3]);
            $text = 'Presupuesto Rechazado';

        }
        // RECHAZAR EL PRESUPUESTO
        elseif(request()->type == 3)
        {
            $text = 'Presupuesto Borrado';
            $purchase_budget->delete();
        }

        if(request()->url)
        {
            return redirect(request()->url);
        }
        else
        {
            return redirect('wish-purchase');
        }
    }

    public function wish_purchase_budgets_approved(WishPurchase $wish_purchase)
    {
        $purchase_budgets = $wish_purchase->purchase_budgets()->where('status',2)->get();

        return view('pages.wish-purchase.wish-purchase-budgets-approved',compact('wish_purchase','purchase_budgets'));
    }

    // public function pdf(WishPurchase $wish_purchase)
    // {
    //      return PDF::loadView('pages.wish-purchase.pdf', compact('wish_purchase'))
    //         ->setPaper('A4', 'portrait')
    //         ->stream();

    // }

    public function searchProviderStamped()
    {
        $invoice_number = explode('-', request()->purchase_number);
        $invoice_number = $invoice_number[0] . '-' . $invoice_number[1];

        $purchase = Purchase::select("purchases.*", DB::raw("DATE_FORMAT(stamped_validity, '%d/%m/%Y') stamp_validity"))
            ->where('provider_id', request()->provider_id)
            ->where('number', 'like', ['%'.$invoice_number.'%'])
            ->whereIn('status', [1,3,4])
            ->orderBy('id', 'DESC')
            ->first();

        return  response()->json($purchase);
    }

    public function pdf(WishPurchase $restocking)
    {
        return PDF::loadView('pages.wish-purchase.pdf', compact('restocking'))
                    ->setPaper([0, 0, 250, 100], 'portrait')
                    // ->setPaper([0,0,300,300], 'portrait')
                    ->stream();
    }
    private function parse($value)
    {
        return str_replace(',', '.',str_replace('.', '', $value));
    }

    public function ajax_wish()
    {
        if(request()->ajax())
        {
            $results   = [];

            if(request()->client_id && request()->site_id)
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
                foreach ($sites as $key => $site) {
                    $results['items'][$key]['id']               = $site->id;
                    $results['items'][$key]['service_id']       = $site->services_id;
                    $results['items'][$key]['service_name']     = $site->service->description;
                    $results['items'][$key]['quantity']         = $site->quantity;
                    $results['items'][$key]['description']      = '';
                }
            }
            return response()->json($results);
        }
        abort(404);
    }

}
