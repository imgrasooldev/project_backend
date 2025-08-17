<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Interfaces\SubcategoryRepositoryInterface;
use App\Repositories\Eloquent\SubcategoryRepository;
use App\Repositories\Interfaces\ServiceProviderRepositoryInterface;
use App\Repositories\Eloquent\ServiceProviderRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Interfaces\JobPostRepositoryInterface;
use App\Repositories\Eloquent\JobPostRepository;
use App\Repositories\Interfaces\JobApplicationPostRepositoryInterface;
use App\Repositories\Eloquent\JobApplicationRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(SubcategoryRepositoryInterface::class, SubcategoryRepository::class);
        $this->app->bind(ServiceProviderRepositoryInterface::class, ServiceProviderRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(JobPostRepositoryInterface::class, JobPostRepository::class);
$this->app->bind(
    \App\Repositories\Interfaces\JobApplicationRepositoryInterface::class,
    \App\Repositories\Eloquent\JobApplicationRepository::class
);


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
