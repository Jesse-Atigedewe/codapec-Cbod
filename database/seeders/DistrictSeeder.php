<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
     $districtsByRegion = [
            'BRONG-AHAFO' => [
                'BECHEM',
                'BEKWAI',
                'BEREKUM',
                'DADIESOABA',
                'DORMAA AHENKRO',
                'GOASO',
                'NKRWANKWANTA',
                'SANKORE',
                'SUNYANI',
                'TECHIMAN',
            ],
            'CENTRAL' => [
                'AJUMAKO',
                'ASIKUMA',
                'ASSIN FOSU',
                'JUKWA',
                'NYAKROM',
                'TWIFO PRASO',
            ],
            'EASTERN' => [
                'ASAMANKESE',
                'AYIREBI',
                'BAWDUA',
                'KADE',
                'MAMPONG',
                'NANKESE',
                'NEW ABIREM',
                'BAWDUA',
                'NKAWKAW',
                'ODA',
                'OSINO',
                'SUHUM',
                'TAFO'

            ],
            'ASHANTI' => [
                'NEW EDUBIASE',
                'OBUASI',
                'ANTOAKROM',
                'BEKWAI',
                'EFFIDUASE',
                'ASIWA',
                'BROFOYEDRU',
                'JUASO',
                'KONONGO',
                'OFFINSO',
                'NKAWIE',
                'MANKRANSO',
                'TEPA',
                'NYINAHIN',
                'MAMPONG',

            ],
            'WESTERN-NORTH' => [
                'ADJOAFUA', 
                'JUABOSO',
                'BODI',
                'ESSAM',
                'AKONTOMBRA',
                'ADABOKROM',
                'BOAKO',
                'DADIESO',
                'BIBIANI',
                'SEFWI BEKWAI',
                'ENCHI',

            ],
            'VOLTA' => [
                'JASIKAN',
                'HOHOE',
                'DODI PAPASE'

            ],
            'WESTERN-SOUTH' => [
                'AIYINASE',
                'GWIRA',
                'PRESTEA',
                'SAMREBOI',
                'ASANKRAGWA',
                'BOINSO',
                'ELUBO',
                'DABOASE',
                'DIASO',
                'WASSA AKROPONG',
                'DUNKWA',
                'HUNI VALLEY',
                'KEJEBRIL',
                'MANSO AMENFI',
                'TARKWA',

            ],
        ];

        foreach ($districtsByRegion as $regionName => $districts) {
            $region = Region::where('name', $regionName)->first();

            if (!$region) {
                continue;
            }

            foreach ($districts as $district) {
                District::firstOrCreate([
                    'name' => $district,
                    'region_id' => $region->id,
                ]);
            }
        }
        
         
    }
}
