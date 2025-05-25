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
                    'type' => 'recruitment',
                    'status' => Arr::random($status),
                ]);
            }

            else{
                EventRegistration::create([
                    'event_id' => $event->id,
                    'type' => 'registration',
                    'status' => Arr::random($status),
                ]);
            }
        }
    }
}
