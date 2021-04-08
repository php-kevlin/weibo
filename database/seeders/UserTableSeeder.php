<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::factory()->count(50)->create();
        $user = User::find(1);
        $user->name = 'admin';
        $user->email = '2426609750@qq.com';
        $user->password = bcrypt("111111");
        $user->is_admin = true;

        $user->save();

    }
}
