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
        // isi user lama
        $firstUser = DB::table('users')->first();

        if ($firstUser) {
            DB::table('wallets')
                ->whereNull('user_id')
                ->update([
                    'user_id' => $firstUser->id
                ]);
        }

        Schema::table('wallets', function (Blueprint $table) {

            // tambah foreign key saja
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
