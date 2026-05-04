<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRole;
use App\Models\DetailSkp;
use Illuminate\Database\Seeder;
use App\Models\Tag;


class DatabaseSeeder extends Seeder {
	/**
	 * Seed the application's database.
	 */
	public function run(): void {
		$this->call( [
			UserSeeder::class,
			DetailSkpSeeder::class,
			TagSeeder::class,
			RolePermissionSeeder::class,
		] );
	}
}



