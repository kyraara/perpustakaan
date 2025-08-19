<?php

namespace App\Providers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Member;
use App\Models\SchoolClass;
use App\Policies\BookPolicy;
use App\Policies\LoanPolicy;
use App\Policies\SchoolClassPolicy;
use App\Policies\MemberPolicy;
use App\Observers\LoanObserver;
use App\Policies\CategoryPolicy;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    protected $policies = [
        // Daftarkan policy Anda di sini
        Book::class => BookPolicy::class,
        Member::class => MemberPolicy::class,
        Loan::class => LoanPolicy::class,
        SchoolClass::class => SchoolClassPolicy::class,
        Category::class => CategoryPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Loan::observe(LoanObserver::class);
    }
}
