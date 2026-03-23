<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateTaxNameForZZCodeId15 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('taxes')
            ->where('id', 15)
            ->where(function ($query) {
                $query->where('code', 'ZZ')
                    ->orWhere('code', "ZZ\r");
            })
            ->update([
                'name' => 'No aplica *',
                'code' => 'ZZ',
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('taxes')
            ->where('id', 15)
            ->where(function ($query) {
                $query->where('code', 'ZZ')
                    ->orWhere('code', "ZZ\r");
            })
            ->update([
                'name' => 'No aplica',
            ]);
    }
}
