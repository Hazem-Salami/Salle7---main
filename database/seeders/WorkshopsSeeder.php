<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Seeder;

class WorkshopsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createWorkshop('abdallah',
            'alhamoi',
            'abdallah.alhamoi@gmail.com',
            '0936943559',
            '12345678',
            'Al khair',
            '33.486979', '36.339795',
            1,
            'ورشة كهرباء',
            'دمشق مزة جبل');

        $this->createWorkshop('abdallah1',
            'alhamoi1',
            'abdallah.alhamoi.1@gmail.com',
            '09369435591',
            '12345678',
            'Al khair1',
            '33.486635', '36.341513',
            1,
            'ورشة كهرباء',
            'دمشق مزة جبل');

        $this->createWorkshop('abdallah2',
            'alhamoi2',
            'abdallah.alhamoi.2@gmail.com',
            '09369435591',
            '12345678',
            'Al khair2',
            '33.489141', '36.347899',
            1,
            'ورشة كهرباء',
            'دمشق مزة جبل');

    }

    private function createWorkshop($first, $last, $email, $phone, $password, $workshop, $latitude, $longitude, $type, $description, $address): Workshop
    {
        $user = User::create([
            'firstname' => $first,
            'lastname' => $last,
            'email' => $email,
            'phone_number' => $phone,
            'user_type' => 1,
            'password' => bcrypt($password),
        ]);

        return $user->workshop()->create([
            'name' => $workshop,
            'authenticated' => 1,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'address' => $address,
            'description' => $description,
            'type' => $type
        ]);
    }
}
