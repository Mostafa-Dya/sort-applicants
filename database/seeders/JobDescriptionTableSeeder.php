<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JobDescriptionTable;

class JobDescriptionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobDescriptions = [
            [
                'status' => 'Active',
                'category' => 'Technology',
                'public_entity' => 'Ministry of Health',
                'sub_entity' => 'Emergency Center',
                'governorate' => 'Cairo',
                'job_title' => 'Software Engineer',
                'work_centers' => 5,
                'assignees' => 10,
                'vacancies' => 2,
                'card_number' => '123456789',
                'specialization' => 'Information Technology',
                'record_entry' => 'User 1',
                'entry_date' => '2023-01-15',
                'last_modifier' => 'Manager',
                'modification_date' => '2023-02-20',
                'audited_by' => 'Head of Department',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status' => 'Active2',
                'category' => 'Technology2',
                'public_entity' => 'Ministry of Health2',
                'sub_entity' => 'Emergency Center2',
                'governorate' => 'Cairo2',
                'job_title' => 'Software Engineer2',
                'work_centers' => 5,
                'assignees' => 10,
                'vacancies' => 2,
                'card_number' => '1234567892',
                'specialization' => 'Information Technology2',
                'record_entry' => 'User 2',
                'entry_date' => '2023-01-15',
                'last_modifier' => 'Manager',
                'modification_date' => '2023-02-20',
                'audited_by' => 'Head of Department',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status' => 'Active3',
                'category' => 'Technology3',
                'public_entity' => 'Ministry of Health3',
                'sub_entity' => 'Emergency Center3',
                'governorate' => 'Cairo3',
                'job_title' => 'Software Engineer',
                'work_centers' => 5,
                'assignees' => 10,
                'vacancies' => 2,
                'card_number' => '123456789',
                'specialization' => 'Information Technology',
                'record_entry' => 'User 3',
                'entry_date' => '2023-01-15',
                'last_modifier' => 'Manager',
                'modification_date' => '2023-02-20',
                'audited_by' => 'Head of Department',
                'created_at' => now(),
                'updated_at' => now(),
            ]
            ];
            JobDescriptionTable::insert($jobDescriptions);

    }
}
