<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'user.roles' => $request->user() ? $request->user()->roles->pluck('name') : [],
            'user.permissions' => $request->user() ? $request->user()->getPermissionsViaRoles()->pluck('name') : [],
        
            'navigation' => [
                [
                    'name' => 'Panel Principal',
                    'route' => 'dashboard',
                    'icon' => 'fas fa-th-large',
                    'show' => true,
                ],
                [
                    'name' => 'Productos',
                    'route' => 'products.index', // Asegúrate que esta ruta exista en web.php
                    'icon' => 'fas fa-pills',
                    'show' => $request->user()?->can('Leer productos'),
                ],
                [
                    'name' => 'Categorías',
                    'route' => 'categories.index',
                    'icon' => 'fas fa-tags',
                    'show' => $request->user()?->can('Leer categorías'),
                ],
                [
                    'name' => 'Usuarios y Roles',
                    'route' => 'roles.index',
                    'icon' => 'fas fa-users-cog',
                    'show' => $request->user()?->hasRole('admin'),
                ],
            ]
            ];
    }
}
