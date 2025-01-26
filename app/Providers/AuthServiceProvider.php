<?php

namespace App\Providers;

use App\Models\master\AksesMenu;
use App\Models\Utility\Menu;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::before(function($user, $ability) {
            if ($user->isSuperadmin()) {
                return true;
            }
        });

        $menu = Menu::where('ishide', DB::raw("'0'"))->orderBy('jenis')->orderBy('urutan')->get();

        foreach($menu as $menuItem)
        {
            Gate::define(
                'view-'.$menuItem->menuid,
                function($user) use ($menuItem)
                {
                    $aksesMenu = AksesMenu::where('aksesid', DB::raw("$user->aksesid"))
                        ->where('menuid', DB::raw("$menuItem->menuid"))
                        ->where('lihat', 1)
                        ->where('dlt', 0)
                        ->first()
                    ;
                    if($aksesMenu != null)
                    {
                        return true;
                    }
                }
            );

            Gate::define(
                'add-'.$menuItem->menuid,
                function($user) use ($menuItem)
                {
                    $aksesMenu = AksesMenu::where('aksesid', DB::raw("$user->aksesid"))
                        ->where('menuid', DB::raw("$menuItem->menuid"))
                        ->where('tambah', 1)
                        ->where('dlt', 0)
                        ->first()
                    ;
                    if($aksesMenu != null)
                    {
                        return true;
                    }
                }
            );

            Gate::define(
                'edit-'.$menuItem->menuid,
                function($user) use ($menuItem)
                {
                    $aksesMenu = AksesMenu::where('aksesid', DB::raw("$user->aksesid"))
                        ->where('menuid', DB::raw("$menuItem->menuid"))
                        ->where('ubah', 1)
                        ->where('dlt', 0)
                        ->first()
                    ;
                    if($aksesMenu != null)
                    {
                        return true;
                    }
                }
            );

            Gate::define(
                'delete-'.$menuItem->menuid,
                function($user) use ($menuItem)
                {
                    $aksesMenu = AksesMenu::where('aksesid', DB::raw("$user->aksesid"))
                        ->where('menuid', DB::raw("$menuItem->menuid"))
                        ->where('hapus', 1)
                        ->where('dlt', 0)
                        ->first()
                    ;
                    if($aksesMenu != null)
                    {
                        return true;
                    }
                }
            );

            Gate::define(
                'print-'.$menuItem->menuid,
                function($user) use ($menuItem)
                {
                    $aksesMenu = AksesMenu::where('aksesid', DB::raw("$user->aksesid"))
                        ->where('menuid', DB::raw("$menuItem->menuid"))
                        ->where('cetak', 1)
                        ->where('dlt', 0)
                        ->first()
                    ;
                    if($aksesMenu != null)
                    {
                        return true;
                    }
                }
            );

        }


    }
}
