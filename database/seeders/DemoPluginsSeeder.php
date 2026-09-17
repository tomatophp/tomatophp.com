<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TomatoPHP\FilamentPlugins\Models\Table;
use TomatoPHP\FilamentPlugins\Models\TableCol;

/**
 * Demo rows for tomatophp/filament-plugins: one table-builder record with its columns so the tables
 * list and edit pages show data. It is never migrated or generated, so no files or database tables
 * are created; on the public demo keep the generator off with ->allowGenerator(false).
 */
class DemoPluginsSeeder extends Seeder
{
    /**
     * @var array<int, array{name: string, type: string, length?: int, nullable?: bool, unique?: bool, primary?: bool, auto_increment?: bool, unsigned?: bool, comment?: string}>
     */
    protected array $columns = [
        ['name' => 'id', 'type' => 'bigInteger', 'primary' => true, 'auto_increment' => true, 'unsigned' => true],
        ['name' => 'title', 'type' => 'string', 'length' => 255, 'comment' => 'Post title'],
        ['name' => 'slug', 'type' => 'string', 'length' => 255, 'unique' => true],
        ['name' => 'body', 'type' => 'longText', 'nullable' => true],
        ['name' => 'published_at', 'type' => 'timestamp', 'nullable' => true],
    ];

    public function run(): void
    {
        if (Table::query()->where('name', 'blog_posts')->exists()) {
            return;
        }

        $table = Table::query()->create([
            'module' => 'Blog',
            'name' => 'blog_posts',
            'comment' => 'Posts for the demo Blog module',
            'timestamps' => true,
            'soft_deletes' => true,
            'migrated' => false,
            'generated' => false,
        ]);

        foreach ($this->columns as $order => $column) {
            TableCol::query()->create([
                'table_id' => $table->id,
                'order' => $order,
                'name' => $column['name'],
                'type' => $column['type'],
                'length' => $column['length'] ?? null,
                'comment' => $column['comment'] ?? null,
                'nullable' => $column['nullable'] ?? false,
                'unique' => $column['unique'] ?? false,
                'primary' => $column['primary'] ?? false,
                'auto_increment' => $column['auto_increment'] ?? false,
                'unsigned' => $column['unsigned'] ?? false,
                'index' => false,
                'foreign' => false,
                'foreign_on_delete_cascade' => false,
            ]);
        }
    }
}
