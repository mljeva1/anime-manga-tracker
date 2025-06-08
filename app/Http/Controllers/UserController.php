<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Services\JikanService;

class UserController extends Controller
{
    protected $jikanService;

    public function __construct(JikanService $jikanService)
    {
        $this->jikanService = $jikanService;
    }

    public function index()
    {
        // Dohvati korisnika s eager loadingom za role
        $users = User::with('role')->find(Auth::id());
        
        // Dohvati nasumične avatare s cachiranjem
        $randomAvatars = $this->getRandomAvatars();
        
        return view('user.index', compact('users', 'randomAvatars'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'name' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'name' => 'Pogrešno korisničko ime ili lozinka.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 2, // Pretpostavljamo da je 2 uloga običnog korisnika
        ]);

        Auth::login($user);

        return redirect('/user');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Provjera autorizacije
        if (Auth::id() != $id) {
            return redirect()->back()->with('error', 'Nemate dozvolu za uređivanje ovog korisnika.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:users,name,' . $id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
        ]);

        $user->name = $request->name;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Korisnički podaci uspješno ažurirani.');
    }

    public function getRandomAvatars()
    {
        // Koristimo cachiranje za smanjenje broja API poziva
        return Cache::remember('random_avatars_' . Auth::id(), 60, function() {
            $avatars = [];
            
            // Dohvati 6 avatara
            for ($i = 0; $i < 6; $i++) {
                // Dodaj pauzu između zahtjeva zbog rate limita
                if ($i > 0) {
                    usleep(250000); // 0.25 sekundi pauze
                }
                
                $character = $this->jikanService->getRandomCharacter();
                
                if ($character && isset($character['images']['webp']['image_url'])) {
                    $avatars[] = [
                        'name' => $character['name'],
                        'image' => $character['images']['webp']['image_url']
                    ];
                }
            }
            
            return $avatars;
        });
    }

    public function regenerateAvatars()
    {
        // Očisti cache za ovog korisnika
        Cache::forget('random_avatars_' . Auth::id());
        
        // Dohvati nove nasumične avatare
        $avatars = $this->getRandomAvatars();
        
        return response()->json($avatars);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|url',
        ]);

        $user = Auth::user();
        $user->avatar = $request->avatar;
        $user->save();

        return redirect()->back()->with('success', 'Avatar uspješno promijenjen!');
    }
}