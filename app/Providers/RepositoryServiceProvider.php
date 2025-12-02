<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\AuthorRepositoryInterface;
use App\Repositories\AuthorRepository;
use App\Interfaces\BookRepositoryInterface; 
use App\Repositories\BookRepository;
use App\Interfaces\BorrowRecordRepositoryInterface;
use App\Repositories\BorrowRecordRepository;
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AuthorRepositoryInterface::class , AuthorRepository::class);
        $this->app->bind(BookRepositoryInterface::class , BookRepository::class);
        $this->app->bind(BorrowRecordRepositoryInterface::class , BorrowRecordRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
