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
            'name' => 'Sarah Council',
            'email' => 'sarah@scc.qld.gov.au',
        ]);

        $gymAdmin = User::factory()->create([
            'name' => 'Mike Trainer',
            'email' => 'mike@crossfitnoosa.com.au',
        ]);

        $schoolAdmin = User::factory()->create([
            'name' => 'Principal Jane',
            'email' => 'principal@coolumss.eq.edu.au',
        ]);

        // Create sample organisations
        $council = Organisation::factory()->verified()->create([
            'name' => 'Sunshine Coast Council',
            'email' => 'alerts@scc.qld.gov.au',
            'url' => 'https://www.sunshinecoast.qld.gov.au',
        ]);

        $gym = Organisation::factory()->verified()->create([
            'name' => 'CrossFit Noosa',
            'email' => 'info@crossfitnoosa.com.au',
        ]);

        $school = Organisation::factory()->create([
            'name' => 'Coolum State School',
            'email' => 'office@coolumss.eq.edu.au',
        ]);

        // Attach admins to their organisations
        $councilAdmin->organisations()->attach($council, ['role' => 'owner']);
        $gymAdmin->organisations()->attach($gym, ['role' => 'owner']);
        $schoolAdmin->organisations()->attach($school, ['role' => 'owner']);

        // Create subscribers
        $subscribers = Subscriber::factory(25)->verified()->create();

        // Subscribe them to organisations
        foreach ($subscribers as $subscriber) {
            $subscriber->organisations()->attach(
                collect([$council, $gym, $school])->random(rand(1, 3))
            );
        }

        // Create some notifications
        Notification::factory()->sent()->create([
            'organisation_id' => $council->id,
            'title' => 'Road Closure - David Low Way',
            'body' => 'David Low Way will be closed between Coolum and Peregian from 6am-6pm tomorrow for roadworks. Please use alternative routes via Eumundi.',
        ]);

        Notification::factory()->sent()->create([
            'organisation_id' => $council->id,
            'title' => 'Boil Water Alert - Maroochydore',
            'body' => 'Water supply in Maroochydore CBD may be contaminated. Boil water before drinking until further notice.',
            'link' => 'https://www.sunshinecoast.qld.gov.au/alerts',
        ]);

        Notification::factory()->create([
            'organisation_id' => $gym->id,
            'title' => '6am Class Cancelled Tomorrow',
            'body' => 'Coach Sarah is unwell. 6am class cancelled. 7:30am class will run as normal.',
        ]);

        Notification::factory()->scheduled()->create([
            'organisation_id' => $school->id,
            'title' => 'Athletics Carnival Postponed',
            'body' => 'Due to forecast rain, Friday\'s athletics carnival is postponed to next Tuesday. Same times apply.',
        ]);
    }
}
