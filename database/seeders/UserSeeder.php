<?php
namespace Database\Seeders;

use Mark\MjdCore\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $user = new User();

        for ($i = 0; $i < 50; $i++) {
            $user->query()->insert([
                'username'   => $this->faker->userName,
                'email'      => $this->faker->unique()->safeEmail,
                'password'   => password_hash('password123', PASSWORD_BCRYPT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}