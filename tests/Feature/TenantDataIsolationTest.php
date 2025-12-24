<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Class TenantDataIsolationTest
 * Validates strict data isolation using a manual database teardown strategy.
 */
class TenantDataIsolationTest extends TestCase
{
    /**
     * RefreshDatabase handles the Central DB.
     * The custom tearDown in TestCase.php handles physical Tenant DB cleanup.
     */
    use RefreshDatabase;

    public function test_products_are_isolated_in_separate_databases()
    {
        // Provisioning instances with unique IDs to further prevent collision
        $tenantA = Tenant::create(['id' => 'alpha', 'store_name' => 'Alpha Store']);
        $tenantA->domains()->create(['domain' => 'alpha.localhost']);

        $tenantB = Tenant::create(['id' => 'beta', 'store_name' => 'Beta Store']);
        $tenantB->domains()->create(['domain' => 'beta.localhost']);

        $tenantA->run(function () {
            Product::create(['name' => 'Alpha Item', 'price' => 100, 'stock' => 10]);
        });

        $tenantB->run(function () {
            Product::create(['name' => 'Beta Item', 'price' => 200, 'stock' => 5]);
        });

        $tenantA->run(function () {
            $this->assertEquals(1, Product::count());
            $this->assertDatabaseHas('products', ['name' => 'Alpha Item']);
        });

        $tenantB->run(function () {
            $this->assertEquals(1, Product::count());
            $this->assertDatabaseHas('products', ['name' => 'Beta Item']);
        });
    }

    public function test_user_cannot_login_to_another_tenant_store()
    {
        $tenantA = Tenant::create(['id' => 'user-a', 'store_name' => 'Store A']);
        $tenantA->domains()->create(['domain' => 'user-a.localhost']);

        $tenantB = Tenant::create(['id' => 'user-b', 'store_name' => 'Store B']);
        $tenantB->domains()->create(['domain' => 'user-b.localhost']);

        $tenantA->run(function () {
            User::create([
                'name' => 'User Alpha',
                'email' => 'test@example.com',
                'password' => Hash::make('secret'),
                'role' => 'customer'
            ]);
        });

        // Request dispatched to Tenant B domain
        $response = $this->post('http://user-b.localhost:8000/login', [
            'email' => 'test@example.com',
            'password' => 'secret',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertFalse(auth()->check());
    }
}