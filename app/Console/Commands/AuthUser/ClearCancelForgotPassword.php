<?php

namespace App\Console\Commands\AuthUser;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClearCancelForgotPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-cancel-forgot-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset cancel session user forgot password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::channel('cron.clean_cancel_forgot_password')->info('Start cleanup user cancel reset password...');
        $users = User::whereNotNull('verify_otp')
            ->where('otp_expired_at', '<=' , Carbon::now()->subDays(3)->toDateTimeString())
            ->get();
        Log::channel('cron.clean_cancel_forgot_password')->info('Total: ' . sizeof($users) . ' user cancel change password');
        foreach ($users as $user) {
            Password::deleteToken($user);
            $user->otp_expired_at = null;
            $user->verify_otp = null;
            $user->save();
            Log::channel('cron.clean_cancel_forgot_password')->info('Clean for user ' . $user->id . ' done.');
        }

        DB::table('password_reset_tokens')
            ->where('created_at', '<=', Carbon::now()->subDays(3)->toDateTimeString())
            ->delete();
        Log::channel('cron.clean_cancel_forgot_password')->info('Cleanup successfully !');
    }
}
