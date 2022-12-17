<?php

namespace Database\Factories;

use App\Models\UserLoginRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserLoginRecord>
 */
class UserLoginRecordFactory extends Factory
{
    /**
     * 模型類別名稱
     *
     * @var string
     */
    protected $model = UserLoginRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'ip' => $this->faker->ipv4(),
            'host' => $this->faker->url(),
            'user_agent' => $this->faker->userAgent(),
            'request_time' => $this->faker->date('Y-m-d H:m:s'),
        ];
    }
}
