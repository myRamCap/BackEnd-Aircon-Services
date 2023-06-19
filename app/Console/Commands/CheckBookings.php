<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-bookings';

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
        $bookings = Booking::where('booking_date', '=', Carbon::today()->toDateString())
                    ->whereRaw("TIMESTAMPDIFF(MINUTE, CONCAT(booking_date, ' ', time), NOW()) >= 15")
                    ->where('status', '=', 'Up Coming')
                    ->get();

        foreach ($bookings as $booking) {
            $booking->status = 'missed';
            $booking->save();
        }
    }
}
