<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\Organisation;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Super Admin (you)
        $superAdmin = User::factory()->create([
            'name' => 'Josh (Super Admin)',
            'email' => 'josh@notifi.test',
            'is_super_admin' => true,
        ]);

        // Create Org Admin users
        $councilAdmin = User::factory()->create([
            'name' => 'Sarah Williams',
            'email' => 'sarah@tauranga.govt.nz',
        ]);

        $cafeAdmin = User::factory()->create([
            'name' => 'Mike Chen',
            'email' => 'mike@mountlifeguards.co.nz',
        ]);

        $schoolAdmin = User::factory()->create([
            'name' => 'Principal Jane',
            'email' => 'principal@papamoa.school.nz',
        ]);

        // Create sample organisations (NZ based)
        $council = Organisation::factory()->verified()->create([
            'name' => 'Tauranga City Council',
            'email' => 'alerts@tauranga.govt.nz',
            'url' => 'https://www.tauranga.govt.nz',
            'latitude' => -37.6870,
            'longitude' => 176.1654,
            'timezone' => 'Pacific/Auckland',
        ]);

        $cafe = Organisation::factory()->verified()->create([
            'name' => 'Mount Maunganui Surf Lifesaving Club',
            'email' => 'info@mountlifeguards.co.nz',
            'url' => 'https://www.mountlifeguards.co.nz',
            'latitude' => -37.6324,
            'longitude' => 176.1785,
            'timezone' => 'Pacific/Auckland',
        ]);

        $school = Organisation::factory()->create([
            'name' => 'Papamoa Beach School',
            'email' => 'office@papamoa.school.nz',
            'url' => 'https://www.papamoa.school.nz',
            'latitude' => -37.7195,
            'longitude' => 176.2983,
            'timezone' => 'Pacific/Auckland',
        ]);

        // Attach admins to their organisations
        $councilAdmin->organisations()->attach($council, ['role' => 'owner']);
        $cafeAdmin->organisations()->attach($cafe, ['role' => 'owner']);
        $schoolAdmin->organisations()->attach($school, ['role' => 'owner']);

        // Create subscribers with NZ phone numbers
        $subscribers = Subscriber::factory(25)->verified()->create();

        // Subscribe them to organisations
        foreach ($subscribers as $subscriber) {
            $subscriber->organisations()->attach(
                collect([$council, $cafe, $school])->random(rand(1, 3))
            );
        }

        // Create some notifications
        Notification::factory()->sent()->create([
            'organisation_id' => $council->id,
            'title' => 'Road Closure - Marine Parade',
            'body' => 'Marine Parade will be closed between Salisbury Ave and Adams Ave from 7am-5pm tomorrow for pipe repairs. Use Maunganui Road instead.',
        ]);

        Notification::factory()->sent()->create([
            'organisation_id' => $council->id,
            'title' => 'Beach Warning - Rip Currents',
            'body' => 'Strong rip currents at Mount Main Beach today. Swim between the flags only. Lifeguards on patrol until 6pm.',
            'link' => 'https://www.tauranga.govt.nz/council/council-news-and-information',
        ]);

        Notification::factory()->create([
            'organisation_id' => $cafe->id,
            'title' => 'Closed for Private Event',
            'body' => 'The club will be closed to the public Saturday evening for a wedding reception. Normal hours resume Sunday 7am.',
        ]);

        Notification::factory()->scheduled()->create([
            'organisation_id' => $school->id,
            'title' => 'School Gala Day Reminder',
            'body' => 'Don\'t forget our annual gala is this Saturday from 10am! Bring the family for food, games, and fun. Cash and EFTPOS available.',
        ]);
    }
}
