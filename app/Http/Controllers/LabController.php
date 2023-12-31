<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use App\Utilities\TimeMappings;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Mpdf\Tag\Time;

class LabController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $labs = Lab::all();

        // return view('dashboard.lab.index', compact('labs'));
        return response()->json($labs);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.lab.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:labs,name',
            'size' => 'required',
            'capacity' => 'required',
        ]);

        $validated['slug'] = Str::of($request->name)->slug('-');

        Lab::create($validated);

        // return redirect(route('labs.index'))->with('status', 'Lab berhasil ditambahkan');
        return response()->json([
            'success' => true,
            'mesagge' => 'success',
            'data' => $validated
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lab $lab)
    {
        return view('home.lab', compact('lab'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lab $lab)
    {
        return view('dashboard.lab.edit', compact('lab'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lab $lab)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'size' => 'required',
            'capacity' => 'required',
        ]);

        $validated['slug'] = Str::of($request->name)->slug('-');

        Lab::where('id', $lab->id)->update($validated);

        // return redirect(route('labs.index'))->with('status', $lab->name . ' berhasil di edit');
        return response()->json([
            'success' => true,
            'mesagge' => 'success',
            'data' => $validated
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lab $lab)
    {
        try {
            Lab::destroy($lab->id);
            // return redirect(route('labs.index'))->with('status', 'lab berhasil di hapus');
            return response()->json([
                'success' => true,
                'mesagge' => 'success',
            ]);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1451) {
                // return redirect()->back()->with('error', 'lab tidak dapat dihapus karena memiliki relasi dengan entitas lain.');
                return response()->json([
                    'success' => true,
                    'mesagge' => 'success',
                ]);
            }
            // return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus lab.');
        }
    }
}
