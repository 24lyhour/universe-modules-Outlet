<?php

namespace Modules\Outlet\Http\Middleware;

use App\Services\MenuService;
use Closure;
use Illuminate\Http\Request;

class DashboardMiddlewareHandle
{
    protected static bool $registered = false;

    public function handle(Request $request, Closure $next)
    {
        if ($request->is('dashboard', 'dashboard/*')) {
            $this->registerMenuItems();
        }

        return $next($request);
    }

    protected function registerMenuItems(): void
    {
        if (static::$registered) {
            return;
        }

        MenuService::addMenuItem(
            menu: 'primary',
            id: 'outlet',
            title: __('Outlet'),
            url: route('outlet.outlets.index'),
            icon: 'Building2',
            order: 50,
            permissions: 'outlets.view_any',
            route: 'outlet.*'
        );

        MenuService::addSubmenuItem('primary', 'outlet', __('Outlets'), route('outlet.outlets.index'), 10, 'outlets.view_any', 'outlet.outlets.*', 'Building2');
        MenuService::addSubmenuItem('primary', 'outlet', __('Outlet Types'), route('outlet.outlet-types.index'), 20, 'outlet_types.view_any', 'outlet.outlet-types.*', 'LayoutGrid');

        static::$registered = true;
    }
}
