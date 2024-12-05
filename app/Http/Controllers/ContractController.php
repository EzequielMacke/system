<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateContractsRequest;
use App\Models\Branch;
use App\Models\BudgetService;
use App\Models\BudgetServiceDetail;
use App\Models\Clauses;
use App\Models\ConstructionSite;
use App\Models\ContractDetails;
use App\Models\Contracts;
use App\Models\Input;
use App\Models\Obligations;
use App\Models\Service;
use App\Models\WishService;
use App\Models\WishServiceDetail;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Contracts::with('client','construction_site')->orderBy('id');
        if(request()->s)
        {
            $contracts = $contracts->whereHas('construction_site', function($query){
                $query->where('description','LIKE', '%'. request()->s . '%');
            })->OrwhereHas('client', function($query2){
                $query2->where('razon_social','LIKE', '%'. request()->s . '%');
            });
        }

        $contracts = $contracts->paginate(20);
        return view('pages.contract.index', compact('contracts'));
    }

    public function create()
    {
        $construction_sites      = ConstructionSite::pluck('description', 'id');
        $branches               = Branch::where('status', true)->pluck('name', 'id');
        $services               = Service::pluck('description', 'id');
        return view('pages.contract.create', compact('construction_sites' , 'branches', 'services'));
    }

    public function store(CreateContractsRequest $request)
    {
        if(request()->ajax())
        {
            DB::transaction(function() use ($request)
            {

                $contract = Contracts::create([
                        'description'           => request()->observation,
                        'date_created'          => Carbon::now(),
                        'date_signed'           =>null,
                        'constructionsite_id'   => request()->site_id,
                        'term'                  => request()->term,
                        'budget_service_id'     => request()->budget_id,
                        'client_id'             => request()->client_id,
                        'user_id'               => auth()->user()->id,
                        'placement'             => request()->placement,
                        'issue'                => request()->issue,
                        'status'                => 1
                ]);
                $contracte = request()->all();
                $detailsoblis = [];
                foreach ($contracte['service_id-obli'] as $key => $service_id) {
                    $obligation_id = $contracte['id-obli'][$key] ?? null;

                    // Si ambos son nulos, termina el array
                    if (is_null($obligation_id)) {
                        break;
                    }

                    $detailsoblis[] = [
                        'service_id' => intval($service_id),
                        'obligation_id' => intval($obligation_id),
                    ];
                }

                // Iterar sobre el array y crear los detalles del contrato
                foreach ($detailsoblis as $detailsobli) {
                    ContractDetails::create([
                        'contract_id'   => $contract->id,
                        'service_id'    => $detailsobli['service_id'],
                        'obligation_id' => $detailsobli['obligation_id'],
                    ]);
                }
                $detailsclaus = [];
                foreach ($contracte['service_id-clau'] as $key => $service_id) {
                    $clause_id = $contracte['id-clau'][$key] ?? null;

                    // Si ambos son nulos, termina el array
                    if (is_null($clause_id)) {
                        break;
                    }

                    $detailsclaus[] = [
                        'service_id' => intval($service_id),
                        'clause_id' => intval($clause_id),
                    ];
                }

                // Iterar sobre el array y crear los detalles del contrato
                foreach ($detailsclaus as $detailsclau) {
                    ContractDetails::create([
                        'contract_id'   => $contract->id,
                        'service_id'    => $detailsclau['service_id'],
                        'clause_id'     => $detailsclau['clause_id'],
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

    public function ajax_contract()
    {
        if(request()->ajax())
        {
            $results   = [];
            if(request()->client_id && request()->site_id )
            {
                $sites = BudgetService::where('client_id',request()->client_id)->where('constructionsite_id',request()->site_id)->where('status',1)->get();
                foreach ($sites as $key => $site) {
                    $results['items'][$key]['id']               = $site->id;
                    $results['items'][$key]['date_budget']      = $site->id.' - '.Carbon::createFromFormat('Y-m-d',$site->date_budgets)->format('d/m/Y');
                    $results['items'][$key]['description']      = $site->description;

                }
            }
            else if (request()->budget_id) {
                $sites = BudgetServiceDetail::where('budget_service_id', request()->budget_id)->get();
                foreach ($sites as $key => $site) {
                    $results['items'][$key]['id'] = $site->id;
                    $results['items'][$key]['service_id'] = $site->service_id;
                    $results['items'][$key]['service_name'] = $site->service->description;
                    $results['items'][$key]['quantity'] = $site->quantity;
                    $results['items'][$key]['level'] = $site->level;
                    $results['items'][$key]['description'] = '';

                    // Recuperar cláusulas y obligaciones
                    $clauses = Clauses::where('type_id', $site->service_id)->get();
                    $obligations = Obligations::where('type_id', $site->service_id)->get();

                    // Agregar cláusulas al resultado
                    foreach ($clauses as $clauseKey => $clause) {
                        $results['items'][$key]['clauses'][$clauseKey]['id'] = $clause->id;
                        $results['items'][$key]['clauses'][$clauseKey]['description'] = $clause->description;
                        $results['items'][$key]['clauses'][$clauseKey]['service_id'] = $site->service_id;
                    }

                    // Agregar obligaciones al resultado
                    foreach ($obligations as $obligationKey => $obligation) {
                        $results['items'][$key]['obligations'][$obligationKey]['id'] = $obligation->id;
                        $results['items'][$key]['obligations'][$obligationKey]['name'] = $obligation->name;
                        $results['items'][$key]['obligations'][$obligationKey]['service_id'] = $site->service_id;
                    }
                }

                // Extraer cláusulas y obligaciones únicas
                $clauses = [];
                $obligations = [];
                foreach ($results['items'] as $item) {
                    if (isset($item['clauses'])) {
                        foreach ($item['clauses'] as $clause) {
                            $clauses[] = $clause;
                        }
                    }
                    if (isset($item['obligations'])) {
                        foreach ($item['obligations'] as $obligation) {
                            $obligations[] = $obligation;
                        }
                    }
                }

                $clauniqueArray = [];
                foreach ($clauses as $item) {
                    $clauniqueArray[$item['id']] = $item;
                }
                $clauniqueArray = array_values($clauniqueArray);

                $obliuniqueArray = [];
                foreach ($obligations as $item) {
                    $obliuniqueArray[$item['id']] = $item;
                }
                $obliuniqueArray = array_values($obliuniqueArray);

                // Agregar los arrays únicos a la respuesta
                $results['clauniqueArray'] = $clauniqueArray;
                $results['obliuniqueArray'] = $obliuniqueArray;
                return response()->json($results);
            }
            else if(request()->client_id && request()->site_id && request()->type == 'presupuesto' )
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
