<?php

namespace Database\Seeders\Traits;

trait UgandaLocationData
{
    protected function getUgandaRegionsWithDistricts(): array
    {
        return [
            'Central' => [
                'Kampala', 'Wakiso', 'Mukono', 'Luweero', 'Mpigi', 'Mityana', 'Nakaseke', 'Nakasongola',
                'Kayunga', 'Buikwe', 'Mubende', 'Kiboga', 'Kyankwanzi', 'Gomba', 'Butambala', 'Buvuma',
                'Kalungu', 'Lwengo', 'Lyantonde', 'Masaka', 'Rakai', 'Sembabule', 'Kalangala', 'Kyotera',
                'Bukomansimbi', 'Kassanda', 'Kikuube', 'Kakumiro', 'Kagadi', 'Kibaale', 'Kyenjojo', 'Kyegegwa',
                'Kiryandongo', 'Masindi', 'Buliisa', 'Ntoroko', 'Bundibugyo', 'Bunyangabu', 'Kasese', 'Kamwenge',
                'Kabarole'
            ],
            'Eastern' => [
                'Jinja', 'Iganga', 'Kamuli', 'Bugiri', 'Mayuge', 'Namutumba', 'Buyende', 'Kaliro', 'Namayingo',
                'Namisindwa', 'Bulambuli', 'Sironko', 'Kapchorwa', 'Kween', 'Bukwo', 'Mbale', 'Manafwa', 'Bududa',
                'Butaleja', 'Tororo', 'Pallisa', 'Kibuku', 'Budaka', 'Busia', 'Serere', 'Soroti', 'Kumi', 'Ngora',
                'Katakwi', 'Amuria', 'Kaberamaido', 'Dokolo', 'Amolatar'
            ],
            'Northern' => [
                'Lira', 'Alebtong', 'Otuke', 'Apac', 'Kole', 'Oyam', 'Dokolo', 'Amolatar', 'Gulu', 'Amuru',
                'Nwoya', 'Pader', 'Kitgum', 'Lamwo', 'Agago', 'Arua', 'Nebbi', 'Zombo', 'Pakwach', 'Madi Okollo',
                'Terego', 'Maracha', 'Koboko', 'Yumbe', 'Moyo', 'Obongi', 'Adjumani'
            ],
            'Western' => [
                'Mbarara', 'Bushenyi', 'Ntungamo', 'Rukungiri', 'Kabale', 'Kisoro', 'Kanungu', 'Ibanda', 'Kiruhura',
                'Buhweju', 'Mitooma', 'Rubanda', 'Rukiga', 'Sheema', 'Kasese', 'Kabarole', 'Bundibugyo', 'Ntoroko',
                'Kyenjojo', 'Kyegegwa', 'Kyenjojo', 'Kamwenge', 'Kyejonjo', 'Kabarole', 'Kasese', 'Ntoroko', 'Bundibugyo',
                'Bunyangabu', 'Kakumiro', 'Kagadi', 'Kibaale', 'Kyenjojo', 'Kyegegwa', 'Kiryandongo', 'Masindi', 'Buliisa',
                'Ntoroko', 'Bundibugyo', 'Bunyangabu'
            ]
        ];
    }

