<?php

namespace App\Console\Commands;

use App\Jobs\HappyBirthdayJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HappyBirthday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:happy-birthday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send happy birthday to all users. Run every days at 07h00 morning';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $currentDay = $today->format('d');
        $currentMonth = $today->format('m');
        $users = DB::table('users')
            ->whereDay('birthday', $currentDay)
            ->whereMonth('birthday', $currentMonth)
            ->get();

        foreach ($users as $user) {
            dispatch(new HappyBirthdayJob($user));
            Log::channel('cron.noti_happy_birthday_user')
                ->info('Sent happy birthday to user ' . $user->id . ' | ' . $user->email);
        }
    }
}
