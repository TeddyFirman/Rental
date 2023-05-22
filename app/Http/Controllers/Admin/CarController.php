<?php

namespace App\Http\Controllers\Admin;

use App\Models\Car;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CarStoreRequest;
use App\Http\Requests\Admin\CarUpdateRequest;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cars = Car::latest()->get();
        return view('admin.cars.index', compact('cars'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.cars.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CarStoreRequest $request)
    {
        if ($request->validated()) {
            $gambar = $request->file('gambar')->store('assets/car', 'public');
            $slug = Str::slug($request->nama_mobil, '-');

            Car::create($request->except('gambar') + ['gambar' => $gambar, 'slug' => $slug]);
        }

        return redirect()->route('admin.cars.index')->with([
            'message' => 'Sukses menambahkan data!',
            'alert-type' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Car $car)
    {
        return view('admin.cars.edit', compact('car'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CarUpdateRequest $request, Car $car)
    {
        if ($request->validated()) {
            $slug = Str::slug($request->nama_mobil, '-');
            $car->update($request->validated() + ['slug' => $slug]);
        }

        return redirect()->route('admin.cars.index')->with([
            'message' => 'Sukses mengupdate data!',
            'alert-type' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Car $car)
    {
        if($car->gambar){
            unlink('storage/' . $car->gambar);
        }
        $car->delete();

        return redirect()->back()->with([
            'message' => 'Data berhasil dihapus!',
            'alert-type' => 'success'
        ]);
    }

    public function updateImage(Request $request,$carId)
    {
        $request->validate([
            'gambar' => 'required|image'
        ]);
        $car = Car::findOrFail($carId);
        if ($request->gambar) {
            unlink('storage/' . $car->gambar);
            $gambar = $request->file('gambar')->store('assets/car', 'public');

            $car->update(['gambar' => $gambar]);
        }

        return redirect()->back()->with([
            'message' => 'Gambar berhasil diupdate!',
            'alert-type' => 'info'
        ]);
    }
}
