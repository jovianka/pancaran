<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;

class EventRegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(EventSeeder::class);
        $events = Event::all();

        $status = [
            'open',
            'closed',
        ];

        foreach ($events as $event) {
            $tagName = $event->tag->name ?? null;
            if ($tagName == 'Committee'){
                EventRegistration::create([
                    'event_id' => $event->id,
                    'poster'=>'https://images.unsplash.com/photo-1584448141569-69f342da535c',
                    'start_date' => fake()->dateTimeBetween('-1 month', 'now'),
                    'end_date' => fake()->dateTimeBetween('now', '+1 month'),
                    'type' => 'recruitment',
                    'status' => Arr::random($status),
                    'start_date' => today()->format('Y-m-d'),
                    'end_date' => today()->addDay()->format('Y-m-d')
                ]);
            }

            else{
                EventRegistration::create([
                    'event_id' => $event->id,
                    'poster'=>'https://images.unsplash.com/photo-1584448141569-69f342da535c',
                    'start_date' => fake()->dateTimeBetween('-1 month', 'now'),
                    'end_date' => fake()->dateTimeBetween('now', '+1 month'),
                    'type' => 'registration',
                    'status' => Arr::random($status),
                    'start_date' => today()->format('Y-m-d'),
                    'end_date' => today()->addDay()->format('Y-m-d')
                ]);
            }
        }
    }
}
