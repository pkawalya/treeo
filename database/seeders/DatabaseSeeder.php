<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Base\Registry\Models\Registry;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

	private $role;

	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		// Seed core data first
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            VendorSeeder::class,
            InventorySeeder::class,
        ]);

        		// Create or get admin role
        $adminRole = \Base\ACL\Models\Role::firstOrCreate(
            ['name' => 'ADMIN'],
            ['description' => 'Administrator role']
        );

        // Create or get user role
        $userRole = \Base\ACL\Models\Role::firstOrCreate(
            ['name' => 'USER'],
            ['description' => 'User role']
        );

        // Create or get guest role
        $guestRole = \Base\ACL\Models\Role::firstOrCreate(
            ['name' => 'GUEST'],
            ['description' => 'Guest role']
        );

		// Create or get admin permission and attach it to admin role
        $adminPermission = \Base\ACL\Models\Permission::firstOrCreate(
            ['name' => 'ADMIN_DEFAULT_BUNDLES'],
            [
                'description' => 'Default permissions for Administrator',
                'type' => 'BUNDLE',
                'content' => config('modules.base.acl.bundles')
            ]
        );
        $adminRole->permissions()->syncWithoutDetaching([$adminPermission->id]);

        // Create or get user permission and attach it to user role
        $userPermission = \Base\ACL\Models\Permission::firstOrCreate(
            ['name' => 'MANAGE_OWN_TOKEN'],
            [
                'description' => 'Default permissions for User',
                'type' => 'METHOD',
                'content' => [
                    'token.view',
                    'token.create',
                    'token.delete',
                ],
            ]
        );
        $userRole->permissions()->syncWithoutDetaching([$userPermission->id]);

        // Create or get guest permission and attach it to guest role
        $guestPermission = \Base\ACL\Models\Permission::firstOrCreate(
            ['name' => 'LAN'],
            [
                'description' => 'Local Area Network',
                'type' => 'IP',
                'content' => [
                    '10.0.0.0/8',
                    '172.16.0.0/12',
                    '192.168.0.0/16',
                ],
            ]
        );
        $guestRole->permissions()->syncWithoutDetaching([$guestPermission->id]);

		// Create or get CHANGE_USERNAME permission and attach it to admin role
        $changeUsernamePerm = \Base\ACL\Models\Permission::firstOrCreate(
            ['name' => 'CHANGE_USERNAME'],
            [
                'description' => 'Change username',
                'type' => 'TAG',
                'content' => ['change-username']
            ]
        );
        $adminRole->permissions()->syncWithoutDetaching([$changeUsernamePerm->id]);

        // Create or get CHANGE_VERIFIED_AT permission and attach it to admin role
        $changeVerifiedAtPerm = \Base\ACL\Models\Permission::firstOrCreate(
            ['name' => 'CHANGE_VERIFIED_AT'],
            [
                'description' => 'Change verified at',
                'type' => 'TAG',
                'content' => ['change-verified-at']
            ]
        );
        $adminRole->permissions()->syncWithoutDetaching([$changeVerifiedAtPerm->id]);

		// Create or get LAN permission
        $lanPermission = \Base\ACL\Models\Permission::firstOrCreate(
            ['name' => 'LAN'],
            [
                'description' => 'Local Area Network',
                'type' => 'IP',
                'content' => [
                    '10.0.0.0/8',
                    '172.16.0.0/12',
                    '192.168.0.0/16',
                ]
            ]
        );
        $adminRole->permissions()->syncWithoutDetaching([$lanPermission->id]);

		        // Run our seeders in the correct order
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            VendorSeeder::class,
            InventorySeeder::class,
            SeedlingSeeder::class,
            CommunitySeeder::class,
            FarmerSeeder::class,
            DistributionSeeder::class,
        ]);

        // Get the admin user and attach admin role
        $adminUser = \App\Models\User::where('email', 'admin@treeo.com')->first();
        if ($adminUser) {
            $adminUser->roles()->attach($adminRole);
            \Base\User\Models\Profile::factory()->create(['user_id' => $adminUser->id]);
        }

        // Create default settings if they don't exist
        $registryData = [
            ['group' => 'themes', 'keyword' => 'primary_color_scheme', 'content' => '#696969'],
            ['group' => 'themes', 'keyword' => 'danger_color_scheme', 'content' => '#e81717'],
            ['group' => 'themes', 'keyword' => 'gray_color_scheme', 'content' => '#292424'],
            ['group' => 'themes', 'keyword' => 'info_color_scheme', 'content' => '#a10da1'],
            ['group' => 'themes', 'keyword' => 'success_color_scheme', 'content' => '#0db30d'],
            ['group' => 'themes', 'keyword' => 'warning_color_scheme', 'content' => '#f0b32c'],
            ['group' => 'themes', 'keyword' => 'disable_top_navigation', 'content' => 'false'],
            ['group' => 'themes', 'keyword' => 'revealable_passwords', 'content' => 'true'],
            ['group' => 'logins', 'keyword' => 'default_register_roles', 'content' => json_encode(['GUEST'])],
            ['group' => 'logins', 'keyword' => 'enable_register', 'content' => 'true'],
            ['group' => 'logins', 'keyword' => 'enable_password_reset', 'content' => 'true'],
            ['group' => 'logins', 'keyword' => 'enable_email_verification', 'content' => 'true'],
        ];

        foreach ($registryData as $item) {
            Registry::updateOrCreate(
                ['group' => $item['group'], 'keyword' => $item['keyword']],
                $item
            );
        }

        // Only update timestamps if we didn't just create the records
        Registry::whereNull('created_at')->orWhereNull('updated_at')->update([
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
	}
}
