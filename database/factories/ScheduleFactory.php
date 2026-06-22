<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('now', '+7 days');
        $end   = (clone $start)->modify('+1 hour');

        return [
            'user_id'        => User::factory(),
            'user_name'      => $this->faker->name(),
            'group_name'     => 'Personal',
            'activity_name'  => $this->faker->sentence(3),
            'date'           => $start->format('Y-m-d'),
            'time'           => $start->format('H:i:s'),
            'start_datetime' => $start->format('Y-m-d H:i:s'),
            'end_datetime'   => $end->format('Y-m-d H:i:s'),
            'category'       => 'Lainnya',
            'priority'       => 'low',
            'is_completed'   => false,
        ];
    }

    public function completed(): static
    {
        return $this->state(['is_completed' => true, 'completed_at' => now()]);
    }

    public function highPriority(): static
    {
        return $this->state(['priority' => 'high']);
    }
}