    protected function getUgandanSubCounties(string $district): array
    {
        // Common sub-counties that appear in many districts
        $commonSubCounties = [
            'Central', 'Division A', 'Division B', 'Division C', 'Town Council', 'Municipality', 'Rural', 'Urban',
            'North', 'South', 'East', 'West', 'Upper', 'Lower', 'Central', 'Town Board'
        ];

        $districtSubCounties = [
            'Kampala' => ['Kampala Central', 'Kawempe', 'Makindye', 'Nakawa', 'Lubaga', 'Nakasero', 'Kololo', 'Ntinda', 'Bwaise', 'Kisenyi'],
            'Wakiso' => ['Entebbe', 'Kira', 'Nansana', 'Makindye Ssabagabo', 'Kiira', 'Kajjansi', 'Gayaza', 'Wakiso Town', 'Kakiri', 'Sseguku'],
            'Mukono' => ['Mukono Town', 'Ntinda', 'Nama', 'Njeru', 'Seeta', 'Lugazi', 'Nabbaale', 'Nagojje', 'Kasawo', 'Ntunda'],
            'Jinja' => ['Jinja Central', 'Wanyange', 'Mpumudde', 'Bugembe', 'Wakaliga', 'Nalufenya', 'Budondo', 'Buwenge', 'Kakira', 'Mafubira'],
            'Mbarara' => ['Kakoba', 'Kamukuzi', 'Nyamitanga', 'Kashari', 'Rwampara', 'Ibanda', 'Kiruhura', 'Isingiro', 'Mbarara Municipality', 'Rwampara'],
            'Gulu' => ['Gulu Municipality', 'Laroo', 'Bardege', 'Layibi', 'Pece', 'Bardege-Layibi', 'Lakwana', 'Paicho', 'Awach', 'Paidha'],
            'Arua' => ['Arua Hill', 'Oluko', 'Vurra', 'Adumi', 'Offaka', 'Pajulu', 'Logiri', 'Dadamu', 'Ajia', 'Rhino Camp'],
            'Mbale' => ['Northern', 'Industrial', 'Wanale', 'Nkoma', 'Nakaloke', 'Bungokho', 'Nkoma', 'Bungokho-Mutoto', 'Busoba', 'Nakaloke'],
            'Lira' => ['Lira Municipality', 'Barr', 'Odit', 'Ojwina', 'Adyel', 'Odit', 'Ireda', 'Ongura', 'Adeknino', 'Ongura'],
            'Mbarara' => ['Kakoba', 'Kamukuzi', 'Nyamitanga', 'Kashari', 'Rwampara', 'Ibanda', 'Kiruhura', 'Isingiro', 'Mbarara Municipality', 'Rwampara'],
        ];

        return $districtSubCounties[$district] ?? array_map(
            fn($sc) => "$sc " . $this->getRandomSuffix(),
            array_slice($commonSubCounties, 0, rand(5, 10))
        );
    }

    protected function getRandomSuffix(): string
    {
        $suffixes = [
            'Division', 'Sub-county', 'Town Council', 'Municipality', 'County', 'Parish', 'Ward', 'Zone',
            'Block', 'Sector', 'Cell', 'Village', 'Parish', 'Sub-county', 'Town Board'
        ];
        return $suffixes[array_rand($suffixes)];
    }

    protected function getRandomUgandanDistrict(): string
    {
        $regions = $this->getUgandaRegionsWithDistricts();
        $randomRegion = array_rand($regions);
        $districts = $regions[$randomRegion];
        return $districts[array_rand($districts)];
    }

    protected function getRandomUgandanPhoneNumber(): string
    {
        $prefixes = ['70', '71', '72', '74', '75', '76', '77', '78', '79'];
        $prefix = $prefixes[array_rand($prefixes)];
        return '256' . $prefix . rand(100000, 999999);
    }

