<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSiswaIdFromOrangtuasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orangtuas', function (Blueprint $table) {
            if (Schema::hasColumn('orangtuas', 'siswa_id')) {
                // First remove any foreign key constraints
                $table->dropForeign(['siswa_id']);
                $table->dropColumn('siswa_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orangtuas', function (Blueprint $table) {
            if (!Schema::hasColumn('orangtuas', 'siswa_id')) {
                $table->foreignId('siswa_id')->nullable()->constrained('siswas')->nullOnDelete();
            }
        });
    }
}
