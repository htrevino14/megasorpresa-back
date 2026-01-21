<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Exception;
use OpenApi\Generator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class GenerateOpenApiDocs extends Command
{
    protected $signature = 'openapi:generate';
    protected $description = 'Generate OpenAPI documentation from annotations';

    public function handle()
    {
        $this->info('Generating OpenAPI documentation...');

        try {
            // Create finder for app directory
            $finder = new Finder();
            $finder->in(base_path('app'))
                ->name('*.php');

            // Generate OpenAPI documentation
            $generator = new Generator();
            $this->info('Scanning for annotations...');
            $openapi = $generator->generate($finder->getIterator(), null, false);

            $this->info('Annotations scanned. Building spec...');

            // Ensure output directory exists
            $docDir = storage_path('api-docs');
            if (!is_dir($docDir)) {
                mkdir($docDir, 0755, true);
            }

            // Debug: Check if openapi has content
            if ($openapi) {
                $pathCount = $openapi->paths ? count((array)$openapi->paths) : 0;
                $this->info('OpenAPI object created with ' . $pathCount . ' paths');
                
                // Check specific properties
                $this->info('Has info: ' . ($openapi->info ? 'yes' : 'no'));
                $this->info('Has servers: ' . ($openapi->servers ? 'yes' : 'no'));
                $this->info('Has paths: ' . ($openapi->paths ? 'yes' : 'no'));
                $this->info('Has components: ' . ($openapi->components ? 'yes' : 'no'));
                $this->info('Has tags: ' . ($openapi->tags ? 'yes' : 'no'));
                
                if ($openapi->info) {
                    $this->info('Info title: ' . ($openapi->info->title ?? 'N/A'));
                    $this->info('Info version: ' . ($openapi->info->version ?? 'N/A'));
                    $this->info('Info object dump: ' . json_encode($openapi->info, JSON_PRETTY_PRINT));
                }
                
                // Check paths
                if ($openapi->paths) {
                    $this->info('Paths object dump: ' . json_encode($openapi->paths, JSON_PRETTY_PRINT));
                }
            } else {
                $this->warn('OpenAPI object is null');
            }

            // Write JSON using the OpenApi object's toJson() method
            $jsonPath = $docDir . '/api-docs.json';
            file_put_contents($jsonPath, $openapi->toJson());
            $this->info("✓ Created: $jsonPath (" . filesize($jsonPath) . " bytes)");

            // Write YAML
            $yamlPath = $docDir . '/api-docs.yaml';
            file_put_contents($yamlPath, $openapi->toYaml());
            $this->info("✓ Created: $yamlPath (" . filesize($yamlPath) . " bytes)");

            // Create spec.yaml as copy of api-docs.yaml
            $specPath = $docDir . '/api-spec.yaml';
            copy($yamlPath, $specPath);
            $this->info("✓ Created: $specPath");

            $this->info('✓ OpenAPI documentation generated successfully!');

            return self::SUCCESS;

        } catch (Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return self::FAILURE;
        }
    }
}
