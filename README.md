Technical Documentation: Multi-Tenant SaaS Infrastructure
Architecture Pattern: Multi-Database Isolation (Isolated Schema)
 
1. High-Level Architecture
This platform utilizes a Database-per-Tenant approach. Each client (tenant) is provisioned with a physically separate database. This strategy ensures maximum data privacy, simplifies individual backups, and allows for custom schema scaling per client.
Core Infrastructure Components:
•	Central/Landlord Store: The primary database responsible for managing the tenant lifecycle, domain mappings, and global system configurations.
•	Tenant Instances: Dynamic databases generated automatically for every new organization.
•	Context Resolver: Middleware that detects tenant identity based on the incoming request's hostname (Subdomain).
 
2. Technical Stack
Layer	Component	Description
Backend	Laravel 11	Robust PHP Framework for enterprise logic.
Frontend	Vue 3 & Inertia.js	Reactive UI providing a Single Page Application (SPA) experience.
Database	MySQL / MariaDB	Relational Database Management System.
Tenancy	Stancl/Tenancy	The engine responsible for dynamic DB switching & migrations.
UI Design	Glassmorphism	Modern, premium interface with translucent aesthetic.
 

3. PERFORMANCE: TENANT-AWARE CACHING
To achieve sub-millisecond latency and minimize SQL overhead, the system utilizes a centralized Redis server managed via the Predis client.
3.1 Redis Infrastructure
•	Driver: Redis (v7.x recommended) via Predis library.
•	Tenant Separation: Cache isolation is maintained through Dynamic Key Prefixing.
•	Logic: Upon tenant initialization, the application dynamically updates the cache.prefix configuration.
o	Prefix Format: tenant_{tenant_id}:
o	Result: This ensures Tenant A cannot access or overwrite the cached data of Tenant B in the shared Redis memory.
3.2 Intelligent Data Caching
•	Query Caching: Frequently accessed resources (Products, Settings) are stored in RAM.
•	Rate Limiting: Redis handles per-tenant request throttling to prevent API abuse and ensure system stability.
 
4. SECURITY HARDENING & DATA PROTECTION
Security is implemented as a "Defense in Depth" strategy across all tenant databases.
4.1 Protection Against SQL Injection
The system utilizes the Laravel Eloquent ORM and Query Builder, which natively use PDO Parameter Binding. This ensures that raw input is never directly concatenated into SQL queries.
4.2 Advanced Input Validation
Strict validation rules are applied at the Request layer before data reaches the controller.
•	Type Hinting: Ensuring numeric inputs for prices and stock.
•	Sanitization: Stripping malicious HTML/scripts to prevent XSS (Cross-Site Scripting).
4.3 At-Rest Data Encryption
Sensitive tenant information (such as API keys or private contact details) is encrypted using AES-256-GCM.
•	Mechanism: Encrypted using the Crypt facade with the system's unique APP_KEY.
•	Isolation: While the logic is central, the encrypted blobs are stored within the isolated tenant databases.


5. Data & Resource Isolation
A. Database Isolation
Upon receiving an HTTP request, the system identifies the subdomain. If valid, the system terminates the central database connection and hot-swaps it with the specific tenant database connection.
B. Storage & Asset Isolation
To prevent cross-tenant asset access, each tenant is assigned a dedicated root directory within the storage path:
•	Directory Path: storage/app/public/tenant_[id]/
•	Asset URL: http://[subdomain].localhost/storage/[id]/...
 
6. Key Logic & Workflows
🚀 Tenant Provisioning (Deployment)
1.	Admin enters the organization name and desired subdomain in the Central Dashboard.
2.	The system validates subdomain availability against the tenants table.
3.	Database Creation: An automated CREATE DATABASE command is executed.
4.	Auto-Migration: The entire tenant schema (products, orders, users) is migrated to the new database.
5.	Domain Binding: The subdomain is linked to the Tenant ID for future request resolution.
🛡️ Cross-Tenant Security
Authentication is strictly scoped to the tenant's database. A user registered in Store A does not exist in the database of Store B, eliminating any possibility of session hijacking or unauthorized cross-tenant access.
 
7. Quality Assurance & Testing
The infrastructure is guarded by an automated Feature Test Suite covering:
•	Data Integrity: Validating mandatory fields like store_name during provisioning.
•	Database Isolation: Ensuring Product::all() only retrieves records for the active tenant.
•	Teardown Protocol: Utilizing a custom tearDown method to drop physical databases after test cycles to maintain environment cleanliness.
 
8. Maintenance Commands
Command	Utility
php artisan tenants:migrate	Runs migrations across all existing tenant databases.
php artisan tenants:list	Displays active tenants and their database connection status.
php artisan test --filter=TenantDataIsolationTest	Executes the cross-tenant security and isolation validation.
 
Developer Note: > Always use http://[subdomain].localhost:8000 during local development to ensure the Tenancy middleware can correctly resolve the context.

<img width="468" height="639" alt="image" src="https://github.com/user-attachments/assets/b4f3bb43-6569-450e-9cc9-80415e5e9e2d" />
