<?php

namespace App\Http\Controllers;

use App\Models\CrmCustomerType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrmCustomerTypeController extends Controller
{
    public function index(): View
    {
        return view('crm.customer-types', [
            'customerTypes' => CrmCustomerType::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:crm_customer_types,slug'],
            'color' => ['nullable', 'string', 'max:7'],
        ]);

        $data = $request->only(['name', 'slug']);
        $data['color'] = $request->filled('color')
            ? (string) $request->input('color')
            : $this->getUniqueColor();

        CrmCustomerType::create($data);

        return redirect()->back()->with('status', 'Dodano nowy typ klienta.');
    }

    public function show(int $id): JsonResponse
    {
        $type = CrmCustomerType::findOrFail($id);

        return response()->json($type);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $type = CrmCustomerType::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:crm_customer_types,slug,'.$id],
            'color' => ['required', 'string', 'max:7'],
        ]);

        $type->update($request->only(['name', 'slug', 'color']));

        return redirect()->back()->with('status', 'Zaktualizowano typ klienta.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $type = CrmCustomerType::findOrFail($id);
        $type->delete();

        return redirect()->back()->with('status', 'Usunięto typ klienta.');
    }

    private function getUniqueColor(): string
    {
        $colorPalette = [
            '#2563eb', '#3b82f6', '#10b981', '#14b8a6', '#06b6d4',
            '#8b5cf6', '#a855f7', '#ec4899', '#f43f5e', '#ef4444',
            '#f59e0b', '#f97316', '#84cc16', '#22c55e', '#6366f1',
            '#d946ef', '#f472b6', '#fb923c', '#fbbf24', '#a3e635',
        ];

        $usedColors = CrmCustomerType::pluck('color')->toArray();

        foreach ($colorPalette as $color) {
            if (! in_array($color, $usedColors, true)) {
                return $color;
            }
        }

        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }
}
