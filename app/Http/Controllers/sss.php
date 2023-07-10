<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function monthlyfilter($monthStart, $monthEnd, $year) {
        $mstart = date('m', strtotime($monthStart));
        $mend = date('m', strtotime($monthEnd));
        $cols = '';
        $select_cols = '';

        $colsResult = DB::select("
            SELECT
                GROUP_CONCAT(DISTINCT
                    CONCAT(
                        'IFNULL(SUM(CASE WHEN subquery.month = \"', months.month_name, '\" THEN subquery.qty ELSE 0 END), 0) AS month', months.month_name
                    ) ORDER BY months.month
                ) AS cols
            FROM (
                WITH RECURSIVE months AS (
                    SELECT 1 AS month
                    UNION ALL
                    SELECT month + 1
                    FROM months
                    WHERE month < $mend
                )
                SELECT DATE_FORMAT(DATE '2023-01-01' + INTERVAL (month - 1) MONTH, '%M') AS month_name, month
                FROM months
                WHERE month >= $mstart
            ) months;
        ");


        $cols = $colsResult[0]->cols;

        $select_cols = "subquery.service_center, subquery.services, $cols";

        $query = "
            SELECT $select_cols
            FROM (
                SELECT COUNT(b.name) AS qty, c.name AS service_center, MONTHNAME(a.booking_date) AS month, b.name AS services
                FROM bookings a
                INNER JOIN services b ON a.services_id = b.id
                INNER JOIN service_centers c ON a.service_center_id = c.id
                WHERE MONTH(a.booking_date) BETWEEN $mstart AND $mend AND YEAR(a.booking_date) = $year
                GROUP BY c.name, MONTHNAME(a.booking_date), b.name
                ORDER BY c.name, b.name, MONTHNAME(a.booking_date) ASC
            ) AS subquery
            GROUP BY subquery.service_center, subquery.services
        ";

        $result = DB::select($query);
    
        return response($result, 200);
    }

    public function monthDay($month, $year) {
        $month = date('m', strtotime($month));
       
        $DaysInMonth = DB::select("SELECT DAY(LAST_DAY(CONCAT($year, '-', LPAD($month, 2, '0'), '-01'))) AS DaysInMonth;");
        $day = $DaysInMonth[0]->DaysInMonth; // Extract the value from the array
 
        $cols = '';
        $select_cols = '';

        // Generate the column names dynamically
        $colsResult = DB::select("
            SELECT GROUP_CONCAT(DISTINCT
                CONCAT(
                    'IFNULL(SUM(CASE WHEN b.day = ', days.day, ' THEN b.qty ELSE 0 END), 0) AS day', days.day
                ) ORDER BY days.day ASC SEPARATOR ', ') AS cols
            FROM (
            WITH RECURSIVE days AS (
                SELECT 1 AS day
                UNION ALL
                SELECT day + 1
                FROM days
                WHERE day < $day
            )
                SELECT day
                FROM days
            ) days 
        ");

        if (!empty($colsResult)) {
            $cols = $colsResult[0]->cols;
        }
    
        // Generate the SELECT statement with dynamic columns
        $select_cols = "b.service_center, b.services, $cols";
 
        // Execute the final query
        $query = "
            SELECT $select_cols
            FROM (
                SELECT COUNT(b.name) AS qty, c.name AS service_center, DAY(a.booking_date) AS day, b.name AS services
                FROM bookings a
                INNER JOIN services b ON a.services_id = b.id
                INNER JOIN service_centers c ON a.service_center_id = c.id
                WHERE MONTH(a.booking_date) = $month AND YEAR(a.booking_date) = $year
                GROUP BY c.name, DAY(a.booking_date), b.name
            ) b
            GROUP BY b.service_center, b.services
            ORDER BY b.service_center, b.services
        ";

        $result = DB::select($query);

        return response($result, 200);
    }

    public function monthly() {
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;

        $DaysInMonth = DB::select("SELECT DAY(LAST_DAY(CONCAT($year, '-', LPAD($month, 2, '0'), '-01'))) AS DaysInMonth;");
        $day = $DaysInMonth[0]->DaysInMonth; // Extract the value from the array

        $cols = '';
        $select_cols = '';

        // Generate the column names dynamically
        $colsResult = DB::select("
            SELECT GROUP_CONCAT(DISTINCT
                CONCAT(
                    'IFNULL(SUM(CASE WHEN b.day = ', days.day, ' THEN b.qty ELSE 0 END), 0) AS day', days.day
                ) ORDER BY days.day ASC SEPARATOR ', ') AS cols
            FROM (
            WITH RECURSIVE days AS (
                SELECT 1 AS day
                UNION ALL
                SELECT day + 1
                FROM days
                WHERE day < $day
            )
                SELECT day
                FROM days
            ) days 
        ");

        if (!empty($colsResult)) {
            $cols = $colsResult[0]->cols;
        }
    
        // Generate the SELECT statement with dynamic columns
        $select_cols = "b.service_center, b.services, $cols";
 
        // Execute the final query
        $query = "
            SELECT $select_cols
            FROM (
                SELECT COUNT(b.name) AS qty, c.name AS service_center, DAY(a.booking_date) AS day, b.name AS services
                FROM bookings a
                INNER JOIN services b ON a.services_id = b.id
                INNER JOIN service_centers c ON a.service_center_id = c.id
                WHERE MONTH(a.booking_date) = $month AND YEAR(a.booking_date) = $year
                GROUP BY c.name, DAY(a.booking_date), b.name
            ) b
            GROUP BY b.service_center, b.services
            ORDER BY b.service_center, b.services
        ";

        $result = DB::select($query);

        return response($result, 200);
    }
    /**
     * Display a listing of the services for today.
     */
    public function today() {
        $current_day = date('Y/m/d', time()); 

        $today = DB::select("SELECT
                    b.name AS services,
                    c.name AS service_center,
                    COUNT(CASE WHEN a.time = '08:00' THEN b.name END) AS qty_08_00,
                    COUNT(CASE WHEN a.time = '08:30' THEN b.name END) AS qty_08_30,
                    COUNT(CASE WHEN a.time = '09:00' THEN b.name END) AS qty_09_00,
                    COUNT(CASE WHEN a.time = '09:30' THEN b.name END) AS qty_09_30,
                    COUNT(CASE WHEN a.time = '10:00' THEN b.name END) AS qty_10_00,
                    COUNT(CASE WHEN a.time = '10:30' THEN b.name END) AS qty_10_30,
                    COUNT(CASE WHEN a.time = '11:00' THEN b.name END) AS qty_11_00,
                    COUNT(CASE WHEN a.time = '11:30' THEN b.name END) AS qty_11_30,
                    COUNT(CASE WHEN a.time = '12:00' THEN b.name END) AS qty_12_00,
                    COUNT(CASE WHEN a.time = '12:30' THEN b.name END) AS qty_12_30,
                    COUNT(CASE WHEN a.time = '13:00' THEN b.name END) AS qty_13_00,
                    COUNT(CASE WHEN a.time = '13:30' THEN b.name END) AS qty_13_30,
                    COUNT(CASE WHEN a.time = '14:00' THEN b.name END) AS qty_14_00,
                    COUNT(CASE WHEN a.time = '14:30' THEN b.name END) AS qty_14_30,
                    COUNT(CASE WHEN a.time = '15:00' THEN b.name END) AS qty_15_00,
                    COUNT(CASE WHEN a.time = '15:30' THEN b.name END) AS qty_15_30,
                    COUNT(CASE WHEN a.time = '16:00' THEN b.name END) AS qty_16_00,
                    COUNT(CASE WHEN a.time = '16:30' THEN b.name END) AS qty_16_30,
                    COUNT(CASE WHEN a.time = '17:00' THEN b.name END) AS qty_17_00,
                    COUNT(CASE WHEN a.time = '17:30' THEN b.name END) AS qty_17_30,
                    COUNT(CASE WHEN a.time = '18:00' THEN b.name END) AS qty_18_00,
                    COUNT(CASE WHEN a.time = '18:30' THEN b.name END) AS qty_18_30,
                    COUNT(CASE WHEN a.time = '19:00' THEN b.name END) AS qty_19_00
                FROM
                    bookings a
                    INNER JOIN services b ON a.services_id = b.id
                    INNER JOIN service_centers c ON a.service_center_id = c.id
                WHERE
                    booking_date = '$current_day'
                GROUP BY
                    b.name,
                    c.name
                ORDER BY
                    a.time
            ");

            return response($today, 200);
    }

    /**
     * Display a listing of the services for yearly.
     */
    public function yearly() {
        $yearly = DB::select("
        SELECT
            c.name AS services,
            b.name AS service_center,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 1 THEN 1 ELSE 0 END) AS INT) AS January,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 2 THEN 1 ELSE 0 END) AS INT) AS February,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 3 THEN 1 ELSE 0 END) AS INT) AS March,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 4 THEN 1 ELSE 0 END) AS INT) AS April,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 5 THEN 1 ELSE 0 END) AS INT) AS May,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 6 THEN 1 ELSE 0 END) AS INT) AS June,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 7 THEN 1 ELSE 0 END) AS INT) AS July,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 8 THEN 1 ELSE 0 END) AS INT) AS August,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 9 THEN 1 ELSE 0 END) AS INT) AS September,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 10 THEN 1 ELSE 0 END) AS INT) AS October,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 11 THEN 1 ELSE 0 END) AS INT) AS November,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 12 THEN 1 ELSE 0 END) AS INT) AS December
        FROM
            bookings a
            INNER JOIN service_centers b ON a.service_center_id = b.id
            INNER JOIN services c ON a.services_id = c.id
        GROUP BY
            services,
            service_center
        ORDER BY
            services;
        ");

        return response($yearly, 200);
    }

    public function yearlyfilter($yearStart, $yearEnd) {
        $cols = '';
        $select_cols = '';

        $colsResult = DB::select("
            SELECT
                GROUP_CONCAT(DISTINCT
                    CONCAT(
                        'IFNULL(SUM(CASE WHEN subquery.year = ', years.year, ' THEN subquery.qty ELSE 0 END), 0) AS year', years.year
                    ) ORDER BY years.year
                ) AS cols
            FROM (
                WITH RECURSIVE years AS (
                    SELECT 1 AS year
                    UNION ALL
                    SELECT year + 1
                    FROM years
                    WHERE year < $yearEnd
                )
                SELECT  year
                FROM years
                WHERE year >= $yearStart
            ) years;
        ");


        $cols = $colsResult[0]->cols;

        $select_cols = "subquery.service_center, subquery.services, $cols";
 
        $query = "
            SELECT $select_cols
            FROM (
                SELECT COUNT(b.name) AS qty, c.name AS service_center, year(a.booking_date) AS year, b.name AS services
                FROM bookings a
                INNER JOIN services b ON a.services_id = b.id
                INNER JOIN service_centers c ON a.service_center_id = c.id
                WHERE year(a.booking_date) between $yearStart and $yearEnd
                GROUP BY c.name, year(a.booking_date), b.name
                ORDER BY c.name, b.name, year(a.booking_date)
            ) AS subquery
            GROUP BY subquery.service_center, subquery.services
        ";

        $result = DB::select($query);
    
        return response($result, 200);
    }

    public function yearMonth($year) {
        $months = DB::select("
        SELECT
            c.name AS services,
            b.name AS service_center,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 1 THEN 1 ELSE 0 END) AS INT) AS January,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 2 THEN 1 ELSE 0 END) AS INT) AS February,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 3 THEN 1 ELSE 0 END) AS INT) AS March,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 4 THEN 1 ELSE 0 END) AS INT) AS April,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 5 THEN 1 ELSE 0 END) AS INT) AS May,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 6 THEN 1 ELSE 0 END) AS INT) AS June,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 7 THEN 1 ELSE 0 END) AS INT) AS July,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 8 THEN 1 ELSE 0 END) AS INT) AS August,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 9 THEN 1 ELSE 0 END) AS INT) AS September,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 10 THEN 1 ELSE 0 END) AS INT) AS October,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 11 THEN 1 ELSE 0 END) AS INT) AS November,
            CAST(SUM(CASE WHEN MONTH(a.booking_date) = 12 THEN 1 ELSE 0 END) AS INT) AS December
        FROM
            bookings a
            INNER JOIN service_centers b ON a.service_center_id = b.id
            INNER JOIN services c ON a.services_id = c.id
            WHERE YEAR(a.booking_date) = $year
        GROUP BY
            services,
            service_center
        ORDER BY
            services;
        ");

        return response($months, 200);
    }
}
