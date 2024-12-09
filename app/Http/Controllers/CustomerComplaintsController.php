<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderServiceRequest;
use App\Http\Requests\Request;
use App\Models\Branch;
use App\Models\BudgetService;
use App\Models\BudgetServiceDetail;
use App\Models\ConstructionSite;
use App\Models\Contracts;
use App\Models\CustomerComplaints;
use App\Models\Input;
use App\Models\InputMaterialDetails;
use App\Models\InputUsedDetails;
use App\Models\InputUseds;
use App\Models\Materials;
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
use CreateInputMaterialDetailsTable;
use CreateOrderOficialDetailsTable;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use League\CommonMark\Node\Block\Document;

class CustomerComplaintsController extends Controller
{
    public function index()
    {
        $customer_complaints = CustomerComplaints::with('client','construction_site')->orderBy('id');
        if(request()->s)
        {
            $customer_complaints = $customer_complaints->whereHas('construction_site', function($query){
                $query->where('description','LIKE', '%'. request()->s . '%');
            })->OrwhereHas('client', function($query2){
                $query2->where('razon_social','LIKE', '%'. request()->s . '%');
            });
        }

        $customer_complaints = $customer_complaints->paginate(20);
        return view('pages.customer-complaints.index', compact('customer_complaints'));
    }

    public function create()
    {
        $lascompla = CustomerComplaints::orderBy('id', 'desc')->first();
        $newCompla = $lascompla ? $lascompla->id + 1 : 1;
        $construction_sites      = ConstructionSite::pluck('description', 'id');
        $branches               = Branch::where('status', true)->pluck('name', 'id');
        $services               = Service::pluck('description', 'id');
        $oficial                = Oficial::all();
        $const = array(config('constants'));
        $posts = $const[0]['posts'];
        return view('pages.customer-complaints.create', compact('construction_sites' , 'branches', 'services','newCompla','oficial','posts'));
    }

    public function store(CreateOrderServiceRequest $request)
    {
        if(request()->ajax())
        {
            DB::transaction(function() use ($request)
            {
                CustomerComplaints::create([
                    'description'           => request()->observation,
                    'date'                  => request()->date,
                    'order_id'              => request()->order_id,
                    'client_id'             => request()->client_id,
                    'user_id'               => auth()->user()->id,
                    'constructionsite_id'   => request()->site_id ,
                    'branch_id'             => request()->branch_id,
                    'status'                => 1,
                ]);

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
    public function changeStatus($id)
    {
        $customer_complaint = CustomerComplaints::find($id);
        if ($customer_complaint && $customer_complaint->status == 1) {
            $customer_complaint->status = 2;
            $customer_complaint->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }
    public function ajax_customer()
    {
        if (request()->ajax()) {
            $results = [];

            if (request()->client_id && request()->site_id) {
                $orders = OrderService::where('client_id', request()->client_id)
                                      ->where('constructionsite_id', request()->site_id)
                                      ->get();

                foreach ($orders as $key => $order) {
                    $results['items'][$key] = [
                        'id' => $order->id,
                        'date_created' => $order->date_created,
                        'description' => $order->description,
                    ];
                }
            } else if (request()->order_id) {
                $order = OrderService::where('id', request()->order_id)->first();
                $contratos = $order->contract_id;
                $contact = Contracts::where('id', $contratos)->first();
                $budgetes = $contact->budget_service_id;
                $budget = BudgetService::where('id', $budgetes)->first();
                $wishes = $budget->wish_service_id;
                $wishDetails = WishServiceDetail::where('wish_services_id', $wishes)->get();
                $orderSerDetails = OrderServiceDetail::where('order_id', request()->order_id)->get();
                $orderOficialDetails = OrderOficialDetail::where('order_id', request()->order_id)->get();

                $results['contract_id'] = $contact->id;
                $results['budget_id'] = $budget->id;
                $results['purchase_id'] = $budget->purchase_id;

                $results['service_details'] = [];
                foreach ($orderSerDetails as $key => $orderSerDetail) {
                    $input = Input::find($orderSerDetail->input_id);
                    $service = Service::find($orderSerDetail->service_id);
                    $results['service_details'][$key] = [
                        'order_id' => $orderSerDetail->order_id,
                        'service_id' => $orderSerDetail->service_id,
                        'service_name' => $service->description,
                        'input_id' => $orderSerDetail->input_id,
                        'input_name' => $input->description,
                        'input_quantity' => $orderSerDetail->input_quantity,
                    ];
                }

                $results['oficial_details'] = [];
                foreach ($orderOficialDetails as $key => $orderOficialDetail) {
                    $oficial = Oficial::find($orderOficialDetail->oficial_id);
                    $role = config('constants.posts.' . $oficial->post);
                    $results['oficial_details'][$key] = [
                        'order_id' => $orderOficialDetail->order_id,
                        'oficial_id' => $orderOficialDetail->oficial_id,
                        'name' => $oficial->name,
                        'document' => $oficial->document_nr,
                        'role_id' => $oficial->post,
                        'role' => $role,
                    ];
                }

                $results['wish_details'] = [];
                foreach ($wishDetails as $key => $wishDetail) {
                    $services = Service::find($wishDetail->services_id);
                    $results['wish_details'][$key] = [
                        'id' => $wishDetail->id,
                        'service_id' => $wishDetail->services_id,
                        'service_name' => $services->description,
                        'quantity' => $wishDetail->quantity,
                        'level' => $wishDetail->level,
                    ];
                }

            }

            return response()->json($results);
        }
        abort(404);
    }

}
