<?php

namespace App\Http\Controllers;

use App\Models\CrmDeal;
use App\Models\CrmStage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CrmStageController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:crm_stages,slug'],
            'color' => ['required', 'string', 'max:20'],
            'order' => ['required', 'integer', 'min:0'],
            'is_closed' => ['nullable', 'boolean'],
        ]);

        CrmStage::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'color' => $validated['color'],
            'order' => $validated['order'],
            'is_active' => true,
            'is_closed' => (bool) ($validated['is_closed'] ?? false),
        ]);

        return redirect()->route('crm.settings')->with('status', 'Etap został dodany.');
    }

    public function edit(int $id): JsonResponse
    {
        return response()->json(CrmStage::findOrFail($id));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $stage = CrmStage::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:crm_stages,slug,'.$id],
            'color' => ['required', 'string', 'max:20'],
            'order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_closed' => ['nullable', 'boolean'],
        ]);

        $stage->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'color' => $validated['color'],
            'order' => $validated['order'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'is_closed' => (bool) ($validated['is_closed'] ?? false),
        ]);

        return redirect()->route('crm.settings')->with('status', 'Etap został zaktualizowany.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $stage = CrmStage::findOrFail($id);

        $dealExists = CrmDeal::where('stage', $stage->slug)->exists();
        if ($dealExists) {
            return redirect()->route('crm.settings')->with('status', 'Nie można usunąć etapu przypisanego do szans sprzedażowych.');
        }

        $stage->delete();

        return redirect()->route('crm.settings')->with('status', 'Etap został usunięty.');
    }
}
