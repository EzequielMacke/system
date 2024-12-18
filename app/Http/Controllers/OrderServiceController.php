<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderServiceRequest;
use App\Models\Branch;
use App\Models\BudgetService;
use App\Models\BudgetServiceDetail;
use App\Models\ConstructionSite;
use App\Models\Contracts;
use App\Models\Oficial;
use App\Models\Oflicial;
use App\Models\OrderOficialDetail;
use App\Models\OrderService;
use App\Models\OrderServiceDetail;
use App\Models\Service;
use App\Models\WishService;
use App\Models\WishServiceDetail;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use CreateOrderOficialDetailsTable;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use League\CommonMark\Node\Block\Document;

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
        $lastOrder = OrderService::orderBy('id', 'desc')->first();
        $newOrderNumber = $lastOrder ? $lastOrder->id + 1 : 1;
        $construction_sites      = ConstructionSite::pluck('description', 'id');
        $branches               = Branch::where('status', true)->pluck('name', 'id');
        $services               = Service::pluck('description', 'id');
        $oficial                = Oficial::all();
        $const = array(config('constants'));
        $posts = $const[0]['posts'];
        return view('pages.order-service.create', compact('construction_sites' , 'branches', 'services','newOrderNumber','oficial','posts'));
    }

    public function changeStatus($id)
    {
        $orderService = OrderService::find($id);
        if ($orderService && $orderService->status == 1) {
            $orderService->status = 3;
            $orderService->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function store(CreateOrderServiceRequest $request)
    {
        if(request()->ajax())
        {
            DB::transaction(function() use ($request)
            {
                $order = OrderService::create([
                    'date_created'          =>Carbon::createFromFormat('d/m/Y', request()->date)->format('Y-m-d'),
                    'date_ending'           => request()->date_ending,
                    'user_id'               => auth()->user()->id,
                    'branch_id'             => request()->branch_id,
                    'contract_id'           =>  request()->contract_id,
                    'client_id'             => request()->client_id,
                    'constructionsite_id'   => request()->site_id ,
                    'budget_id'             => request()->budget_service_id,
                    'observation'           => request()->observation,
                    'status'                => 1
                ]);
                $dataser = request()->all();
                $detailsers = [];
                $detaoficials = [];
                foreach ($dataser['input_id'] as $key => $value) {
                    $detailsers[] = [
                        'order_id' => $order->id,
                        'input_id' => $dataser['input_id'][$key],
                        'service_id' => $dataser['service_id'][$key],
                        'input_quantity' => $dataser['quantity'][$key],
                    ];
                }
                foreach ($dataser['id_oficial'] as $key => $value) {
                    $detaoficials[] = [
                        'order_id' => $order->id,
                        'oficial_id' => $dataser['id_oficial'][$key],
                    ];
                }
                foreach ($detailsers as $detailser ) {
                    OrderServiceDetail::create([
                        'order_id'              => $detailser['order_id'],
                        'input_id'              => $detailser['input_id'],
                        'service_id'            => $detailser['service_id'],
                        'input_quantity'        => $detailser['input_quantity'],
                    ]);
                }
                foreach ($detaoficials as $detaoficial ) {
                    OrderOficialDetail::create([
                        'order_id'              => $detaoficial['order_id'],
                        'oficial_id'             => $detaoficial['oficial_id'],
                    ]);
                }
                $contract = Contracts::find($request->contract_id);
            if ($contract) {
                $contract->status = 2;
                $contract->save();
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

    public function ajax_order()
    {
    if(request()->ajax())
    {
        $results = [];
        if(request()->client_id && request()->site_id)
        {
            $contracts = Contracts::where('client_id', request()->client_id)
                                  ->where('constructionsite_id', request()->site_id)
                                  ->where('status', 1)
                                  ->get();

            foreach ($contracts as $key => $contract) {
                $results['items'][$key]['id'] = $contract->id;
                $results['items'][$key]['term'] = $contract->term;
                $results['items'][$key]['budget_service_id'] = $contract->budget_service_id;
            }
        }
        else if(request()->contract_id)
        {
            $contracts = Contracts::where('status', 1)->where('id', request()->contract_id)
                                  ->first();

                $results['id'] = $contracts->id;
                $results['term'] = $contracts->term;
                $results['budget_service_id'] = $contracts->budget_service_id;
            $budget = BudgetServiceDetail::where('budget_service_id', $results['budget_service_id'])
                ->get();
                foreach ($budget as $key => $value) {
                    $results['budget_service_detail'][$key]['id'] = $value->id;
                    $results['budget_service_detail'][$key]['budget_service_id'] = $value->budget_service_id;
                    $results['budget_service_detail'][$key]['service_id'] = $value->service_id;
                    $results['budget_service_detail'][$key]['quantity'] = $value->quantity;
                    $results['budget_service_detail'][$key]['price'] = $value->price;
                    $results['budget_service_detail'][$key]['level'] = $value->level;
                    $results['budget_service_detail'][$key]['total_price'] = $value->total_price;
                    $results['budget_service_detail'][$key]['quantity_per_meter'] = $value->quantity_per_meter;
                    $results['budget_service_detail'][$key]['input_id'] = $value->input_id;
                    $results['budget_service_detail'][$key]['input_name'] = $value->input->description;
                    $results['budget_service_detail'][$key]['service_description'] = $value->service->description;
                }

        }


        return response()->json($results);
    }
    abort(404);
    }

}
