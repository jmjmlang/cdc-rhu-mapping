<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admins (3) ────────────────────────────────────────────────────────

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'           => 'Admin',
                'password'       => bcrypt('admin1234'),
                'role'           => 'admin',
                'org_role'       => 'Municipal Health Officer',
                'account_status' => 'approved',
                'gender'         => 'male',
                'birthdate'      => '1978-03-10',
                'age'            => 47,
            ]
        );

        User::updateOrCreate(
            ['email' => 'maricel.santos@gmail.com'],
            [
                'name'           => 'Maricel Santos',
                'password'       => bcrypt('admin1234'),
                'role'           => 'admin',
                'org_role'       => 'Public Health Nurse',
                'account_status' => 'approved',
                'gender'         => 'female',
                'birthdate'      => '1985-07-22',
                'age'            => 40,
            ]
        );

        User::updateOrCreate(
            ['email' => 'renato.cruz@gmail.com'],
            [
                'name'           => 'Renato Cruz',
                'password'       => bcrypt('admin1234'),
                'role'           => 'admin',
                'org_role'       => 'Sanitation Inspector',
                'account_status' => 'approved',
                'gender'         => 'male',
                'birthdate'      => '1980-11-05',
                'age'            => 44,
            ]
        );

        // ── RHU Officer ──────────────────────────────────────────────────────

        User::updateOrCreate(
            ['email' => 'rhu@gmail.com'],
            [
                'name'           => 'RHU Officer',
                'password'       => bcrypt('rhu1234'),
                'role'           => 'rhu',
                'org_role'       => 'Rural Health Midwife',
                'account_status' => 'approved',
                'gender'         => 'female',
                'birthdate'      => '1985-06-15',
                'age'            => 40,
            ]
        );

        // ── Citizens (30) ─────────────────────────────────────────────────────

        $citizens = [
            // Dev autofill accounts (primary test users)
            ['email' => 'johnmichael.talbo@gmail.com',  'name' => 'John Michael Talbo',    'gender' => 'male',   'birthdate' => '2000-03-15', 'age' => 26],
            ['email' => 'engiemar.balanay@gmail.com',   'name' => 'Engiemar Balanay',      'gender' => 'male',   'birthdate' => '2000-07-22', 'age' => 25],

            // Luna barangay residents
            ['email' => 'grace.padilla@gmail.com',      'name' => 'Grace Padilla',         'gender' => 'female', 'birthdate' => '1990-01-14', 'age' => 35],
            ['email' => 'romeo.delos.santos@gmail.com', 'name' => 'Romeo Delos Santos',    'gender' => 'male',   'birthdate' => '1975-09-03', 'age' => 50],
            ['email' => 'lorna.abad@gmail.com',         'name' => 'Lorna Abad',            'gender' => 'female', 'birthdate' => '1983-04-19', 'age' => 42],
            ['email' => 'felix.guerrero@gmail.com',     'name' => 'Felix Guerrero',        'gender' => 'male',   'birthdate' => '1968-12-01', 'age' => 57],
            ['email' => 'cynthia.reyes@gmail.com',      'name' => 'Cynthia Reyes',         'gender' => 'female', 'birthdate' => '1992-06-28', 'age' => 33],
            ['email' => 'marlon.espino@gmail.com',      'name' => 'Marlon Espino',         'gender' => 'male',   'birthdate' => '1987-02-17', 'age' => 38],

            // Bacsay barangay residents
            ['email' => 'eden.bautista@gmail.com',      'name' => 'Eden Bautista',         'gender' => 'female', 'birthdate' => '1995-08-10', 'age' => 30],
            ['email' => 'noel.mendoza@gmail.com',       'name' => 'Noel Mendoza',          'gender' => 'male',   'birthdate' => '1972-05-25', 'age' => 53],
            ['email' => 'roselyn.garcia@gmail.com',     'name' => 'Roselyn Garcia',        'gender' => 'female', 'birthdate' => '1988-11-07', 'age' => 37],
            ['email' => 'alberto.ramos@gmail.com',      'name' => 'Alberto Ramos',         'gender' => 'male',   'birthdate' => '1965-03-30', 'age' => 60],
            ['email' => 'arlene.austria@gmail.com',     'name' => 'Arlene Austria',        'gender' => 'female', 'birthdate' => '1997-10-12', 'age' => 28],
            ['email' => 'danilo.flores@gmail.com',      'name' => 'Danilo Flores',         'gender' => 'male',   'birthdate' => '1980-07-04', 'age' => 45],
            ['email' => 'loreta.villanueva@gmail.com',  'name' => 'Loreta Villanueva',     'gender' => 'female', 'birthdate' => '1970-01-22', 'age' => 55],

            // Turod barangay residents
            ['email' => 'randy.hernandez@gmail.com',    'name' => 'Randy Hernandez',       'gender' => 'male',   'birthdate' => '1993-04-08', 'age' => 32],
            ['email' => 'emily.castillo@gmail.com',     'name' => 'Emily Castillo',        'gender' => 'female', 'birthdate' => '1986-09-15', 'age' => 39],
            ['email' => 'victor.navarro@gmail.com',     'name' => 'Victor Navarro',        'gender' => 'male',   'birthdate' => '1960-06-20', 'age' => 65],
            ['email' => 'myra.luna@gmail.com',          'name' => 'Myra Luna',             'gender' => 'female', 'birthdate' => '1999-12-03', 'age' => 26],
            ['email' => 'rolando.imperial@gmail.com',   'name' => 'Rolando Imperial',      'gender' => 'male',   'birthdate' => '1977-08-18', 'age' => 48],
            ['email' => 'teresita.macion@gmail.com',    'name' => 'Teresita Macion',       'gender' => 'female', 'birthdate' => '1955-02-14', 'age' => 70],

            // Zumigui barangay residents
            ['email' => 'joel.pascual@gmail.com',       'name' => 'Joel Pascual',          'gender' => 'male',   'birthdate' => '1991-03-27', 'age' => 34],
            ['email' => 'eva.domingo@gmail.com',        'name' => 'Eva Domingo',           'gender' => 'female', 'birthdate' => '1984-07-11', 'age' => 41],
            ['email' => 'harold.enriquez@gmail.com',    'name' => 'Harold Enriquez',       'gender' => 'male',   'birthdate' => '1996-01-06', 'age' => 29],
            ['email' => 'nelda.corpuz@gmail.com',       'name' => 'Nelda Corpuz',          'gender' => 'female', 'birthdate' => '1963-10-30', 'age' => 62],
            ['email' => 'christopher.bueno@gmail.com',  'name' => 'Christopher Bueno',     'gender' => 'male',   'birthdate' => '1989-05-16', 'age' => 36],
            ['email' => 'aurora.santiago@gmail.com',    'name' => 'Aurora Santiago',       'gender' => 'female', 'birthdate' => '1973-12-09', 'age' => 52],
            ['email' => 'gerald.aquino@gmail.com',      'name' => 'Gerald Aquino',         'gender' => 'male',   'birthdate' => '2001-04-23', 'age' => 24],
            ['email' => 'marietta.soriano@gmail.com',   'name' => 'Marietta Soriano',      'gender' => 'female', 'birthdate' => '1981-08-07', 'age' => 44],
            ['email' => 'arsenio.dela.cruz@gmail.com',  'name' => 'Arsenio Dela Cruz',     'gender' => 'male',   'birthdate' => '1958-11-19', 'age' => 67],
        ];

        foreach ($citizens as $c) {
            User::updateOrCreate(
                ['email' => $c['email']],
                [
                    'name'           => $c['name'],
                    'password'       => bcrypt('citizen1234'),
                    'role'           => 'citizen',
                    'account_status' => 'approved',
                    'gender'         => $c['gender'],
                    'birthdate'      => $c['birthdate'],
                    'age'            => $c['age'],
                ]
            );
        }

        $this->command->info('Seeded: 3 admins, 1 RHU, 30 citizens.');
    }
}
