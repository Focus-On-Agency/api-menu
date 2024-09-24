<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		DB::table('users')->insert([
			'name' => 'User',
			'email' => 'user@example.com',
			'password' => Hash::make('password'),
		]);

		DB::table('users')->insert([
			'name' => 'Admin',
			'email' => 'admin@example.com',
			'password' => Hash::make('password'),
			'role' => 'admin',
		]);

		DB::table('restaurants')->insert([
			['name' => 'Napoli', 'address' => 'Via Agostino Depretis, 24'],
			['name' => 'Portici', 'address' => 'Viale Privato L. D\'Amore, 15'],
			['name' => 'Sorrento', 'address' => 'Via Rivolo S.Antonio, 13']
		]);

		DB::table('restaurants_users')->insert([
			['restaurant_id' => 1, 'user_id' => 1],

			['restaurant_id' => 1, 'user_id' => 2],
			['restaurant_id' => 2, 'user_id' => 2],
			['restaurant_id' => 3, 'user_id' => 2],
		]);

		DB::table('menus')->insert([
			['name' => 'Menu Napoli', 'icon_name' => 'chopstick'],
			['name' => 'Wine List Napoli', 'icon_name' => 'wine'],
			['name' => 'Drink List Napoli', 'icon_name' => 'drink'],

			['name' => 'Menu Portici', 'icon_name' => 'chopstick'],
			['name' => 'Wine List Portici', 'icon_name' => 'wine'],
			['name' => 'Drink List Portici', 'icon_name' => 'drink'],

			['name' => 'Menu Sorrento', 'icon_name' => 'chopstick'],
			['name' => 'Wine List Sorrento', 'icon_name' => 'wine'],
			['name' => 'Drink List Sorrento', 'icon_name' => 'drink'],
		]);

		DB::table('menu_restaurants')->insert([
			['menu_id' => 1, 'restaurant_id' => 1],
			['menu_id' => 2, 'restaurant_id' => 1],
			['menu_id' => 3, 'restaurant_id' => 1],

			['menu_id' => 4, 'restaurant_id' => 2],
			['menu_id' => 5, 'restaurant_id' => 2],
			['menu_id' => 6, 'restaurant_id' => 2],

			['menu_id' => 7, 'restaurant_id' => 3],
			['menu_id' => 8, 'restaurant_id' => 3],
			['menu_id' => 9, 'restaurant_id' => 3],
		]);
	}
}
