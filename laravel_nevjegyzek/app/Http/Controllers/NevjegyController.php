<?php
namespace App\Http\Controllers;

use App\Models\Nevjegy;
use Illuminate\Http\Request;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Storage;

class NevjegyController extends Controller
{
    

    public function welcome(Request $request)
    {
        $kifejezes = $request->input('kifejezes', '');
        $mennyit = 9;

        $nevjegyek = Nevjegy::where('nev', 'like', "%{$kifejezes}%")
            ->orWhere('cegnev', 'like', "%{$kifejezes}%")
            ->orWhere('mobil', 'like', "%{$kifejezes}%")
            ->orWhere('email', 'like', "%{$kifejezes}%")
            ->orderBy('nev', 'asc')
            ->paginate($mennyit)
            ->withQueryString();

        return view('welcome', compact('nevjegyek', 'kifejezes'));
    }

    public function index(Request $request)
{
    $kifejezes = $request->input('kifejezes', ''); // Keresési kifejezés
    $mennyit = 9; // Lapozás mértéke

    // Névjegyek lekérése szűrés és rendezés alapján
    $nevjegyek = Nevjegy::where('nev', 'like', "%{$kifejezes}%")
        ->orWhere('cegnev', 'like', "%{$kifejezes}%")
        ->orWhere('mobil', 'like', "%{$kifejezes}%")
        ->orWhere('email', 'like', "%{$kifejezes}%")
        ->orderBy('nev', 'asc')
        ->paginate($mennyit)
        ->withQueryString();

    return view('nevjegyek.index', compact('nevjegyek', 'kifejezes'));
}


    public function create()
    {
        return view('nevjegyek.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nev' => 'required|string|max:255',
            'cegnev' => 'nullable|string|max:255',
            'mobil' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $nevjegy = new Nevjegy();
        $nevjegy->nev = $request->input('nev');
        $nevjegy->cegnev = $request->input('cegnev');
        $nevjegy->mobil = $request->input('mobil');
        $nevjegy->email = $request->input('email');

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('kepek', 'public');
            $nevjegy->foto = basename($fotoPath);
        } else {
            $nevjegy->foto = 'nincskep.png';
        }

        $nevjegy->save();

        return redirect()->route('admin.nevjegyek.index')->with('success', 'Névjegy sikeresen hozzáadva.');
    }

    public function edit($id)
    {
        $nevjegy = Nevjegy::findOrFail($id);
        return view('nevjegyek.edit', compact('nevjegy'));
    }
    

    public function update(Request $request, $id)
    {
        $request->validate([
            'nev' => 'required|string|max:255',
            'cegnev' => 'nullable|string|max:255',
            'mobil' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $nevjegy = Nevjegy::findOrFail($id);
        $nevjegy->nev = $request->input('nev');
        $nevjegy->cegnev = $request->input('cegnev');
        $nevjegy->mobil = $request->input('mobil');
        $nevjegy->email = $request->input('email');

        if ($request->hasFile('foto')) {
            if ($nevjegy->foto && $nevjegy->foto != 'nincskep.png') {
                Storage::disk('public')->delete('kepek/' . $nevjegy->foto);
            }
            $fotoPath = $request->file('foto')->store('kepek', 'public');
            $nevjegy->foto = basename($fotoPath);
        }

        $nevjegy->save();

        return redirect()->route('admin.nevjegyek.index')->with('success', 'Névjegy sikeresen frissítve.');
    }

    public function destroy($id)
    {
        $nevjegy = Nevjegy::findOrFail($id);
        if ($nevjegy->foto && $nevjegy->foto != 'nincskep.png') {
            Storage::disk('public')->delete('kepek/' . $nevjegy->foto);
        }
        $nevjegy->delete();

        return redirect()->route('admin.nevjegyek.index')->with('success', 'Névjegy törölve.');
    }

    public function jsonEndpoint()
{
    // Az összes névjegy lekérdezése
    $nevjegyek = Nevjegy::all();

    // JSON válasz küldése
    return response()->json($nevjegyek);
}

}