    /**
     * Get approximate coordinates for a district in Uganda
     */
    protected function getUgandaDistrictCoordinates(string $district): array
    {
        // Approximate coordinates for major districts in Uganda
        $coordinates = [
            // Central Region
            'Kampala' => ['lat' => 0.3136, 'lng' => 32.5811],
            'Wakiso' => ['lat' => 0.4044, 'lng' => 32.4595],
            'Mukono' => ['lat' => 0.3381, 'lng' => 32.7553],
            'Mpigi' => ['lat' => 0.2000, 'lng' => 31.9833],
            'Mityana' => ['lat' => 0.4000, 'lng' => 32.0500],
            'Masaka' => ['lat' => -0.3167, 'lng' => 31.7167],
            'Mubende' => ['lat' => 0.5500, 'lng' => 31.4000],
            'Kayunga' => ['lat' => 0.7000, 'lng' => 32.8833],
            'Luweero' => ['lat' => 0.8497, 'lng' => 32.4731],
            'Nakaseke' => ['lat' => 0.7167, 'lng' => 32.3833],
            'Nakasongola' => ['lat' => 1.3167, 'lng' => 32.4667],
            'Rakai' => ['lat' => -0.7200, 'lng' => 31.4000],
            'Sembabule' => ['lat' => -0.0833, 'lng' => 31.4000],
            'Kalungu' => ['lat' => -0.1667, 'lng' => 31.7500],
            'Lwengo' => ['lat' => -0.4000, 'lng' => 31.4000],
            'Lyantonde' => ['lat' => -0.4000, 'lng' => 31.1500],
            'Kalangala' => ['lat' => -0.3000, 'lng' => 32.2500],
            'Kyotera' => ['lat' => -0.6167, 'lng' => 31.5167],
            'Bukomansimbi' => ['lat' => -0.1500, 'lng' => 31.6000],
            'Gomba' => ['lat' => 0.1833, 'lng' => 31.9167],
            'Butambala' => ['lat' => 0.2000, 'lng' => 32.1000],
            'Buvuma' => ['lat' => 0.3000, 'lng' => 33.2500],
            'Kassanda' => ['lat' => 0.5000, 'lng' => 31.9000],
            'Kiboga' => ['lat' => 0.9167, 'lng' => 31.7667],
            'Kyankwanzi' => ['lat' => 1.2500, 'lng' => 31.8000],
            'Mityana' => ['lat' => 0.4000, 'lng' => 32.0500],
            'Mubende' => ['lat' => 0.5500, 'lng' => 31.4000],
            'Mukono' => ['lat' => 0.3381, 'lng' => 32.7553],
            'Nakaseke' => ['lat' => 0.7167, 'lng' => 32.3833],
            'Nakasongola' => ['lat' => 1.3167, 'lng' => 32.4667],
            'Rakai' => ['lat' => -0.7200, 'lng' => 31.4000],
            'Sembabule' => ['lat' => -0.0833, 'lng' => 31.4000],
            'Wakiso' => ['lat' => 0.4044, 'lng' => 32.4595],
            
            // Eastern Region
            'Jinja' => ['lat' => 0.4244, 'lng' => 33.2042],
            'Iganga' => ['lat' => 0.6092, 'lng' => 33.4686],
            'Kamuli' => ['lat' => 0.9472, 'lng' => 33.1197],
            'Bugiri' => ['lat' => 0.5714, 'lng' => 33.7417],
            'Mayuge' => ['lat' => 0.4597, 'lng' => 33.4803],
            'Namutumba' => ['lat' => 0.8361, 'lng' => 33.6856],
            'Buyende' => ['lat' => 1.1517, 'lng' => 33.1556],
            'Kaliro' => ['lat' => 0.8949, 'lng' => 33.5048],
            'Namayingo' => ['lat' => 0.2397, 'lng' => 33.8849],
            'Namisindwa' => ['lat' => 0.7794, 'lng' => 34.3742],
            'Bulambuli' => ['lat' => 1.1667, 'lng' => 34.3833],
            'Sironko' => ['lat' => 1.2333, 'lng' => 34.2500],
            'Kapchorwa' => ['lat' => 1.4000, 'lng' => 34.4500],
            'Kween' => ['lat' => 1.4167, 'lng' => 34.5333],
            'Bukwo' => ['lat' => 1.2833, 'lng' => 34.7500],
            'Mbale' => ['lat' => 1.0806, 'lng' => 34.1750],
            'Manafwa' => ['lat' => 0.9167, 'lng' => 34.2833],
            'Bududa' => ['lat' => 1.0000, 'lng' => 34.3333],
            'Butaleja' => ['lat' => 0.9167, 'lng' => 33.9500],
            'Tororo' => ['lat' => 0.6833, 'lng' => 34.1833],
            'Pallisa' => ['lat' => 1.1500, 'lng' => 33.7167],
            'Kibuku' => ['lat' => 1.0333, 'lng' => 33.8000],
            'Budaka' => ['lat' => 1.0833, 'lng' => 33.9500],
            'Busia' => ['lat' => 0.4667, 'lng' => 34.0833],
            'Serere' => ['lat' => 1.5000, 'lng' => 33.4667],
            'Soroti' => ['lat' => 1.7167, 'lng' => 33.6167],
            'Kumi' => ['lat' => 1.5000, 'lng' => 33.9333],
            'Ngora' => ['lat' => 1.4333, 'lng' => 33.7833],
            'Katakwi' => ['lat' => 1.9167, 'lng' => 34.0000],
            'Amuria' => ['lat' => 2.0000, 'lng' => 33.6500],
            'Kaberamaido' => ['lat' => 1.7389, 'lng' => 33.1594],
            'Dokolo' => ['lat' => 1.9167, 'lng' => 33.1667],
            'Amolatar' => ['lat' => 1.6333, 'lng' => 32.8167],
            
            // Northern Region
            'Lira' => ['lat' => 2.2350, 'lng' => 32.9097],
            'Alebtong' => ['lat' => 2.2500, 'lng' => 33.2500],
            'Otuke' => ['lat' => 2.5000, 'lng' => 33.5000],
            'Apac' => ['lat' => 1.9833, 'lng' => 32.5333],
            'Kole' => ['lat' => 2.4000, 'lng' => 32.8000],
            'Oyam' => ['lat' => 2.2500, 'lng' => 32.4167],
            'Gulu' => ['lat' => 2.7667, 'lng' => 32.3000],
            'Amuru' => ['lat' => 2.8333, 'lng' => 31.9167],
            'Nwoya' => ['lat' => 2.6333, 'lng' => 31.8500],
            'Pader' => ['lat' => 2.8000, 'lng' => 33.1333],
            'Kitgum' => ['lat' => 3.2833, 'lng' => 32.8833],
            'Lamwo' => ['lat' => 3.5000, 'lng' => 32.8000],
            'Agago' => ['lat' => 3.0000, 'lng' => 33.5000],
            'Arua' => ['lat' => 3.0300, 'lng' => 30.9100],
            'Nebbi' => ['lat' => 2.4833, 'lng' => 31.1000],
            'Zombo' => ['lat' => 2.5000, 'lng' => 30.9000],
            'Pakwach' => ['lat' => 2.4667, 'lng' => 31.5000],
            'Madi Okollo' => ['lat' => 2.2500, 'lng' => 31.3333],
            'Terego' => ['lat' => 3.0000, 'lng' => 31.1667],
            'Maracha' => ['lat' => 3.2500, 'lng' => 30.9167],
            'Koboko' => ['lat' => 3.4136, 'lng' => 30.9599],
            'Yumbe' => ['lat' => 3.4500, 'lng' => 31.2500],
            'Moyo' => ['lat' => 3.6500, 'lng' => 31.7167],
            'Obongi' => ['lat' => 3.3000, 'lng' => 31.4000],
            'Adjumani' => ['lat' => 3.3667, 'lng' => 31.8000],
            
            // Western Region
            'Mbarara' => ['lat' => -0.6136, 'lng' => 30.6583],
            'Bushenyi' => ['lat' => -0.5333, 'lng' => 30.1833],
            'Ntungamo' => ['lat' => -0.8833, 'lng' => 30.2667],
            'Rukungiri' => ['lat' => -0.7833, 'lng' => 29.9167],
            'Kabale' => ['lat' => -1.2500, 'lng' => 29.9833],
            'Kisoro' => ['lat' => -1.2833, 'lng' => 29.6833],
            'Kanungu' => ['lat' => -0.7000, 'lng' => 29.7333],
            'Ibanda' => ['lat' => -0.1333, 'lng' => 30.5000],
            'Kiruhura' => ['lat' => -0.2000, 'lng' => 30.8500],
            'Buhweju' => ['lat' => -0.3500, 'lng' => 30.3000],
            'Mitooma' => ['lat' => -0.6000, 'lng' => 30.0167],
            'Rubanda' => ['lat' => -1.1667, 'lng' => 29.8500],
            'Rukiga' => ['lat' => -1.2000, 'lng' => 30.1000],
            'Sheema' => ['lat' => -0.6000, 'lng' => 30.2500],
            'Kasese' => ['lat' => 0.1833, 'lng' => 30.0833],
            'Kabarole' => ['lat' => 0.6000, 'lng' => 30.2500],
            'Bundibugyo' => ['lat' => 0.7000, 'lng' => 30.0667],
            'Ntoroko' => ['lat' => 1.0500, 'lng' => 30.4833],
            'Kyenjojo' => ['lat' => 0.6167, 'lng' => 30.6500],
            'Kyegegwa' => ['lat' => 0.5000, 'lng' => 31.0500],
            'Kiryandongo' => ['lat' => 1.7667, 'lng' => 32.1000],
            'Masindi' => ['lat' => 1.6833, 'lng' => 31.7167],
            'Buliisa' => ['lat' => 2.1167, 'lng' => 31.4000],
            'Kakumiro' => ['lat' => 0.7833, 'lng' => 31.3167],
            'Kagadi' => ['lat' => 0.9500, 'lng' => 30.8000],
            'Kibaale' => ['lat' => 0.9667, 'lng' => 31.2000],
            'Bunyangabu' => ['lat' => 0.5000, 'lng' => 30.2500],
            'Kikuube' => ['lat' => 1.2500, 'lng' => 31.3000],
            'Kakumiro' => ['lat' => 0.7833, 'lng' => 31.3167],
            'Kagadi' => ['lat' => 0.9500, 'lng' => 30.8000],
            'Kibaale' => ['lat' => 0.9667, 'lng' => 31.2000],
            'Bunyangabu' => ['lat' => 0.5000, 'lng' => 30.2500],
            'Kikuube' => ['lat' => 1.2500, 'lng' => 31.3000],
            'Kakumiro' => ['lat' => 0.7833, 'lng' => 31.3167],
            'Kagadi' => ['lat' => 0.9500, 'lng' => 30.8000],
            'Kibaale' => ['lat' => 0.9667, 'lng' => 31.2000],
            'Bunyangabu' => ['lat' => 0.5000, 'lng' => 30.2500],
            'Kikuube' => ['lat' => 1.2500, 'lng' => 31.3000],
            'Kakumiro' => ['lat' => 0.7833, 'lng' => 31.3167],
            'Kagadi' => ['lat' => 0.9500, 'lng' => 30.8000],
            'Kibaale' => ['lat' => 0.9667, 'lng' => 31.2000],
            'Bunyangabu' => ['lat' => 0.5000, 'lng' => 30.2500],
            'Kikuube' => ['lat' => 1.2500, 'lng' => 31.3000],
            'Kakumiro' => ['lat' => 0.7833, 'lng' => 31.3167],
            'Kagadi' => ['lat' => 0.9500, 'lng' => 30.8000],
            'Kibaale' => ['lat' => 0.9667, 'lng' => 31.2000],
            'Bunyangabu' => ['lat' => 0.5000, 'lng' => 30.2500],
            'Kikuube' => ['lat' => 1.2500, 'lng' => 31.3000],
            'Kakumiro' => ['lat' => 0.7833, 'lng' => 31.3167],
            'Kagadi' => ['lat' => 0.9500, 'lng' => 30.8000],
            'Kibaale' => ['lat' => 0.9667, 'lng' => 31.2000],
            'Bunyangabu' => ['lat' => 0.5000, 'lng' => 30.2500],
            'Kikuube' => ['lat' => 1.2500, 'lng' => 31.3000],
            'Kakumiro' => ['lat' => 0.7833, 'lng' => 31.3167],
            'Kagadi' => ['lat' => 0.9500, 'lng' => 30.8000],
            'Kibaale' => ['lat' => 0.9667, 'lng' => 31.2000],
            'Bunyangabu' => ['lat' => 0.5000, 'lng' => 30.2500],
            'Kikuube' => ['lat' => 1.2500, 'lng' => 31.3000],
        ];

        // Return coordinates for the district or default to Kampala if not found
        return $coordinates[$district] ?? ['lat' => 0.3136, 'lng' => 32.5811];
    }
}
