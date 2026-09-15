<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use TomatoPHP\FilamentDocs\Facades\FilamentDocs;
use TomatoPHP\FilamentDocs\Models\Document;
use TomatoPHP\FilamentDocs\Models\DocumentTemplate;

/**
 * Demo templates and documents for tomatophp/filament-docs. Templates use the $USER_NAME var
 * registered in AppServiceProvider and the built-in $DATE and $UUID vars.
 */
class DemoDocsSeeder extends Seeder
{
    /**
     * @var array<int, array{name: string, icon: string, color: string, body: string}>
     */
    protected array $templates = [
        [
            'name' => 'Service Agreement',
            'icon' => 'heroicon-o-document-text',
            'color' => '#2563eb',
            'body' => '<h1>Service Agreement</h1><p>Reference: $UUID</p><p>This agreement is made on $DATE between TomatoPHP and <strong>$USER_NAME</strong>.</p><h2>1. Services</h2><p>TomatoPHP provides Filament plugin development, maintenance and support as described in the attached proposal.</p><h2>2. Payment</h2><p>Invoices are due within 30 days of issue.</p><h2>3. Term</h2><p>The agreement runs for twelve months and renews unless either party gives 30 days notice.</p><p>Signed: $USER_NAME</p>',
        ],
        [
            'name' => 'Offer Letter',
            'icon' => 'heroicon-o-briefcase',
            'color' => '#16a34a',
            'body' => '<h1>Offer Letter</h1><p>$DATE</p><p>Dear $USER_NAME,</p><p>We are happy to offer you the position of Laravel Developer at TomatoPHP, starting next month.</p><p>Please sign and return this letter to confirm.</p>',
        ],
        [
            'name' => 'Certificate of Completion',
            'icon' => 'heroicon-o-academic-cap',
            'color' => '#db2777',
            'body' => '<h1>Certificate of Completion</h1><p>This certifies that <strong>$USER_NAME</strong> completed the Filament Plugins workshop on $DATE.</p><p>Certificate ID: $UUID</p>',
        ],
    ];

    /**
     * @var array<int, array{0: int, 1: string}>
     */
    protected array $documents = [
        [0, 'amira.hassan@example.com'],
        [0, 'omar.khaled@example.com'],
        [1, 'lina.farouk@example.com'],
        [2, 'youssef.nabil@example.com'],
    ];

    public function run(): void
    {
        $templates = collect($this->templates)->map(function (array $data): DocumentTemplate {
            $template = DocumentTemplate::query()->updateOrCreate(['name' => $data['name']], [
                'body' => $data['body'],
                'icon' => $data['icon'],
                'color' => $data['color'],
                'is_active' => true,
            ]);

            FilamentDocs::create($template);

            return $template;
        });

        foreach ($this->documents as $index => [$templateIndex, $email]) {
            $user = User::query()->where('email', $email)->first();
            $template = $templates[$templateIndex];

            if (! $user) {
                continue;
            }

            Document::query()->updateOrCreate(['ref' => 'DOC-2026-00'.($index + 1)], [
                'document_template_id' => $template->id,
                'body' => str(FilamentDocs::body($template->id))->replace('$USER_NAME', $user->name)->toString(),
                'is_send' => $index % 2 === 0,
                'model_type' => User::class,
                'model_id' => $user->id,
            ]);
        }
    }
}
