<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\DeliveryArea;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class DeliveryLocationController extends Controller
{
    public function index()
    {
        $cities = City::with('deliveryAreas')->orderBy('name')->get();
        $defaultDeliveryFee = StoreSetting::current()->delivery_fee;

        return view('admin.delivery-locations.index', compact('cities', 'defaultDeliveryFee'));
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

        return back()->with('success', __('messages.city_added'));
    }

    public function destroyCity(City $city)
    {
        if ($city->deliveryAreas()->count() > 0) {
            return back()->with('error', __('messages.city_has_areas'));
        }

        $city->delete();

        return back()->with('success', __('messages.city_deleted'));
    }

    public function storeArea(Request $request)
    {
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'delivery_fee' => 'required|numeric|min:0',
        ]);

        $exists = DeliveryArea::where('city_id', $validated['city_id'])
            ->where('name', $validated['name'])
            ->exists();

        if ($exists) {
            return back()->with('error', __('messages.area_exists'));
        }

        DeliveryArea::create([
            'city_id' => $validated['city_id'],
            'name' => $validated['name'],
            'is_active' => true,
            'delivery_fee' => $validated['delivery_fee'],
        ]);

        return back()->with('success', __('messages.area_added'));
    }

    public function updateArea(Request $request, DeliveryArea $area)
    {
        $validated = $request->validate([
            'delivery_fee' => 'required|numeric|min:0',
        ]);

        $area->update($validated);

        return back()->with('success', __('messages.delivery_fee_updated', ['area' => $area->name]));
    }

    public function destroyArea(DeliveryArea $area)
    {
        $area->delete();

        return back()->with('success', __('messages.area_deleted'));
    }
}
