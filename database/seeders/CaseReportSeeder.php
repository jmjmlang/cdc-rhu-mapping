<?php

namespace Database\Seeders;

use App\Models\Barangay;
use App\Models\CaseReport;
use App\Models\HealthCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class CaseReportSeeder extends Seeder
{
    public function run(): void
    {
        // ── Load references ────────────────────────────────────────────────────
        $admin    = User::where('email', 'admin@gmail.com')->firstOrFail();
        $admin2   = User::where('email', 'maricel.santos@gmail.com')->first() ?? $admin;
        $admin3   = User::where('email', 'renato.cruz@gmail.com')->first()   ?? $admin;

        $citizens = User::where('role', 'citizen')->get()->keyBy('email');
        $barangays  = Barangay::all()->keyBy('name');
        $categories = HealthCategory::all()->keyBy('name');

        if ($barangays->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('Skipping CaseReportSeeder — no barangays or categories.');
            return;
        }

        // Helper: get citizen by email, fallback to admin if not seeded yet
        $c = fn (string $email) => $citizens[$email] ?? $admin;

        // Shorthand references
        $bacsay  = $barangays['Bacsay'];
        $turod   = $barangays['Turod'];
        $zumigui = $barangays['Zumigui'];
        $luna    = $barangays['Luna'];

        $dengue      = $categories['Dengue'];
        $tb          = $categories['Tuberculosis'];
        $maln        = $categories['Malnutrition'];
        $hyper       = $categories['Hypertension'];
        $diarrhea    = $categories['Diarrhea'];

        // ── Build the reports dataset ──────────────────────────────────────────
        // Format: [user, barangay, category, cases, status, days_ago, reviewed_by?, notes?]
        $rows = [

            // ───────────────────────────────────────────────────────────────────
            // APPROVED REPORTS — current 30-day window (days 1–28)
            // ───────────────────────────────────────────────────────────────────

            // Dengue cluster — Bacsay (creates Critical/High DSS signal)
            [$c('johnmichael.talbo@gmail.com'),  $bacsay,  $dengue,   12, 'approved',  2,  $admin],
            [$c('eden.bautista@gmail.com'),       $bacsay,  $dengue,    8, 'approved',  4,  $admin],
            [$c('noel.mendoza@gmail.com'),         $bacsay,  $dengue,    6, 'approved',  6,  $admin2],
            [$c('arlene.austria@gmail.com'),       $bacsay,  $dengue,    5, 'approved',  9,  $admin2],
            [$c('danilo.flores@gmail.com'),        $bacsay,  $dengue,    4, 'approved', 12,  $admin],

            // TB — Turod
            [$c('engiemar.balanay@gmail.com'),    $turod,   $tb,        3, 'approved',  3,  $admin],
            [$c('randy.hernandez@gmail.com'),     $turod,   $tb,        4, 'approved',  7,  $admin3],
            [$c('emily.castillo@gmail.com'),      $turod,   $tb,        2, 'approved', 11,  $admin3],
            [$c('victor.navarro@gmail.com'),      $turod,   $tb,        5, 'approved', 15,  $admin],
            [$c('myra.luna@gmail.com'),           $turod,   $tb,        1, 'approved', 18,  $admin2],

            // Malnutrition — Zumigui (significant cluster)
            [$c('joel.pascual@gmail.com'),        $zumigui, $maln,      7, 'approved',  5,  $admin],
            [$c('eva.domingo@gmail.com'),         $zumigui, $maln,      9, 'approved',  8,  $admin2],
            [$c('harold.enriquez@gmail.com'),     $zumigui, $maln,      6, 'approved', 13,  $admin],
            [$c('nelda.corpuz@gmail.com'),        $zumigui, $maln,      4, 'approved', 16,  $admin3],
            [$c('christopher.bueno@gmail.com'),   $zumigui, $maln,      8, 'approved', 20,  $admin],

            // Hypertension — Luna
            [$c('grace.padilla@gmail.com'),       $luna,    $hyper,     3, 'approved',  4,  $admin],
            [$c('romeo.delos.santos@gmail.com'),  $luna,    $hyper,     5, 'approved',  7,  $admin2],
            [$c('lorna.abad@gmail.com'),          $luna,    $hyper,     2, 'approved', 10,  $admin],
            [$c('felix.guerrero@gmail.com'),      $luna,    $hyper,     6, 'approved', 14,  $admin3],
            [$c('cynthia.reyes@gmail.com'),       $luna,    $hyper,     4, 'approved', 18,  $admin],

            // Diarrhea — cross-barangay
            [$c('marlon.espino@gmail.com'),       $luna,    $diarrhea,  3, 'approved',  6,  $admin2],
            [$c('roselyn.garcia@gmail.com'),      $bacsay,  $diarrhea,  2, 'approved',  9,  $admin],
            [$c('rolando.imperial@gmail.com'),    $turod,   $diarrhea,  4, 'approved', 13,  $admin],
            [$c('aurora.santiago@gmail.com'),     $zumigui, $diarrhea,  3, 'approved', 17,  $admin3],
            [$c('gerald.aquino@gmail.com'),       $zumigui, $diarrhea,  5, 'approved', 22,  $admin],

            // Additional approved — last month edge (days 28–30, still in window)
            [$c('alberto.ramos@gmail.com'),       $bacsay,  $hyper,     4, 'approved', 25,  $admin],
            [$c('loreta.villanueva@gmail.com'),   $bacsay,  $maln,      2, 'approved', 27,  $admin2],
            [$c('teresita.macion@gmail.com'),     $turod,   $hyper,     6, 'approved', 28,  $admin],
            [$c('marietta.soriano@gmail.com'),    $zumigui, $tb,        3, 'approved', 26,  $admin3],
            [$c('arsenio.dela.cruz@gmail.com'),   $luna,    $dengue,    2, 'approved', 24,  $admin],

            // ───────────────────────────────────────────────────────────────────
            // LAST MONTH WINDOW (days 32–58) — for trend chart comparison
            // ───────────────────────────────────────────────────────────────────

            [$c('johnmichael.talbo@gmail.com'),  $bacsay,  $dengue,    5, 'approved', 33,  $admin],
            [$c('engiemar.balanay@gmail.com'),   $turod,   $tb,        2, 'approved', 35,  $admin],
            [$c('joel.pascual@gmail.com'),       $zumigui, $maln,      3, 'approved', 37,  $admin2],
            [$c('grace.padilla@gmail.com'),      $luna,    $hyper,     4, 'approved', 40,  $admin],
            [$c('eden.bautista@gmail.com'),      $bacsay,  $dengue,    3, 'approved', 42,  $admin],
            [$c('emily.castillo@gmail.com'),     $turod,   $diarrhea,  2, 'approved', 44,  $admin3],
            [$c('eva.domingo@gmail.com'),        $zumigui, $maln,      6, 'approved', 46,  $admin],
            [$c('lorna.abad@gmail.com'),         $luna,    $hyper,     3, 'approved', 49,  $admin2],
            [$c('harold.enriquez@gmail.com'),    $zumigui, $tb,        2, 'approved', 51,  $admin],
            [$c('alberto.ramos@gmail.com'),      $bacsay,  $hyper,     5, 'approved', 54,  $admin],

            // ───────────────────────────────────────────────────────────────────
            // PENDING REPORTS (last 30 days) — for verification queue
            // ───────────────────────────────────────────────────────────────────

            [$c('marietta.soriano@gmail.com'),   $zumigui, $dengue,    4, 'pending',   1,  null],
            [$c('nelson.unused@gmail.com'),      $bacsay,  $tb,        2, 'pending',   1,  null], // will fall back to $admin user_id — OK
            [$c('aurora.santiago@gmail.com'),    $zumigui, $hyper,     3, 'pending',   2,  null],
            [$c('christopher.bueno@gmail.com'),  $zumigui, $maln,      5, 'pending',   2,  null],
            [$c('randy.hernandez@gmail.com'),    $turod,   $dengue,    2, 'pending',   3,  null],
            [$c('noel.mendoza@gmail.com'),        $bacsay,  $maln,      6, 'pending',   3,  null],
            [$c('gerald.aquino@gmail.com'),      $zumigui, $tb,        1, 'pending',   4,  null],
            [$c('marlon.espino@gmail.com'),      $luna,    $diarrhea,  3, 'pending',   5,  null],

            // ───────────────────────────────────────────────────────────────────
            // REJECTED REPORTS — with notes
            // ───────────────────────────────────────────────────────────────────

            [$c('engiemar.balanay@gmail.com'),   $zumigui, $diarrhea,  1, 'rejected',  8,  $admin, 'Duplicate submission — already reported by another citizen.'],
            [$c('john michael.talbo@gmail.com'), $bacsay,  $hyper,     2, 'rejected', 11,  $admin2, 'Insufficient supporting information.'],
            [$c('victor.navarro@gmail.com'),     $luna,    $dengue,    1, 'rejected', 14,  $admin, 'Reported barangay does not match citizen registration.'],
        ];

        $count = 0;

        foreach ($rows as $row) {
            [$user, $barangay, $category, $cases, $status, $daysAgo, $reviewer, $notes] = array_pad($row, 8, null);

            // Skip rows where the citizen wasn't found (fallback to admin means unintended)
            if (! $user || ! $barangay || ! $category) {
                continue;
            }

            $data = [
                'user_id'            => $user->id,
                'barangay_id'        => $barangay->id,
                'health_category_id' => $category->id,
                'number_of_cases'    => $cases,
                'status'             => $status,
                'report_date'        => now()->subDays($daysAgo)->toDateString(),
            ];

            if ($reviewer) {
                $data['reviewed_by'] = $reviewer->id;
                $data['reviewed_at'] = now()->subDays($daysAgo - 1);
            }

            if ($notes) {
                $data['notes'] = $notes;
            }

            CaseReport::updateOrCreate(
                [
                    'user_id'            => $data['user_id'],
                    'barangay_id'        => $data['barangay_id'],
                    'health_category_id' => $data['health_category_id'],
                    'report_date'        => $data['report_date'],
                ],
                $data
            );

            $count++;
        }

        $this->command->info("Seeded {$count} case reports (approved + pending + rejected, current + last month window).");
    }
}
