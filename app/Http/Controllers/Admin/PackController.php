<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pack;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackController extends Controller
{
    public function index()
    {
        $packs = Pack::withCount('products')->orderBy('price_per_day')->paginate(15);

        return view('admin.packs.index', compact('packs'));
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();

        return view('admin.packs.form', ['pack' => new Pack(), 'products' => $products, 'selected' => []]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['image'] = $this->handleImage($request);

        $pack = Pack::create($data);
        $pack->products()->sync($this->syncContent($request));

        return redirect()->route('admin.packs.index')->with('success', 'Pack créé avec succès.');
    }

    public function edit(Pack $pack)
    {
        $products = Product::orderBy('name')->get();
        $selected = $pack->products()->pluck('products.id', 'pack_products.quantity')->all();

        return view('admin.packs.form', compact('pack', 'products', 'selected'));
    }

    public function update(Request $request, Pack $pack)
    {
        $data = $this->validated($request);

        $newImage = $this->handleImage($request);
        if ($newImage) {
            if ($pack->image && Storage::disk('public')->exists($pack->image)) {
                Storage::disk('public')->delete($pack->image);
            }
            $data['image'] = $newImage;
        }

        $pack->update($data);
        $pack->products()->sync($this->syncContent($request));

        return redirect()->route('admin.packs.index')->with('success', 'Pack mis à jour.');
    }

    public function destroy(Pack $pack)
    {
        if ($pack->image && Storage::disk('public')->exists($pack->image)) {
            Storage::disk('public')->delete($pack->image);
        }
        $pack->delete();

        return back()->with('success', 'Pack supprimé.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'recommended_uses' => ['nullable', 'string', 'max:2000'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'deposit' => ['required', 'numeric', 'min:0'],
            'energy_capacity_wh' => ['required', 'integer', 'min:1'],
            'continuous_power_w' => ['required', 'integer', 'min:1'],
            'surge_power_w' => ['required', 'integer', 'min:1'],
            'solar_power_w' => ['nullable', 'integer', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    /** Contenu du pack : table pivot pack_products. */
    private function syncContent(Request $request): array
    {
        $content = [];
        foreach ((array) $request->input('content', []) as $productId => $qty) {
            $qty = (int) $qty;
            if ($qty > 0) {
                $content[(int) $productId] = ['quantity' => $qty];
            }
        }

        return $content;
    }

    private function handleImage(Request $request): ?string
    {
        if ($request->hasFile('image')) {
            return $request->file('image')->store('packs', 'public');
        }

        return null;
    }
}
