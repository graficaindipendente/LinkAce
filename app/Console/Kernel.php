<?php

namespace App\Console;

use App\Console\Commands\CheckLinksCommand;
use App\Console\Commands\RegisterUserCommand;
use App\Console\Commands\Setup\SetupCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel
 *
 * @package App\Console
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        RegisterUserCommand::class,
        CheckLinksCommand::class,
        SetupCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('links:check')->hourly();

        $schedule->command('queue:work --daemon --once')
            ->withoutOverlapping();

        if (env('BACKUP_ENABLED', false)) {
            $schedule->command('backup:clean')->daily()->at('01:00');
            $schedule->command('backup:run')->daily()->at('02:00');
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
