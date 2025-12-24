<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Standard Teardown phase to ensure environment cleanliness.
     */
    protected function tearDown(): void
    {
        // Fetch all tenants created during the test
        Tenant::all()->each(function ($tenant) {
            // Manually drop the physical database to prevent "Already Exists" errors
            $dbName = config('tenancy.database.prefix') . $tenant->id;
            DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
            
            // Delete the tenant record from central
            $tenant->delete();
        });

        parent::tearDown();
    }
}