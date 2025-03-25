<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Ce namespace est appliqué aux contrôleurs de vos routes.
     *
     * Il est également défini comme le namespace racine pour la génération d'URL.
     *
     * @var string|null
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Définir la limite de taux de vos routes.
     *
     * @var int
     */
    // protected $home = '/home';

    /**
     * Démarrage de la liaison de route.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Définir vos routes de l'application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Définir les routes web qui bénéficient du middleware "web".
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Définir les routes API qui bénéficient du middleware "api".
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
