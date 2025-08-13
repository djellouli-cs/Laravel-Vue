<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Numero;
use App\Models\Fax;
use App\Models\Organisme;
use App\Models\Destination;
use App\Models\Classe;
use App\Models\Type;
use App\Models\Reserve;
use App\Models\Technologie;
use App\Models\Facture;
use App\Models\Matricule;
use App\Models\Note;
use App\Models\Post;
use App\Models\Acheminement;
use App\Models\Service;
use App\Models\User;

class ChangeTrackingController extends Controller
{
    /**
     * Show the change tracking dashboard
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $table = $request->get('table', 'all');
        $user = $request->get('user', 'all');
        $action = $request->get('action', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $changes = $this->getRecentChanges($table, $user, $action, $perPage, $startDate, $endDate);

        return Inertia::render('ChangeTracking/Index', [
            'changes' => $changes,
            'filters' => [
                'table' => $table,
                'user' => $user,
                'action' => $action,
                'per_page' => $perPage,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'stats' => $this->getChangeStats(),
            'users' => User::select('id', 'name')->get(),
            'tables' => [
                'numeros' => 'Numéros',
                'faxes' => 'Faxes',
                'organismes' => 'Organismes',
                'destinations' => 'Destinations',
                'classes' => 'Classes',
                'types' => 'Types',
                'reserves' => 'Réserves',
                'technologies' => 'Technologies',
                'factures' => 'Factures',
                'matricules' => 'Matricules',
                'notes' => 'Notes',
                'posts' => 'Posts',
                'acheminements' => 'Acheminements',
                'services' => 'Services',
            ],
        ]);
    }

    /**
     * Get recent changes from all tables
     */
    private function getRecentChanges($table = 'all', $user = 'all', $action = 'all', $perPage = 20, $startDate = null, $endDate = null)
    {
        $changes = collect();

        // Define all models and their table names
        $models = [
            'numeros' => Numero::class,
            'faxes' => Fax::class,
            'organismes' => Organisme::class,
            'destinations' => Destination::class,
            'classes' => Classe::class,
            'types' => Type::class,
            'reserves' => Reserve::class,
            'technologies' => Technologie::class,
            'factures' => Facture::class,
            'matricules' => Matricule::class,
            'notes' => Note::class,
            'posts' => Post::class,
            'acheminements' => Acheminement::class,
            'services' => Service::class,
        ];

        foreach ($models as $tableName => $modelClass) {
            if ($table !== 'all' && $table !== $tableName) {
                continue;
            }

            $query = $modelClass::with('user')
                ->orderBy('updated_at', 'desc')
                ->limit($perPage);

            if ($user !== 'all') {
                $query->where('user_id', $user);
            }

            // Date range filtering
            if ($startDate) {
                $query->whereDate('updated_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('updated_at', '<=', $endDate);
            }

            $records = $query->get();

            foreach ($records as $record) {
                $changes->push([
                    'id' => $record->id,
                    'table' => $tableName,
                    'table_name' => $this->getTableDisplayName($tableName),
                    'record_name' => $this->getRecordDisplayName($record, $tableName),
                    'action' => $record->created_at == $record->updated_at ? 'created' : 'updated',
                    'user' => $record->user ? [
                        'id' => $record->user->id,
                        'name' => $record->user->name,
                    ] : null,
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                    'details' => $this->getRecordDetails($record, $tableName),
                ]);
            }
        }

        // Sort by updated_at descending and paginate
        $changes = $changes->sortByDesc('updated_at');
        
        if ($action !== 'all') {
            $changes = $changes->filter(function ($change) use ($action) {
                return $change['action'] === $action;
            });
        }

        return $changes->take($perPage);
    }

    /**
     * Get change statistics
     */
    private function getChangeStats()
    {
        $stats = [];

        $models = [
            'numeros' => Numero::class,
            'faxes' => Fax::class,
            'organismes' => Organisme::class,
            'destinations' => Destination::class,
            'classes' => Classe::class,
            'types' => Type::class,
            'reserves' => Reserve::class,
            'technologies' => Technologie::class,
            'factures' => Facture::class,
            'matricules' => Matricule::class,
            'notes' => Note::class,
            'posts' => Post::class,
            'acheminements' => Acheminement::class,
            'services' => Service::class,
        ];

        foreach ($models as $tableName => $modelClass) {
            $stats[$tableName] = [
                'total' => $modelClass::count(),
                'today' => $modelClass::whereDate('updated_at', today())->count(),
                'this_week' => $modelClass::whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'this_month' => $modelClass::whereMonth('updated_at', now()->month)->count(),
            ];
        }

        return $stats;
    }

    /**
     * Get table display name
     */
    private function getTableDisplayName($tableName)
    {
        $names = [
            'numeros' => 'Numéros',
            'faxes' => 'Faxes',
            'organismes' => 'Organismes',
            'destinations' => 'Destinations',
            'classes' => 'Classes',
            'types' => 'Types',
            'reserves' => 'Réserves',
            'technologies' => 'Technologies',
            'factures' => 'Factures',
            'matricules' => 'Matricules',
            'notes' => 'Notes',
            'posts' => 'Posts',
            'acheminements' => 'Acheminements',
            'services' => 'Services',
        ];

        return $names[$tableName] ?? ucfirst($tableName);
    }

    /**
     * Get record display name
     */
    private function getRecordDisplayName($record, $tableName)
    {
        switch ($tableName) {
            case 'numeros':
                return $record->NDappel ?? 'Numéro #' . $record->id;
            case 'faxes':
                return $record->NDappel ?? 'Fax #' . $record->id;
            case 'organismes':
                return $record->name ?? 'Organisme #' . $record->id;
            case 'destinations':
                return $record->name ?? 'Destination #' . $record->id;
            case 'classes':
                return $record->classe ?? 'Classe #' . $record->id;
            case 'types':
                return $record->name ?? 'Type #' . $record->id;
            case 'reserves':
                return $record->reserve ?? 'Réserve #' . $record->id;
            case 'technologies':
                return $record->name ?? 'Technologie #' . $record->id;
            case 'factures':
                return $record->facture ?? 'Facture #' . $record->id;
            case 'matricules':
                return $record->matricule ?? 'Matricule #' . $record->id;
            case 'notes':
                return substr($record->content ?? '', 0, 50) . '...' ?? 'Note #' . $record->id;
            case 'posts':
                return $record->post ?? 'Post #' . $record->id;
            case 'acheminements':
                return $record->acheminement ?? 'Acheminement #' . $record->id;
            case 'services':
                return $record->name ?? 'Service #' . $record->id;
            default:
                return 'Record #' . $record->id;
        }
    }

    /**
     * Get record details for display
     */
    private function getRecordDetails($record, $tableName)
    {
        $details = [];

        switch ($tableName) {
            case 'numeros':
                $details = [
                    'NDappel' => $record->NDappel,
                    'Position' => $record->Position,
                    'Organisme' => $record->organisme?->name,
                    'Destination' => $record->destination?->name,
                ];
                break;
            case 'faxes':
                $details = [
                    'NDappel' => $record->NDappel,
                    'Description' => $record->description,
                ];
                break;
            case 'organismes':
                $details = [
                    'Name' => $record->name,
                    'Name FR' => $record->name_fr,
                ];
                break;
            case 'destinations':
                $details = [
                    'Name' => $record->name,
                    'Name FR' => $record->name_fr,
                    'Organisme' => $record->organisme?->name,
                ];
                break;
            default:
                // Get all fillable fields except user_id
                $fillable = $record->getFillable();
                foreach ($fillable as $field) {
                    if ($field !== 'user_id') {
                        $details[$field] = $record->$field;
                    }
                }
        }

        return array_filter($details, function ($value) {
            return $value !== null && $value !== '';
        });
    }
}
