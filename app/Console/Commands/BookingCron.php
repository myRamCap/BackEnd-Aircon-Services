<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BookingCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Booking:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        info("Cron Job running at ". now());

        $bookings = Booking::where('booking_date', '=', Carbon::today()->toDateString())
                    ->whereRaw("TIMESTAMPDIFF(MINUTE, CONCAT(booking_date, ' ', time), NOW()) >= 15")
                    ->where('status', '=', 'Up Coming')
                    ->get();

        foreach ($bookings as $booking) {
            $booking->status = 'Missed';
            $booking->save();
        }

        return 0;
    }
}
