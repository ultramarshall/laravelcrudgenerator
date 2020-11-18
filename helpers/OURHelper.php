<?php

namespace Helpers;

use DB;
use MITBooster;
use DateTime;
use Illuminate\Support\Facades\Log;

class OURHelper
{
    public static function setPerfomance($id)
    {
        $ass = DB::table('our_assemblies')->find($id);
        $booking = DB::table('our_bookings_detail')->find($ass->our_bookings_detail_id);
        $actual_start = DB::table('our_assemblies')->where('our_bookings_detail_id', $booking->id)->min('start_date');
        $actual_end = DB::table('our_assemblies')->where('our_bookings_detail_id', $booking->id)->max('end_date');
        $qty_prod = DB::table('our_assemblies')->where('our_bookings_detail_id', $booking->id)->sum('qty_prod');
        $qty_reject = DB::table('our_assemblies')->where('our_bookings_detail_id', $booking->id)->sum('qty_reject');

        $detail = DB::table('our_assemblies')
                ->selectRaw('SUM(TIME_TO_SEC(TIMEDIFF(end_date, start_date))) as diff')
                ->where('our_bookings_detail_id', $booking->id)
                ->where('type', '<>', 'CHANGE OVER')
                ->first();

        $work_hour2 = 0;
        $speed2 = 0;
        if ($detail != null) {
            $work_hour2 = $detail->diff/3600;
            if ($work_hour2 != 0) {
                $speed2 = $qty_prod/$work_hour2;
            }
            //dd($work_hour2);
        }

        $dt_hour = 0;
        $detail2 = DB::table('our_assemblies')
                ->selectRaw('SUM(TIME_TO_SEC(TIMEDIFF(end_date, start_date))) as diff')
                ->where('our_bookings_detail_id', $booking->id)
                ->where('type', '=', 'DOWN TIME')
                ->first();

        if ($detail2 != null) {
            $dt_hour = $detail2->diff/3600;
        }

        $ar = 0;
        if ($booking->speed != 0) {
            $ar = $speed2/$booking->speed*100;
        }

        $rft = 0;
        if ($qty_prod != 0) {
            $rft = ($qty_prod-$qty_reject)/$qty_prod*100;
        }

        $dt = 0;
        if ($work_hour2 != 0) {
            $dt = ($detail2->diff/3600)/$work_hour2*100;
        }

        $detail3 = DB::table('our_assemblies')
            ->selectRaw('SUM(TIME_TO_SEC(TIMEDIFF(end_date, start_date))) as diff')
            ->where('our_bookings_detail_id', $booking->id)
            ->where('type', '=', 'CHANGE OVER')
            ->first();

        $co = 0;
        $co_hour = 0;
        $add_co = 0;
        if ($detail3 != null) {
            $co_hour = $detail3->diff/3600;
            $changeOver = MITBooster::getSetting('change_over');
            $changeOver = $changeOver/60;
            if ($detail3->diff != null && $detail3->diff != 0) {
                $co = $co_hour/$changeOver*100;
            }
            if($co_hour > $changeOver)
                $add_co = $co_hour - $changeOver;
            //dd($detail3->diff);
            //dd(411/720);
        }

        $detail4 = DB::table('our_assemblies')
            ->selectRaw('SUM(TIME_TO_SEC(TIMEDIFF(end_date, start_date))) as diff')
            ->where('our_bookings_detail_id', $booking->id)
            ->where('type', '=', 'ASSEMBLY')
            ->first();

        $ass_hour = 0;
        $work_hour3 = 0;
        $speed3 = 0;
        if ($detail4 != null) {
            $ass_hour = $detail4->diff/3600;
            // dd($qty_prod);
            $ass_hour = $ass_hour-$add_co;

            $work_hour3 = $ass_hour;
            if ($work_hour3 != 0) {
                $speed3 = $qty_prod/$work_hour3;
            }
        }

        $av = 0;
        $oee = 0;
        // dd($add_co);
        if ($work_hour2 != 0) {
            $av = $ass_hour/$work_hour2*100;
            $oee = $ass_hour/$work_hour2*100*$ar*$rft/10000;
        }

        // dd($oee);

        $last_status = DB::select(DB::raw("
			SELECT a.id, b.type, b.id as ass_id, b.end_date, b.reason
	 		FROM (
	 			SELECT a.our_bookings_detail_id as id, MAX(start_date) as start_date
	 			FROM our_assemblies as a
	 			GROUP BY a.our_bookings_detail_id) as a
	 		INNER JOIN our_assemblies as b on a.id = b.our_bookings_detail_id AND a.start_date = b.start_date
	 		WHERE b.our_bookings_detail_id = :id"), array('id' => $booking->id));

        $type = '';
        $ass_id = 0;
        $ass_date = null;
        $reason = '';
        if ($last_status != null && is_array($last_status) && count($last_status) > 0) {
            $type = $last_status[0]->type;
            $ass_id = $last_status[0]->ass_id;
            $ass_date = $last_status[0]->end_date;
            $reason = $last_status[0]->reason;
        }

        if ($booking->qty > $qty_prod) {
            $actual_end = null;
        }

        $data = [
            'actual_start' => $actual_start,
            'actual_end' => $actual_end,
            'result' => $qty_prod,
            'rejected' => $qty_reject,
            'work_hour2' => $work_hour2,
            'speed2' => $speed2,
            'work_hour3' => $work_hour3,
            'speed3' => $speed3,
            'av' => $av,
            'ar' => $ar,
            'rft' => $rft,
            'downtime' => $dt,
            'changeover' => $co,
            'oee' => $oee,
            'type' => $type,
            'ass_id' => $ass_id,
            'ass_date' => $ass_date,
            'reason' => $reason,
            'ass_hour' => $ass_hour,
            'dt_hour' => $dt_hour,
            'co_hour' => $co_hour,
        ];

        DB::table('our_bookings_detail')
            ->where('id', $booking->id)
            ->update($data);
    }
    public static function latestSchedule($id, $our_items_id, $is_same, $our_lines_id, $work_hour, $start_date, $prefered_date)
    {
        $latest = [];
        $latest['start_date'] = date('Y-m-d H:i:s');
        $latest['end_date'] = date('Y-m-d H:i:s');

        //Get Master Line
        $line = DB::table('our_lines')
            ->where('id', '=', $our_lines_id)
            ->select('start_date', 'our_shifts_id')
            ->first();

        $our_shifts_id = $line->our_shifts_id;

        $is_first = $start_date == null;
        if ($prefered_date != null) {
            $is_first = true;
            $start_date = $prefered_date;
        }


        $is_same_item = false;
        if ($start_date == null) {
            //Get Existing Production Process Before
            $last_booked = DB::table('our_bookings_detail')
                ->where('our_lines_id', '=', $our_lines_id)
                ->where('our_bookings_id', '<>', $id)
                ->max('end_date');
            $start_date = $last_booked == null ? $line->start_date : $last_booked;
            if ($last_booked != null) {
                $is_first = false;
            }


            //Check Same Item
            $last_booking = DB::table('our_bookings_detail')
                ->where('our_lines_id', '=', $our_lines_id)
                ->where('our_bookings_id', '<>', $id)
                ->where('end_date', $last_booked)
                ->first();
            //Log::info($last_booking);
    
            if ($last_booking->our_items_id == $our_items_id) {
                $is_same_item = true;
            }
        }

        $changeOver = 0;
        if (!$is_first) {
            $changeOver = MITBooster::getSetting('change_over');
        }

        if ($is_same_item == 'true' || $is_same == 'true') {
            // Log::info("======");
            $changeOver = 0;
        }
        // Log::info("------");
        // Log::info("is_same:".$is_same);
        // Log::info("is_same_item:".$is_same_item);
        // Log::info("start_date:".$start_date);
        // Log::info("our_items_id:".$our_items_id);
        // Log::info("changeOver:".$changeOver);

        // log::info($start_date);

        $details = [];

        $latest['start_date'] = self::startSchedule($start_date, $our_shifts_id, $our_lines_id, $changeOver);
        // dd($latest['start_date']);
        // Log::info('-------');
        // Log::info($latest['start_date']);
        if ($id != null && $id != 0) {
            DB::table('our_plans')->where('our_bookings_detail_id', '=', $id)->delete();
        }

        $latest['end_date'] = self::endScheduler($id, $latest['start_date'], (int)($work_hour * 3600), $our_shifts_id, $our_lines_id);
        // Log::info('$$$$$$');
        // Log::info($latest['end_date']);

        return $latest;
    }

    public static function startSchedule($date, $our_shifts_id, $our_lines_id, $changeOver)
    {
        // Log::info($date);
        $weekDay = date('w', strtotime($date));

        $working_time = DB::table('our_working_time')
            ->where('weekday', '=', $weekDay)
            ->where('our_shifts_id', '=', $our_shifts_id)
            ->first();

        //If Its Off Day
        if ($working_time == null) {
            return self::startOvertime($date, $our_shifts_id, $our_lines_id, $changeOver);
        }

        //If Its Holiday
        $holiday = DB::table('our_holiday')
            ->where('date', '=', date('Y-m-d', strtotime($date)))
            ->where('our_lines_id', '=', $our_lines_id)
            ->first();
        if ($holiday != null) {
            return self::startOvertime($date, $our_shifts_id, $our_lines_id, $changeOver);
        }
        
        if (date('H:i:s', strtotime($date)) == '00:00:00') {
            $hours = substr($working_time->start_hour, 0, 2);
            $minutes = substr($working_time->start_hour, 3, 2);

            $date = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($date)));
        }

        $is_add = true;
        $date = self::startBreak($date, $changeOver, $working_time->break_start1, $working_time->break_end1, $is_add);
        $date = self::startBreak($date, $changeOver, $working_time->break_start2, $working_time->break_end2, $is_add);
        $date = self::startBreak($date, $changeOver, $working_time->break_start3, $working_time->break_end3, $is_add);
        $date = self::startBreak($date, $changeOver, $working_time->break_start4, $working_time->break_end4, $is_add);
        $date = self::startBreak($date, $changeOver, $working_time->break_start5, $working_time->break_end5, $is_add);
        $date = self::startBreak($date, $changeOver, $working_time->break_start6, $working_time->break_end6, $is_add);

        $hours = 0;
        $minutes = 0;

        $result = date('Y-m-d H:i:s', strtotime($date));

        //Tambahan Check End Date
        $hours = substr($working_time->end_hour, 0, 2);
        $minutes = substr($working_time->end_hour, 3, 2);
        $days = 0;
        if ($hours == 0) {// && $minutes == 0)
            $days = 1;
        }

        $end_date = date('Y-m-d', strtotime($result));
        $end_date = date('Y-m-d H:i:s', strtotime('+'.$days.' day +'.$hours.' hour +'.$minutes.' minutes', strtotime($end_date)));

        // Log::info(date('Y-m-d H:i:s', strtotime($date)));
        // Log::info(date('Y-m-d H:i:s', strtotime($end_date)));

        if (date('H:i:s', strtotime($date)) < date('H:i:s', strtotime($working_time->start_hour))) {
            // dd('aaa');
            $date = date('Y-m-d', strtotime($date));
            $hours = substr($working_time->start_hour, 0, 2);
            $minutes = substr($working_time->start_hour, 3, 2);
            $result = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($date)));
        } elseif (date('Y-m-d H:i:s', strtotime($date)) > date('Y-m-d H:i:s', strtotime($end_date))) {
            // dd('bbb');
            $result = self::startOvertime($date, $our_shifts_id, $our_lines_id, $changeOver);
        }


        if (date('Y-m-d H:i:s', strtotime('+'.$changeOver.' minutes', strtotime($result))) > date('Y-m-d H:i:s', strtotime($end_date))) {
            // dd('aaaa');
            // $hours = substr($working_time->end_hour, 0, 2);
            // $minutes = substr($working_time->end_hour, 3, 2);
            // $date1 = date('Y-m-d', strtotime($result));
            // $date1 = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($date1)));
            
            // $diff = self::diffSeconds($result, $date1);
            // $diff = ($changeOver * 60);

            // Last Time
            // $result = date('Y-m-d', strtotime('+1 day', strtotime($result)));
            // $hours = substr($working_time->start_hour, 0, 2);
            // $minutes = substr($working_time->start_hour, 3, 2);
            // $result = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($result)));
            // $result = date('Y-m-d H:i:s', strtotime('+'.$changeOver.' minutes', strtotime($result)));

            //$date = date('Y-m-d H:i:s', strtotime('+'.$changeOver.' minutes', strtotime($result)));
        
            $result = self::startOvertime($date, $our_shifts_id, $our_lines_id, $changeOver);
        } else {
            // dd('bbbb');
            if ($is_add) {
                $result = date('Y-m-d H:i:s', strtotime('+'.$changeOver.' minutes', strtotime($result)));
            }
        }
        return $result;
    }

    public static function startBreak($date, $changeOver, $start, $end, &$is_add)
    {
        if ($start == null || $end == null) {
            return $date;
        }

        $hours = substr($start, 0, 2);
        $minutes = substr($start, 3, 2);
        $break_start = date('Y-m-d', strtotime($date));
        $break_start = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($break_start)));
        $break_start = new DateTime($break_start);

        $hours = substr($end, 0, 2);
        $minutes = substr($end, 3, 2);
        $break_end = date('Y-m-d', strtotime($date));
        $break_end = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($break_end)));
        $break_end = new DateTime($break_end);

        $current = new DateTime($date);
        if ($current >= $break_start && $current < $break_end) {
            $date = $break_end->format('Y-m-d H:i:s');
        }

        $current = new DateTime(date('Y-m-d H:i:s', strtotime('+'.$changeOver.' minutes', strtotime($date))));
        if ($current >= $break_start && $current < $break_end) {
            //$date1 = date('Y-m-d', strtotime($date));
            $diff = self::diffSeconds($date, $break_start->format('Y-m-d H:i:s'));
            $diff = ($changeOver * 60) - $diff;

            $date = $break_end->format('Y-m-d H:i:s');
            $date = date('Y-m-d H:i:s', strtotime('+'.$diff.' seconds', strtotime($date)));
            $is_add = false;
        }

        return $date;
    }

    public static function startOvertime($date, $our_shifts_id, $our_lines_id, $changeOver)
    {
        $overtime = DB::table('our_overtime')
            ->where('date', '=', date('Y-m-d', strtotime($date)))
            ->where('our_lines_id', '=', $our_lines_id)
            ->first();
        if ($overtime == null) {
            return self::startSchedule(date('Y-m-d', strtotime('+1 day', strtotime($date))), $our_shifts_id, $our_lines_id, $changeOver);
        }

        // Log::info(date('Y-m-d H:i:s', strtotime($date)));
        $overdate = date('Y-m-d', strtotime($overtime->date));
        $hours = substr($overtime->start_hour, 0, 2);
        $minutes = substr($overtime->start_hour, 3, 2);
        $overdate = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($overdate)));
        // Log::info($overdate);

        if (date('Y-m-d H:i:s', strtotime($date)) < $overdate) {
            // Log::info('a');
            // Log::info(date('Y-m-d H:i:s', strtotime($date)));
            // Log::info($overtime->start_hour);
            // Log::info(date('Y-m-d H:i:s', strtotime($overtime->start_hour)));
            // $date = date('Y-m-d', strtotime($date));
            // $hours = substr($overtime->start_hour, 0, 2);
            // $minutes = substr($overtime->start_hour, 3, 2);
            // return date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($date)));
            return $overdate;
        // } elseif (date('H:i:s', strtotime($date)) < date('H:i:s', strtotime($overdate))) {
        //     Log::info('b');
        // 	return $overdate;
        } else {
            // Log::info('c');
            // return self::startSchedule(date('Y-m-d', strtotime('+1 day', strtotime($date))), $our_shifts_id, $our_lines_id, $changeOver);
            return $date;
        }
    }

    public static function endScheduler($id, $date, $seconds, $our_shifts_id, $our_lines_id)
    {
        // Log::info('---->><<---');
        // Log::info($seconds);
        // Log::info($date);
        $initial_date = $date;
        $weekDay = date('w', strtotime($date));

        $working_time = DB::table('our_working_time')
            ->where('weekday', '=', $weekDay)
            ->where('our_shifts_id', '=', $our_shifts_id)
            ->first();

        $start_hour = date('H:i:s', strtotime($working_time->start_hour));
        $end_hour = date('H:i:s', strtotime($working_time->end_hour));

        //If Its Off Day
        if ($working_time == null) {
            if (!self::endOvertime($date, $start_date, $end_date, $seconds, $our_lines_id)) {
                // Log::info('----[[]]---');
                $hours = 0;//date('H', strtotime($start_date));
                $minutes = 0;//date('i', strtotime($start_date));

                $result_date = date('Y-m-d', strtotime($date));
                $result_date = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($result_date)));

                // Log::info($start_hour);
                // Log::info($result_date);

                // Log::info($start_date);
                // Log::info($end_date);
                // Log::info(date('Y-m-d H:i:s', strtotime('+1 day', strtotime($result_date))));

                return self::endScheduler($id, date('Y-m-d H:i:s', strtotime('+1 day', strtotime($result_date))), $seconds, $our_shifts_id, $our_lines_id);
            }

            // Log::info('----[[Working Time]]---');
            if (date('Y-m-d H:i:s', strtotime('+'.$seconds.' seconds', strtotime($start_date))) <= $end_date) {
                // Log::info('----[[Working Time1]]---');
                $hours = date('H', strtotime($start_date));
                $minutes = date('i', strtotime($start_date));
        
                $result_date = date('Y-m-d', strtotime($date));
                $result_date = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($result_date)));
                $result_date = date('Y-m-d H:i:s', strtotime('+'.$seconds.' seconds', strtotime($result_date)));
                return $result_date;
            }
            // Log::info('----[[Working Time2]]---');
            // Log::info($start_date);
            // Log::info($end_date);
            $working_time = db::table('our_working_time')
                ->where('our_shifts_id', $our_shifts_id)
                ->orderby('weekday')
                ->first();
            // Log::info(self::totalSeconds($working_time, $start_date, $end_date));
            $seconds -= self::totalSeconds($working_time, $start_date, $end_date);
            // Log::info($seconds);
                
            return self::endScheduler($id, $end_date, $seconds, $our_shifts_id, $our_lines_id);
        }

        //If Its Holiday
        $holiday = DB::table('our_holiday')
            ->where('date', '=', date('Y-m-d', strtotime($date)))
            ->where('our_lines_id', '=', $our_lines_id)
            ->first();
        if ($holiday != null) {
            if (!self::endOvertime($date, $start_date, $end_date, $seconds, $our_lines_id)) {
                return self::endScheduler($id, date('Y-m-d', strtotime('+1 day', strtotime($date))), $seconds, $our_shifts_id, $our_lines_id);
            }
        }

        //Check Time is not zero
        $check_time = date('H:i:s', strtotime($date));
        if ($check_time == '00:00:00') {
            // Log::info('check time');
            // Log::info($check_time);
            
            $hours = substr($start_hour, 0, 2);
            $minutes = substr($start_hour, 3, 2);

            $date = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($date)));
            $initial_date = $date;
        }
        // Log::info($date);

        $hours = substr($end_hour, 0, 2);
        $minutes = substr($end_hour, 3, 2);
        $days = 0;
        if ($hours == 0) {// && $minutes == 0)
            $days = 1;
        }

        $end_date = date('Y-m-d', strtotime($date));
        $end_date = date('Y-m-d H:i:s', strtotime('+'.$days.' day +'.$hours.' hour +'.$minutes.' minutes', strtotime($end_date)));

        // Log::info($date);
        // Log::info(date('Y-m-d H:i:s', strtotime('+'.$seconds.' seconds', strtotime($date))));

        $breakTime = self::breakTime($working_time, $date, date('Y-m-d H:i:s', strtotime('+'.$seconds.' seconds', strtotime($date))));
        // Log::info('------');
        // Log::info($breakTime);

        if (date('Y-m-d H:i:s', strtotime('+'.($seconds+$breakTime).' seconds', strtotime($date))) <= $end_date) {
            $hours = substr($start_hour, 0, 2);
            $minutes = substr($start_hour, 3, 2);
        
            $result_date = date('Y-m-d', strtotime($date));
            $result_date = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($result_date)));
            if ($result_date < $date) {
                $result_date = $date;
            }
            // $plan_date = date('Y-m-d H:i:s', strtotime('+'.($seconds).' seconds', strtotime($result_date)));
            $result_date = date('Y-m-d H:i:s', strtotime('+'.($seconds+$breakTime).' seconds', strtotime($result_date)));

            // Log::info('1111');
            // Log::info($initial_date);
            // Log::info($result_date);
            self::savePlans($id, $initial_date, $result_date, $working_time);
            return $result_date;
        }

        // Log::info($date);
        // Log::info($end_date);
        $seconds -= self::totalSeconds($working_time, $date, $end_date);
        // Log::info($seconds);
        $date = $end_date;

        // Log::info($date);
        if (!self::endOvertime($date, $start_date, $end_date, $seconds, $our_lines_id)) {
            $hours = substr($end_hour, 0, 2);
            $minutes = substr($end_hour, 3, 2);

            $day = 1;
            if ($hours == 0) {
                $day = 0;
            }
        
            $result_date = date('Y-m-d', strtotime($date));
            $result_date = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($result_date)));

            // Log::info('2222');
            // Log::info($day);
            // Log::info($end_hour);
            // Log::info($initial_date);
            // Log::info($result_date);
            self::savePlans($id, $initial_date, $result_date, $working_time);

            $hours = substr($start_hour, 0, 2);
            $minutes = substr($start_hour, 3, 2);

            $result_date = date('Y-m-d', strtotime($date));
            $result_date = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($result_date)));
            $result_date = date('Y-m-d H:i:s', strtotime('+'.$day.' day', strtotime($result_date)));
            
            // Log::info($initial_date);
            // Log::info($result_date);
            return self::endScheduler($id, $result_date, $seconds, $our_shifts_id, $our_lines_id);
        }
        if (date('Y-m-d H:i:s', strtotime('+'.$seconds.' seconds', strtotime($start_date))) <= $end_date) {
            $hours = date('H', strtotime($start_date));
            $minutes = date('i', strtotime($start_date));
    
            $result_date = date('Y-m-d', strtotime($date));
            $result_date = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($result_date)));
            $result_date = date('Y-m-d H:i:s', strtotime('+'.$seconds.' seconds', strtotime($result_date)));

            Log::info('3333');
            self::savePlans($id, $initial_date, $result_date, $working_time);
            return $result_date;
        }
        $seconds -= self::totalSeconds($working_time, $start_date, $end_date);

        $hours = substr($end_hour, 0, 2);
        $minutes = substr($end_hour, 3, 2);
    
        $result_date = date('Y-m-d', strtotime($date));
        $result_date = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($result_date)));
        
        Log::info('4444');
        self::savePlans($id, $initial_date, $result_date, $working_time);

        $hours = substr($start_hour, 0, 2);
        $minutes = substr($start_hour, 3, 2);
    
        $result_date = date('Y-m-d', strtotime($date));
        $result_date = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($result_date)));
        $result_date = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($result_date)));
        return self::endScheduler($id, $result_date, $seconds, $our_shifts_id, $our_lines_id);
    }

    public static function endOvertime(&$date, &$start_date, &$end_date, &$seconds, $our_lines_id)
    {
        $overtime = DB::table('our_overtime')
            ->where('date', '=', date('Y-m-d', strtotime($date)))
            ->where('our_lines_id', '=', $our_lines_id)
            ->first();
        if ($overtime == null) {
            return false;
        }

        $hours = substr($overtime->start_hour, 0, 2);
        $minutes = substr($overtime->start_hour, 3, 2);

        $start_date = date('Y-m-d', strtotime($date));
        $start_date = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($start_date)));

        $hours = substr($overtime->end_hour, 0, 2);
        $minutes = substr($overtime->end_hour, 3, 2);
        $days = 0;
        if ($hours == 0 && $minutes == 0) {
            $days = 1;
        }

        $end_date = date('Y-m-d', strtotime($date));
        $end_date = date('Y-m-d H:i:s', strtotime('+'.$days.'day +'.$hours.' hour +'.$minutes.' minutes', strtotime($end_date)));

        // Log::info('endOvertime');
        // Log::info(date('H:i:s', strtotime($date)));
        // Log::info(date('H:i:s', strtotime($start_date)));
        if (date('H:i:s', strtotime($date)) <= date('H:i:s', strtotime($start_date))) {
            $hours = substr($overtime->start_hour, 0, 2);
            $minutes = substr($overtime->start_hour, 3, 2);
            $start_date = date('Y-m-d', strtotime($date));
            $start_date = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($start_date)));
            return true;
        } elseif (date('H:i:s', strtotime($date)) > date('H:i:s', strtotime($start_date)) && date('H:i:s', strtotime($date)) < date('H:i:s', strtotime($end_date))) {
            $time = date('H:i:s', strtotime($date));
            $hours = substr($time, 0, 2);
            $minutes = substr($time, 3, 2);
            $start_date = date('Y-m-d', strtotime($date));
            $start_date = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($start_date)));
            return true;
        } else {
            return false;
        }
        
        // if (date('H:i:s', strtotime($date)) >= date('H:i:s', strtotime($end_date))) {
        // 	// Log::info("false");
        // }
    }

    public static function totalSeconds($working_time, $start_date, $end_date)
    {
        $total = self::diffSeconds($start_date, $end_date);
        // Log::info('total');
        // Log::info($total);
        // Log::info(self::breakTime($working_time, $start_date, $end_date));
        return $total - self::breakTime($working_time, $start_date, $end_date);
    }

    public static function diffSeconds($start_date, $end_date)
    {
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $interval = $start->diff($end);

        $days = $interval->format('%d');
        $hours = $interval->format('%h');
        $mins = $interval->format('%i');
        $secs = $interval->format('%s');

        return ($days * 24 * 60 * 60) + ($hours * 60 * 60) + ($mins * 60) + $secs;
    }

    public static function breakTime($working_time, $start_date, $end_date)
    {
        // Log::info('=====');
        // Log::info($working_time->weekday);
        // Log::info($working_time->our_shifts_id);
        // Log::info($start_date);
        // Log::info($end_date);


        $seconds = self::breakDetailTime($working_time->break_start1, $working_time->break_end1, $start_date, $end_date);
        $totalSeconds = $seconds;
        $end_date = date('Y-m-d H:i:s', strtotime('+'.$seconds.' seconds', strtotime($end_date)));
        // Log::info('1 '.$seconds);
        // Log::info($working_time->break_start1);
        // Log::info($working_time->break_end1);

        $seconds = self::breakDetailTime($working_time->break_start2, $working_time->break_end2, $start_date, $end_date);
        $totalSeconds += $seconds;
        $end_date = date('Y-m-d H:i:s', strtotime('+'.$seconds.' seconds', strtotime($end_date)));
        // Log::info('2 '.$seconds);
        // Log::info($working_time->break_start2);
        // Log::info($working_time->break_end2);

        $seconds = self::breakDetailTime($working_time->break_start3, $working_time->break_end3, $start_date, $end_date);
        $totalSeconds += $seconds;
        $end_date = date('Y-m-d H:i:s', strtotime('+'.$seconds.' seconds', strtotime($end_date)));
        // Log::info('3 '.$seconds);
        // Log::info($working_time->break_start3);
        // Log::info($working_time->break_end3);

        $seconds = self::breakDetailTime($working_time->break_start4, $working_time->break_end4, $start_date, $end_date);
        $totalSeconds += $seconds;
        $end_date = date('Y-m-d H:i:s', strtotime('+'.$seconds.' seconds', strtotime($end_date)));
        // Log::info('4 '.$seconds);
        // Log::info($working_time->break_start4);
        // Log::info($working_time->break_end4);

        $seconds = self::breakDetailTime($working_time->break_start5, $working_time->break_end5, $start_date, $end_date);
        $totalSeconds += $seconds;
        $end_date = date('Y-m-d H:i:s', strtotime('+'.$seconds.' seconds', strtotime($end_date)));
        // Log::info('5 '.$seconds);
        // Log::info($working_time->break_start5);
        // Log::info($working_time->break_end5);

        $seconds = self::breakDetailTime($working_time->break_start6, $working_time->break_end6, $start_date, $end_date);
        $totalSeconds += $seconds;
        $end_date = date('Y-m-d H:i:s', strtotime('+'.$seconds.' seconds', strtotime($end_date)));
        // Log::info('6 '.$seconds);
        // Log::info($working_time->break_start6);
        // Log::info($working_time->break_end6);
        
        // Log::info($totalSeconds);
 
        return $totalSeconds;
    }

    public static function breakDetailTime($break_start, $break_end, $start_date, $end_date)
    {
        if ($break_start == null || $break_end == null) {
            return 0;
        }

        $hours = substr($break_start, 0, 2);
        $minutes = substr($break_start, 3, 2);
        $break_start = date('Y-m-d', strtotime($start_date));
        $break_start = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($break_start)));

        $hours = substr($break_end, 0, 2);
        $minutes = substr($break_end, 3, 2);
        $break_end = date('Y-m-d', strtotime($start_date));
        $break_end = date('Y-m-d H:i:s', strtotime('+'.$hours.' hour +'.$minutes.' minutes', strtotime($break_end)));

        if ($end_date <= $break_start || $start_date >= $break_end) {
            return 0;
        }

        // Log::info('zzzz'.$end_date);
        // Log::info('zzzz'.$start_date);
        // Log::info('zzzz'.$break_start);
        // Log::info('zzzz'.$break_end);

        return self::diffSeconds($break_start, $break_end);
    }

    public static function savePlans($id, $start_date, $end_date, $working_time)
    {
        if ($id == null || $id == 0) {
            return;
        }

        $order = DB::table('our_bookings_detail')->where('id', '=', $id)->first();
        if ($order == null) {
            return;
        }

        $diff = self::diffSeconds($start_date, $end_date);
        $diff = $diff - self::breakTime($working_time, $start_date, $end_date);
        $diff = $diff / 3600;
        // Log::info('savePlans');
        // Log::info($diff);

        $qty = $diff * $order->speed;

        $data = [
            'created_at' => date('Y-m-d H:i:s'),
            'our_bookings_detail_id' => $id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'speed' => $order->speed,
            'work_hour' => $diff,
            'qty' => $qty
        ];
        DB::table('our_plans')->insert($data);
    }
    
    public static function GetApi($url)
    {
        $client = new \GuzzleHttp\Client();
        $request = $client->get($url);
        // $response = $request->getBody();
        return $request->json();
    }
}
