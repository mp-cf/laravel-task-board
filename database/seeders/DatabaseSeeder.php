<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $boards = [
            [
                'name' => 'Website Redesign',
                'tasks' => [
                    ['title' => 'Design new homepage mockup', 'status' => 'done'],
                    ['title' => 'Update color palette', 'status' => 'done'],
                    ['title' => 'Implement responsive nav', 'status' => 'in_progress'],
                    ['title' => 'Write copy for About page', 'status' => 'in_progress'],
                    ['title' => 'SEO audit', 'status' => 'todo'],
                    ['title' => 'Performance optimisation', 'status' => 'todo'],
                ],
            ],
            [
                'name' => 'API v2 Launch',
                'tasks' => [
                    ['title' => 'Define OpenAPI spec', 'status' => 'done'],
                    ['title' => 'Implement authentication endpoints', 'status' => 'in_progress'],
                    ['title' => 'Rate limiting middleware', 'status' => 'todo'],
                    ['title' => 'Write integration tests', 'status' => 'todo'],
                    ['title' => 'Update developer docs', 'status' => 'todo'],
                ],
            ],
            [
                'name' => 'Infrastructure',
                'tasks' => [
                    ['title' => 'Set up Railway project', 'status' => 'in_progress'],
                    ['title' => 'Configure PostgreSQL service', 'status' => 'done'],
                    ['title' => 'Configure Redis service', 'status' => 'done'],
                    ['title' => 'Set up Horizon worker process', 'status' => 'todo'],
                    ['title' => 'Configure health check monitoring', 'status' => 'todo'],
                ],
            ],
        ];

        foreach ($boards as $boardData) {
            $board = Board::create(['name' => $boardData['name']]);

            foreach ($boardData['tasks'] as $taskData) {
                $board->tasks()->create($taskData);
            }
        }
    }
}
