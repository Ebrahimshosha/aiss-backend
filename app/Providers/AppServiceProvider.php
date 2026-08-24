<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Article;
use App\Models\Magazine;
use App\Models\Booklet;
use App\Models\CodeStandard;
use App\Models\Conference;
use App\Models\Competition;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-content', function (User $user) {
            return $user->can_add_article;
        });

        Relation::enforceMorphMap([
            'article' => Article::class,
            'magazine' => Magazine::class,
            'booklet' => Booklet::class,
            'code_standard' => CodeStandard::class,
            'conference' => Conference::class,
            'competition' => Competition::class,
        ]);
    }
}
