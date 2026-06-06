<?php

namespace Database\Seeders;

use App\Models\HealthCategory;
use Illuminate\Database\Seeder;

class HealthCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Dengue',
                'description' => 'Sakit na dala ng kagat ng lamok. Mapanganib kapag hindi agad ginagamot.',
                'prevention_tips' => [
                    'Alisin ang tubig na nakatengga sa mga timba, palayok, gulong, o anumang lalagyan.',
                    'Gumamit ng mosquito net o taksan ang mga bintana ng screen para hindi makapasok ang lamok.',
                    'Magsuot ng mahabang damit kapag labas ng bahay, lalo na sa umaga at hapon.',
                    'Mag-apply ng anti-mosquito lotion o spray sa balat na nakalantad.',
                    'Linisin ang paligid ng bahay at sumali sa community clean-up drives.',
                ],
                'action_steps' => [
                    'Magpahinga at uminom ng maraming tubig, juice, o coconut water.',
                    'Uminom ng paracetamol para sa lagnat. Huwag uminom ng aspirin o ibuprofen.',
                    'Bantayan ang mga palatandaan ng pagpalala: matinding sakit sa tiyan, pagsusuka ng dugo, o pagdurugo ng gilagid.',
                    'Pumunta agad sa pinakamalapit na health center o ospital kapag lumala ang kalagayan.',
                ],
            ],
            [
                'name'        => 'Tuberculosis',
                'description' => 'Sakit sa baga na kumakalat sa hangin kapag umubo ang may sakit. Pwede itong gamutin nang buo.',
                'prevention_tips' => [
                    'Siguraduhing mahangin at may sikat ng araw sa loob ng bahay.',
                    'Takpan ang bibig at ilong kapag umubo o bumahing gamit ang tela o siko.',
                    'Ipabakuna ng BCG ang sanggol para maprotektahan mula sa malubhang TB.',
                    'Iwasan ang matagal na pakikisama nang walang maskara sa taong may TB.',
                    'Kumain ng sapat at masustansyang pagkain para maging malakas ang katawan.',
                ],
                'action_steps' => [
                    'Pumunta agad sa pinakamalapit na health center para magpasuri ng plema.',
                    'Inumin ang lahat ng gamot para sa TB araw-araw nang walang palya.',
                    'Tapusin ang buong 6 na buwang gamutan kahit maramdam na itong galing.',
                    'Magsuot ng maskara sa bahay para hindi mahawa ang mga kasama.',
                    'Ipasuri din ang mga kasambahay sa pinakamalapit na health center.',
                ],
            ],
            [
                'name'        => 'Malnutrition',
                'description' => 'Nangyayari ito kapag ang isang tao, lalo na ang bata, ay hindi nakakakuha ng sapat na pagkain para lumaki nang malusog.',
                'prevention_tips' => [
                    'Mag-breastfeed ng eksklusibo sa unang 6 na buwan ng buhay ng sanggol.',
                    'Pagkatapos ng 6 na buwan, simulan nang magbigay ng soft foods tulad ng lugaw, pinisang gulay, at itlog.',
                    'Kumain ng iba-ibang pagkain araw-araw: kanin, prutas, gulay, isda, itlog, at munggo.',
                    'Isali ang mga bata sa Supplemental Feeding Program o Pantawid Pamilya sa barangay.',
                    'Dalhin ang bata sa health center para sa regular na pagsukat ng timbang at taas.',
                ],
                'action_steps' => [
                    'Dalhin agad ang bata sa pinakamalapit na health center para suriin ang kanyang nutrisyon.',
                    'Sundin ang feeding plan ng health worker at huwag laktawan ang anumang kain.',
                    'Ibigay ang therapeutic food na inireset ng nars o doktor ayon sa tamang dami.',
                    'Gamutin ang anumang kasamang sakit tulad ng pagtatae o bulate na maaaring magpasama.',
                    'Subaybayan ang timbang linggo-linggo at iulat ang anumang pagbabago sa health center.',
                ],
            ],
            [
                'name'        => 'Hypertension',
                'description' => 'Mataas na presyon ng dugo. Mapanganib dahil kadalasan ay walang nararamdamang sintomas hanggang sa lumala na.',
                'prevention_tips' => [
                    'Bawasan ang asin sa pagkain. Layuan ang mga de-latang pagkain at maalat na ulam.',
                    'Kumain ng mas maraming prutas at gulay. Bawasan ang mataba at pritong pagkain.',
                    'Mag-ehersisyo kahit 30 minuto sa araw, tulad ng paglalakad o magaang na stretching.',
                    'Huminto sa paninigarilyo at bawasan ang pag-inom ng alak.',
                    'Magpahinga at huwag magpapalubog sa stress. Kausapin ang isang taong pinagkakatiwalaan.',
                ],
                'action_steps' => [
                    'Magpasukat ng presyon ng dugo sa health center nang regular.',
                    'Inumin ang gamot para sa mataas na presyon araw-araw. Huwag laktawan.',
                    'Huwag itigil ang gamot kahit maramdam na itong ayos. Ang mataas na presyon ay walang sintomas.',
                    'Alamin ang mga babala: matinding sakit ng ulo, malabong paningin, sakit sa dibdib. Pumunta agad sa ospital.',
                    'Kapag sobra ang timbang, subukan na mabawasan kahit konti. Makakatulong na ito.',
                ],
            ],
            [
                'name'        => 'Diarrhea',
                'description' => 'Madalas na pagtatae na maaaring magpahina at magpadehydrate, lalo na sa mga bata.',
                'prevention_tips' => [
                    'Laging maghugas ng kamay gamit ang sabon bago kumain at pagkatapos gumamit ng palikuran.',
                    'Uminom lamang ng malinis na tubig. Pakuluan muna kung hindi sigurado.',
                    'Takpan ang pagkain at kumain ng sariwang luto.',
                    'Gumamit ng malinis na palikuran at itapon ang dumi sa tamang lugar.',
                    'Magpasuso sa sanggol. Ang gatas ng ina ay nagpoprotekta sa impeksyon.',
                ],
                'action_steps' => [
                    'Magbigay agad ng Oral Rehydration Solution (ORS). Ihalo ang 1 sachet sa 1 litro ng malinis na tubig.',
                    'Magpainom ng kaunti-konti pero madalas, lalo na sa mga bata.',
                    'Huwag itigil ang pagkain. Magbigay ng malambot na pagkain tulad ng lugaw.',
                    'Pumunta sa health center kung mahigit 2 araw na ang pagtatae, may dugo, o hindi makainom.',
                    'Para sa mga bata, bantayan ang: malalalim na mata, tuyong bibig, walang luha. Ito ay senyales ng panganib.',
                ],
            ],
        ];

        // Default DSS thresholds per category (moderate_min, high_min, critical_min)
        // Based on localized PHC guidelines — admin can override via the Decision Support page.
        $thresholds = [
            'Dengue'        => ['moderate' => 5,  'high' => 15, 'critical' => 30],
            'Tuberculosis'  => ['moderate' => 3,  'high' => 10, 'critical' => 20],
            'Malnutrition'  => ['moderate' => 5,  'high' => 15, 'critical' => 25],
            'Hypertension'  => ['moderate' => 10, 'high' => 30, 'critical' => 50],
            'Diarrhea'      => ['moderate' => 5,  'high' => 15, 'critical' => 30],
        ];

        foreach ($categories as $category) {
            $category['dss_thresholds'] = $thresholds[$category['name']] ?? ['moderate' => 5, 'high' => 15, 'critical' => 30];

            HealthCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
