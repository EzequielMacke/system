<?php
namespace App\Http\Controllers;

use App\Models\Promotions;
use Illuminate\Http\Request;

class PromotionsController extends Controller
{
    public function index(Request $request)
    {
        $promotions = Promotions::orderBy('id');

        if ($request->s) {
            $promotions = $promotions->where('description', 'LIKE', '%' . $request->s . '%');
        }

        $promotions = $promotions->paginate(20);
        return view('pages.promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('pages.promotions.create');
    }

    public function store(Request $request)
    {
        Promotions::create([
            'description'   => $request->description,
            'status'        => $request->status,
            'user_id'       => auth()->user()->id,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            ]);


        return redirect()->route('promotions.index')->with('success', 'Promoción creada correctamente.');
    }

    public function changeStatus($id)
    {
        $promotion = Promotions::find($id);
        if ($promotion && $promotion->status == 1) {
            $promotion->status = 3;
            $promotion->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function edit($id)
    {
        $promotion = Promotions::findOrFail($id);
        return view('pages.promotions.edit', compact('promotion'));
    }
}
