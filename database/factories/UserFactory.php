<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'role_id'           => rand(2,3),
            'name'              => $this->faker->name,
            'email'             => $this->faker->unique()->safeEmail,
            'mobile_no'         => '013'.rand(10000000,99999999),
            'district_id'       => 43,
            'upazila_id'        => rand(379,394),
            'postal_code'       => rand(4000,4500),
            'address'           => $this->faker->sentence,
            'email_verified_at' => now(),
            'password'          => 'Bb~!@#*123',    // password hashed by mutator in model
            'remember_token'    => Str::random(10),
        ];
    }
}
