<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

/**
 * Class Tenant
 * The core entity representing a single tenant within the SaaS ecosystem.
 * This model manages the lifecycle of tenant-specific databases and domain mappings.
 */
class Tenant extends BaseTenant implements TenantWithDatabase
{
    /**
     * Use HasDatabase for automated database provisioning per tenant.
     * Use HasDomains to allow each tenant to have one or more custom/subdomains.
     */
    use HasDatabase, HasDomains;

    /**
     * Define custom columns to be stored in the 'tenants' table within the Central Database.
     * These columns are essential for tenant identification and personalization.
     * * @return array<int, string>
     */
    public static function getCustomColumns(): array
    {
        return [
            /**
             * The primary identifier for the tenant, typically mapped to the subdomain.
             */
            'id',

            /**
             * Custom field to store the display name of the tenant's storefront.
             */
            'store_name', 
        ];
    }
}