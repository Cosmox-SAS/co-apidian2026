<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('type_overtime_surcharges')->where('id', 4)->update(['percentage' => 105.00]);
        DB::table('type_overtime_surcharges')->where('id', 5)->update(['percentage' => 80.00]);
        DB::table('type_overtime_surcharges')->where('id', 6)->update(['percentage' => 155.00]);
        DB::table('type_overtime_surcharges')->where('id', 7)->update(['percentage' => 115.00]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('type_overtime_surcharges')->where('id', 4)->update(['percentage' => 100.00]);
        DB::table('type_overtime_surcharges')->where('id', 5)->update(['percentage' => 75.00]);
        DB::table('type_overtime_surcharges')->where('id', 6)->update(['percentage' => 150.00]);
        DB::table('type_overtime_surcharges')->where('id', 7)->update(['percentage' => 110.00]);
    }
};
