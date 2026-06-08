<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\DeliveryArea;
use Illuminate\Http\Request;

class DeliveryLocationController extends Controller
{
    public function index()
    {
        $cities = City::with('deliveryAreas')->orderBy('name')->get();

        return view('admin.delivery-locations.index', compact('cities'));
    }

    public function storeCity(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cities,name',
        ]);

        City::create([
            'name' => $validated['name'],
            'is_active' => true,
        ]);

        return back()->with('success', 'City added.');
    }

    public function destroyCity(City $city)
    {
        if ($city->deliveryAreas()->count() > 0) {
            return back()->with('error', 'Cannot delete city with areas. Remove areas first.');
        }

        $city->delete();

        return back()->with('success', 'City deleted.');
    }

    public function storeArea(Request $request)
    {
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
        ]);

        $exists = DeliveryArea::where('city_id', $validated['city_id'])
            ->where('name', $validated['name'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'This area already exists for the selected city.');
        }

        DeliveryArea::create([
            'city_id' => $validated['city_id'],
            'name' => $validated['name'],
            'is_active' => true,
        ]);

        return back()->with('success', 'Area added.');
    }

    public function destroyArea(DeliveryArea $area)
    {
        $area->delete();

        return back()->with('success', 'Area deleted.');
    }
}
