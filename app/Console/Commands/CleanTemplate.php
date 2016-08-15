<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

// @codingStandardsIgnoreFile
class CleanTemplate extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'clean:template {--f|force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans the example files out of the project';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->option('force')) {
            $resource = true;
            $docs = true;
            $dbReset = true;
            $migrations = true;
            $seeds = true;
            $route = true;
            $test = true;
        } else {
            $resource = $this->confirm('Remove example resource?', true);
            $docs = $this->confirm('Remove example api docs?', true);
            $dbReset = $this->confirm('Reset database migrations?', true);
            $migrations = $this->confirm(
                'Remove example database migration?',
                true
            );
            $seeds = $this->confirm('Remove example database seed?', true);
            $route = $this->confirm('Remove example route?', true);
            $test = $this->confirm('Remove example test?', true);
        }

        if ($resource) {
            $resourceFilename = base_path('app/Quote.php');
            $resourceSchemaFilename = base_path('app/Schemas/QuoteSchema.php');
            $resourceControllerFilename = base_path('app/Http/Controllers/Api/QuoteController.php');
            if ($this->deleteFile($resourceFilename, 'Example model')) {
                $this->info('Removing example model.');
            }
            if ($this->deleteFile($resourceSchemaFilename, 'Example model schema')) {
                $this->info('Removing example model schema.');
            }
            if ($this->deleteFile($resourceControllerFilename, 'Example controller')) {
                $this->info('Removing example controller.');
            }
        }

        if ($docs) {
            $docsFile = base_path('resources/docs/api-documentation.apib');
            $resourceDocsDir = base_path('resources/docs/quote');
            if ($this->deleteDirectory($resourceDocsDir, 'Example docs directory')) {
                $this->info('Removing example docs directory');
            }
            $this->removeLineContaining($docsFile, 'Quote');
        }

        if ($dbReset) {
            $this->info('Resetting database migrations...');
            $dbResetProcess = new Process('docker-compose run --rm fpm php artisan migrate:reset');
            $dbResetProcess->run();
        }

        if ($migrations) {
            $migrationFilename = database_path('migrations/2016_03_16_122149_create_quotes_table.php');
            if ($this->deleteFile($migrationFilename, 'Example migration')) {
                $this->info('Removing example database migration...');
            }
        }

        if ($seeds) {
            $seedFilename = database_path('seeds/QuotesTableSeeder.php');
            if ($this->deleteFile($seedFilename, 'Example seed')) {
                $this->info('Removing example database seed...');
            }

            $this->info('Altering DatabaseSeeder file...');
            $seederFilename = database_path('seeds/DatabaseSeeder.php');
            $this->removeLineContaining($seederFilename, 'QuotesTableSeeder');
        }

        if ($route) {
            $this->info('Removing example route...');
            $this->removeLineContaining(
                base_path('app/Http/routes.php'),
                'quotes'
            );
        }

        if ($test) {
            $acceptanceFilename = base_path('tests/acceptance/QuoteTest.php');
            if ($this->deleteFile($acceptanceFilename, 'Acceptance test')) {
                $this->info('Removing example tests...');
            }
        }

        $this->info('Replacing README.md...');
        $system = new Filesystem();
        if ($this->deleteFile(base_path('README.md'))) {
            $system->move(base_path('README_DEFAULT.md'), base_path('README.md'));
        }

        $this->info('Removing Travis CI notifications...');
        $this->removeTravisNotification();

        $this->info('Removing LGTM MAINTAINERS file...');
        $this->deleteFile(base_path('MAINTAINERS'));

        $this->info('Removing example Rancher configuration...');
        $this->deleteDirectory(base_path('infrastructure/rancher'));

        $this->info('Removing this command...');
        $this->removeLineContaining(base_path('app/Console/Kernel.php'), 'CleanTemplate');
        $this->deleteFile(base_path('app/Console/Commands/CleanTemplate.php'));

        $this->warn("Review links in README.md and CONTRIBUTING.md to make sure they're relevant to this project");
    }

    private function deleteFile($filename, $type = null)
    {
        $system = new Filesystem();
        if ($system->exists($filename)) {
            $system->delete($filename);
        } else {
            $this->warn("$type already deleted.");
            return false;
        }
        return true;
    }

    private function deleteDirectory($dirname, $type = null)
    {
        $system = new Filesystem();
        if ($system->exists($dirname)) {
            $system->deleteDirectory($dirname);
        } else {
            $this->warn("$type already deleted");
            return false;
        }
        return true;
    }

    private function removeLineContaining($filename, $blacklist)
    {
        $system = new Filesystem();
        $rows = explode("\n", $system->get($filename));

        foreach ($rows as $key => $row) {
            if (preg_match("/($blacklist)/", $row)) {
                unset($rows[$key]);
            }
        }

        $system->put($filename, implode("\n", $rows));
    }

    private function removeTravisNotification()
    {
        $system = new Filesystem();
        $filename = base_path('.travis.yml');
        $blacklist = 'notifications';
        $rows = explode("\n", $system->get($filename));

        $section = false;
        foreach ($rows as $key => $row) {
            if ($section) {
                if (!empty($row) && preg_match('/\s/', $row[0])) {
                    unset($rows[$key]);
                } else {
                    $section = false;
                }
            } elseif (preg_match("/($blacklist)/", $row)) {
                $section = true;
                unset($rows[$key]);
            }
        }

        $system->put($filename, implode("\n", $rows));
    }
}
