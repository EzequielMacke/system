<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateWishServiceRequest;
use App\Models\Branch;
use App\Models\Client;
use App\Models\ConstructionSite;
use App\Models\Service;
use App\Models\WishService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WishServiceController extends Controller
{
    public function index()
    {
        $wishservices = WishService::with('client','construction_site')
        ->orderBy('id');
        if(request()->s)
        {
            $wishservices = $wishservices->whereHas('construction_site', function($query){
                $query->where('description','LIKE', '%'. request()->s . '%');
            })->OrwhereHas('client', function($query2){
                $query2->where('razon_social','LIKE', '%'. request()->s . '%');
            });
        }

        $wishservices = $wishservices->paginate(20);
        return view('pages.wish-service.index', compact('wishservices'));
    }

    public function create()
    {
        $construction_sites     = ConstructionSite::pluck('description', 'id');
        $branches               = Branch::where('status', true)->pluck('name', 'id');
        $services               = Service::pluck('description', 'id');
        return view('pages.wish-service.create', compact('construction_sites' , 'branches', 'services'));
    }

    public function store(CreateWishServiceRequest $request)
    {
        if(request()->ajax())
        {
            DB::transaction(function() use ($request)
            {
                $last_number = WishService::orderBy('date_wish')->limit(1)->first();
                $last_number = $last_number ? $last_number->number : 0;
                $last_number = $last_number + 1;

                $wish_service = WishService::create([
                        'date_wish'             => Carbon::createFromFormat('d/m/Y', request()->date)->format('Y-m-d'),
                        'client_id'             => request()->client_id,
                        'user_id'               => auth()->user()->id,
                        'construction_site_id'  => request()->site_id,
                        'observation'           => request()->observation,
                        'branch_id'             => request()->branch_id,
                        'status'                => 1
                ]);

                // Grabar los Productos
                foreach($request->service_id as $key => $value)
                {
                    $wish_service->wish_service_detail()->create([
                        'wish_services_id'         => $wish_service->id,
                        'services_id'              => $value,
                        'quantity'                 => $request->quantity[$key],
                        'level'                    => $request->level[$key]
                    ]);
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
        $wishService = WishService::find($id);
        if ($wishService && $wishService->status != 2) {
            $wishService->status = 3;
            $wishService->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function edit($id)
    {
        $wishService = WishService::with('wish_service_detail')->findOrFail($id);
        $clients = Client::pluck('razon_social', 'id');
        $construction_sites = ConstructionSite::where('client_id', $wishService->client_id)->pluck('description', 'id');
        $services = Service::pluck('description', 'id');
        return view('pages.wish-service.edit', compact('wishService', 'clients', 'construction_sites', 'services'));
    }

    public function update(Request $request, $id)
    {
        // Validar que la obra pertenezca al cliente
        $constructionSite = ConstructionSite::where('id', $request->construction_site_id)
                                            ->where('client_id', $request->client_id)
                                            ->first();

        if (!$constructionSite) {
            return redirect()->back()->withErrors(['construction_site_id' => 'La obra seleccionada no pertenece al cliente seleccionado.']);
        }

        $wishService = WishService::findOrFail($id);
        $wishService->update($request->all());

        // Actualizar los servicios asociados
        $wishService->wish_service_detail()->delete();
        foreach ($request->service_id as $key => $value) {
            $wishService->wish_service_detail()->create([
                'wish_services_id' => $wishService->id,
                'services_id' => $value,
                'quantity' => $request->quantity[$key],
                'level' => $request->level[$key]
            ]);
        }

        return redirect()->route('wish_service')->with('success', 'Servicio actualizado correctamente.');
    }

    public function ajax_sites()
    {
        if(request()->ajax())
        {
            $results   = [];

            if(request()->client_id)
            {
                $sites = ConstructionSite::where('client_id',request()->client_id)->where('status',1)->get();
                $results                = [];
                foreach ($sites as $key => $site) {
                    $results['items'][$key]['id']       = $site->id;
                    $results['items'][$key]['name']     = $site->description;
                }
            }
            return response()->json($results);
        }
        abort(404);
    }
    public function ajax_sites2()
    {
        if(request()->ajax())
        {
            $results = [];

            if(request()->client_id)
            {
                $sites = ConstructionSite::where('client_id', request()->client_id)->where('status', 1)->get();
                foreach ($sites as $site) {
                    $results[] = ['id' => $site->id, 'description' => $site->description];
                }
            }

            return response()->json($results);
        }
        abort(404);
    }

}
