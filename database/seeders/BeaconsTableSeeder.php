<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BeaconsTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $beacons = [
            ['id' => 1, 'name' => 'Bayanan Main Road', 'location_id' => 3, 'oid' => 'OID_16', 'beacon_id' => '16', 'group' => 'Bayanan', 'latitude' => 14.4101576, 'longitude' => 121.0502],
            ['id' => 2, 'name' => 'Bayanan Baywalk', 'location_id' => 3, 'oid' => 'OID_12', 'beacon_id' => '12', 'group' => 'Bayanan', 'latitude' => 14.4108768, 'longitude' => 121.0502],
            ['id' => 3, 'name' => 'Poblacion Brgy Hall', 'location_id' => 6, 'oid' => 'OID_14', 'beacon_id' => '14', 'group' => 'Poblacion', 'latitude' => 14.3904033, 'longitude' => 121.0425],
            ['id' => 4, 'name' => 'Poblacion Health Center', 'location_id' => 6, 'oid' => 'OID_20', 'beacon_id' => '20', 'group' => 'Poblacion', 'latitude' => 14.3900203, 'longitude' => 121.0425],
            ['id' => 5, 'name' => 'Poblacion NHA Health Center', 'location_id' => 6, 'oid' => 'OID_13', 'beacon_id' => '13', 'group' => 'Poblacion', 'latitude' => 14.3736952, 'longitude' => 121.0425],
            ['id' => 6, 'name' => 'Tunasan Plaza Central', 'location_id' => 6, 'oid' => 'OID_18', 'beacon_id' => '18', 'group' => 'Poblacion', 'latitude' => 14.3892547, 'longitude' => 121.0425],
            ['id' => 7, 'name' => 'Poblacion Elem School', 'location_id' => 6, 'oid' => 'OID_11', 'beacon_id' => '11', 'group' => 'Poblacion', 'latitude' => 14.3884042, 'longitude' => 121.0425],
            ['id' => 8, 'name' => 'Alabang Liwasan', 'location_id' => 1, 'oid' => 'OID_07', 'beacon_id' => '7', 'group' => 'Alabang', 'latitude' => 14.4185506, 'longitude' => 121.0300],
            ['id' => 9, 'name' => 'Alabang Ayala', 'location_id' => 2, 'oid' => 'OID_06', 'beacon_id' => '6', 'group' => 'Ayala Alabang', 'latitude' => 14.4183009, 'longitude' => 121.0220],
            ['id' => 10, 'name' => 'Alabang Elem School', 'location_id' => 1, 'oid' => 'OID_08', 'beacon_id' => '8', 'group' => 'Alabang', 'latitude' => 14.420727, 'longitude' => 121.0300],
            ['id' => 11, 'name' => 'Putatan Solideir Hills E.S.', 'location_id' => 7, 'oid' => 'OID_03', 'beacon_id' => '3', 'group' => 'Putatan', 'latitude' => 14.4009444, 'longitude' => 121.0420],
            ['id' => 12, 'name' => 'Putatan Lakeview HC', 'location_id' => 7, 'oid' => 'OID_04', 'beacon_id' => '4', 'group' => 'Putatan', 'latitude' => 14.3948451, 'longitude' => 121.0420],
            ['id' => 13, 'name' => 'Putatan Brgy Hall', 'location_id' => 7, 'oid' => 'OID_05', 'beacon_id' => '5', 'group' => 'Putatan', 'latitude' => 14.3948969, 'longitude' => 121.0420],
            ['id' => 14, 'name' => 'Putatan City Hall', 'location_id' => 7, 'oid' => 'OID_19', 'beacon_id' => '19', 'group' => 'Putatan', 'latitude' => 14.3949248, 'longitude' => 121.0420],
            ['id' => 15, 'name' => 'Cupang Elem School', 'location_id' => 5, 'oid' => 'OID_09', 'beacon_id' => '9', 'group' => 'Cupang', 'latitude' => 14.4303812, 'longitude' => 121.0520],
            ['id' => 16, 'name' => 'Tunasan Sports Complex', 'location_id' => 9, 'oid' => 'OID_10', 'beacon_id' => '10', 'group' => 'Tunasan', 'latitude' => 14.3830497, 'longitude' => 121.0450],
            ['id' => 17, 'name' => 'Tunasan City Jail', 'location_id' => 9, 'oid' => 'OID_02', 'beacon_id' => '2', 'group' => 'Tunasan', 'latitude' => 14.3765671, 'longitude' => 121.0450],
            ['id' => 18, 'name' => 'Sucat Health Center', 'location_id' => 8, 'oid' => 'OID_17', 'beacon_id' => '17', 'group' => 'Sucat', 'latitude' => 14.4571199, 'longitude' => 121.0480],
            ['id' => 19, 'name' => 'Buli Health Center', 'location_id' => 4, 'oid' => 'OID_15', 'beacon_id' => '15', 'group' => 'Buli', 'latitude' => 14.4424017, 'longitude' => 121.0480],
            ['id' => 20, 'name' => 'Poblacion Itaas Elem School', 'location_id' => 6, 'oid' => 'OID_21', 'beacon_id' => '21', 'group' => 'Poblacion', 'latitude' => 14.3862627, 'longitude' => 121.0425],
        ];

        foreach ($beacons as $beacon) {
            DB::table('beacons')->insert([
                'id' => $beacon['id'],
                'name' => $beacon['name'],
                'location_id' => $beacon['location_id'],
                'oid' => $beacon['oid'],
                'beacon_id' => $beacon['beacon_id'],
                'group' => $beacon['group'],
                'latitude' => $beacon['latitude'],
                'longitude' => $beacon['longitude'],
                'status' => 0,
                'last_seen_at' => null,
                'is_door_open' => 0,
                'last_opened_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}