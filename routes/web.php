<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IpAddressController;
use App\Http\Controllers\PlageController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\AnnuaireController;
use App\Http\Controllers\NumeroSearchController;
use App\Http\Controllers\TechnologieController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\ReserveController;
use App\Http\Controllers\MatriculeController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\OrganismeController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\NumeroController;
use App\Http\Controllers\AcheminementController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\GroupeController;
use App\Http\Controllers\PermanenceController;
use App\Http\Controllers\FaxController;
use App\Http\Controllers\AdslController;
use App\Models\User;
use App\Models\Plage;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StandardController;



// ========================
// Protected routes
// ========================
Route::middleware(['auth', 'approvedOnly'])->group(function () {

    // ========================
    // Annuaire & Related Routes
    // ========================

    Route::get('/acheminements', [AcheminementController::class, 'swd'])->name('acheminements.index');

    // Permanences
    Route::get('/permanences', [PermanenceController::class, 'index'])->name('permanences.index');
    Route::get('/permanences/this-week', [PermanenceController::class, 'thisWeek'])->name('permanences.this-week');
    Route::get('/permanences/create', [PermanenceController::class, 'create'])->name('permanences.create');
    Route::post('/permanences', [PermanenceController::class, 'store'])->name('permanences.store');
    Route::delete('/permanences/delete-precedant', [PermanenceController::class, 'deletePrecedant'])->name('permanences.delete-precedant');
    Route::get('/permanences/{permanence}', [PermanenceController::class, 'show'])->name('permanences.show');
    Route::get('/permanences/{permanence}/edit', [PermanenceController::class, 'edit'])->name('permanences.edit');
    Route::put('/permanences/{permanence}', [PermanenceController::class, 'update'])->name('permanences.update');
    Route::delete('/permanences/{permanence}', [PermanenceController::class, 'destroy'])->name('permanences.destroy');
    Route::get('/api/prochain-permanences', [PermanenceController::class, 'apiProchainPermanences'])->name('api.prochain-permanences');

    // Numero edit
    Route::get('/numeros/{numero}/edit', [NumeroController::class, 'edit'])->name('numeros.edit');

    // Services
    Route::get('/services', [ServiceController::class, 'index'])->name('numero.service');
    Route::post('/services', [ServiceController::class, 'store']);
    Route::put('/services/{service}', [ServiceController::class, 'update']);
    Route::delete('/services/{service}', [ServiceController::class, 'destroy']);

    // Numeros
    Route::get('/manageNumero', [NumeroController::class, 'manageNumero'])->name('numero.manage');
    Route::post('/manageNumero', [NumeroController::class, 'store']);
    Route::put('/manageNumero/{id}', [NumeroController::class, 'update']);
    Route::delete('/manageNumero/{id}', [NumeroController::class, 'destroy']);

    // Acheminement
    Route::get('/manageAcheminement', [AcheminementController::class, 'manageAcheminement'])->name('acheminement.manage');
    Route::post('/manageAcheminement', [AcheminementController::class, 'store']);
    Route::put('/manageAcheminement/{id}', [AcheminementController::class, 'update']);
    Route::delete('/manageAcheminement/{id}', [AcheminementController::class, 'destroy']);

    // Destination CRUD
    Route::get('/manageDestination', [DestinationController::class, 'manageDestination'])->name('destination.manage');
    Route::post('/manageDestination', [DestinationController::class, 'store'])->name('destination.store');
    Route::put('/manageDestination/{id}', [DestinationController::class, 'update'])->name('destination.update');
    Route::delete('/manageDestination/{id}', [DestinationController::class, 'destroy'])->name('destination.destroy');

    // Organismes
    Route::get('/manageOrganisme', [OrganismeController::class, 'manageOrganisme'])->name('organisme.manage');
    Route::get('/organismes', [OrganismeController::class, 'index'])->name('organismes.index');
    Route::post('/manageOrganisme', [OrganismeController::class, 'store']);
    Route::put('/manageOrganisme/{id}', [OrganismeController::class, 'update']);
    Route::delete('/manageOrganisme/{id}', [OrganismeController::class, 'destroy']);

    // Groupes
    Route::get('/manageGroupe', [GroupeController::class, 'manageGroupe'])->name('groupe.manage');
    Route::get('/groupes', [GroupeController::class, 'index'])->name('groupes.index');
    Route::post('/manageGroupe', [GroupeController::class, 'store']);
    Route::put('/manageGroupe/{id}', [GroupeController::class, 'update']);
    Route::delete('/manageGroupe/{id}', [GroupeController::class, 'destroy']);

    // Annuaire
    Route::get('/annuaire/show', [AnnuaireController::class, 'show'])->name('annuaire.show');
    Route::get('/Annuaire', [AnnuaireController::class, 'index'])->name('Annuaire.index');
    Route::get('/annuaire/filter', [AnnuaireController::class, 'filter'])->name('Annuaire.filter');
    Route::get('/annuaire/recherche', [AnnuaireController::class, 'recherche'])->name('Annuaire.recherche');

    // Notes
    Route::get('/manageNote', [NoteController::class, 'manageNote'])->name('note.manage');
    Route::post('/manageNote', [NoteController::class, 'store']);
    Route::put('/manageNote/{id}', [NoteController::class, 'update']);
    Route::delete('/manageNote/{id}', [NoteController::class, 'destroy']);

    // Matricules
    Route::get('/manageMatricule', [MatriculeController::class, 'manageMatricule'])->name('matricule.manage');
    Route::post('/manageMatricule', [MatriculeController::class, 'store']);
    Route::put('/manageMatricule/{id}', [MatriculeController::class, 'update']);
    Route::delete('/manageMatricule/{id}', [MatriculeController::class, 'destroy']);

    // Reserve
    Route::get('/manageReserve', [ReserveController::class, 'manageReserve'])->name('reserve.manage');
    Route::post('/manageReserve', [ReserveController::class, 'store']);
    Route::put('/manageReserve/{id}', [ReserveController::class, 'update']);
    Route::delete('/manageReserve/{id}', [ReserveController::class, 'destroy']);

    // Factures
    Route::get('/manageFacture', [FactureController::class, 'manageFacture'])->name('facture.manage');
    Route::post('/manageFacture', [FactureController::class, 'store']);
    Route::put('/manageFacture/{id}', [FactureController::class, 'update']);
    Route::delete('/manageFacture/{id}', [FactureController::class, 'destroy']);

    // Posts
    Route::get('/managePost', [PostController::class, 'index'])->name('Post.manage');
    Route::post('/managePost', [PostController::class, 'store']);
    Route::put('/managePost/{id}', [PostController::class, 'update']);
    Route::delete('/managePost/{id}', [PostController::class, 'destroy']);

    // Type / Classe / Technologie
    Route::get('/numero/type/manage', [TypeController::class, 'manageType'])->name('type.manage');
    Route::resource('manageType', TypeController::class)->except(['show', 'edit', 'create']);

    Route::get('/numero/classe/manage', [ClasseController::class, 'manageClasse'])->name('classe.manage');
    Route::resource('manageClasse', ClasseController::class)->except(['show', 'edit', 'create']);

    Route::get('/numero/technologies', [TechnologieController::class, 'technologies'])->name('technologies');
    Route::resource('technologies', TechnologieController::class)->except(['show', 'edit', 'create']);

    // Address
    Route::get('/ping', [NetworkController::class, 'ping'])->name('ip.ping');

    // Home
    Route::get('/home', function (Request $request) {
        return inertia('Home', [
            'users' => User::when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
                ->paginate(5)
                ->withQueryString(),
            'searchTeam' => $request->search
        ]);
    })->name('home');

    // Dashboard
    Route::get('/dashboard', function (Request $request) {
        return inertia('Dashboard', [
            'users' => User::when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
                ->paginate(5)
                ->withQueryString(),
            'searchTeam' => $request->search,
            'permanenceStats' => [
                'total' => \App\Models\Permanence::count(),
                'active' => \App\Models\Permanence::where('FSemaine', '>=', now())->count(),
                'upcoming' => \App\Models\Permanence::where('DSemaine', '>', now())->count(),
            ]
        ]);
    })->name('dashboard');

    // Plage table
Route::get('plageTable', function (Request $request) {
    return inertia('plage/PlageTable', [
        'plages' => Plage::when($request->search, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%');
        })->paginate(5)->withQueryString(),
        'searchTeam'=>$request->search
    ]);
})->name('plageTable');
// Static pages & others
Route::get('/about', [IpAddressController::class, 'index2'])->name('about');
Route::get('/Address', [IpAddressController::class, 'index'])->name('Address.index');
Route::put('/Address/{ipAddress}', [IpAddressController::class, 'update'])->name('Address.update');
Route::delete('/Address/{ipAddress}', [IpAddressController::class, 'destroy'])->name('Address.destroy');
Route::post('/addresses', [IpAddressController::class, 'store'])->name('Address.store');

// Other static pages
Route::get('/AboutOrganisme', [IpAddressController::class, 'indexOrganism'])->name('organism');
Route::get('/AboutApplication', [IpAddressController::class, 'indexApplication'])->name('application');
Route::get('/AboutDestination', [IpAddressController::class, 'indexDestination'])->name('destination');
Route::get('/plageUse', [PlageController::class, 'plageUse'])->name('plage');
Route::get('/plageNoUse', [PlageController::class, 'plageNoUse'])->name('noplage');
Route::inertia('/Unify', 'AboutUnify')->name('unify');
    // Logout
  Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
//range address 

Route::post('/plage/update-range', [PlageController::class, 'updateDirectionRange'])->name('plage.range.update');
Route::get('/plage/update-range', [PlageController::class, 'showUpdateRange'])->name('plage.range.form');
// Fax
Route::get('/faxes/statistics', [FaxController::class, 'statistics'])->name('faxes.statistics');
Route::resource('faxes', FaxController::class);

// Change tracking
Route::get('/change-tracking', [App\Http\Controllers\ChangeTrackingController::class, 'index'])->name('change-tracking.index');

// ADSL
Route::prefix('adsl')->group(function () {
    Route::get('/', [AdslController::class, 'index'])->name('adsl.index');
    Route::post('/factures', [AdslController::class, 'store'])->name('adsl.factures.create');
    Route::put('/factures/{id}', [AdslController::class, 'update'])->name('adsl.factures.update');
    Route::delete('/factures/{id}', [AdslController::class, 'destroy'])->name('adsl.factures.destroy');
    Route::get('/provider/{provider}', [AdslController::class, 'byProvider'])->name('adsl.by-provider');
    Route::get('/statistics', [AdslController::class, 'statistics'])->name('adsl.statistics');
});

//users mangement
Route::middleware(['auth', 'verified'])->group(function () {
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->name('users.destroy');

    Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])
        ->name('users.updateRole');
});


  // finsh middlware
});
// ========================
// Public routes
// ========================
    // Login
Route::inertia('/', 'Auth/Login')->name('login');
Route::post('/', [AuthController::class, 'login']);

// Register
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Pending approval page
Route::middleware('auth')->get('/pending', function () {
    $user = auth()->user();
    if ($user && $user->approved) {
        return redirect()->route('home');
    }
    return inertia('PendingApproval');
})->name('pending');


// Standard
Route::middleware(['auth', 'standard'])->group(function () {
Route::get('/manageStandard', [App\Http\Controllers\StandardController::class, 'index'])->name('standard.index');
Route::post('/manageStandard', [App\Http\Controllers\StandardController::class, 'store'])->name('standard.store');
Route::put('/manageStandard/{id}', [App\Http\Controllers\StandardController::class, 'update'])->name('standard.update');
Route::delete('/manageStandard/{id}', [App\Http\Controllers\StandardController::class, 'destroy'])->name('standard.destroy');
Route::post('/numeros/update-ndappel', [StandardController::class, 'updateNDappel']);

});

// Fallback
Route::fallback(fn() => Inertia::render('NotFound')->toResponse(request())->setStatusCode(404));
