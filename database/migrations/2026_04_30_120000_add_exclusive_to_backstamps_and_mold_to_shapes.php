<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    public function up(): void{
        Schema::table('backstamps', function (Blueprint $table){
            $table->boolean('exclusive')->default(false)->after('air_dry');
        });

        Schema::table('shapes', function(Blueprint $table){
            $table->boolean('mold')->default(false)->after('body');
        });
    }

    public function down(): void{
        Schema::table('backstamps', function (Blueprint $table){
            $table->dropColumn('exclusive');
        });

        Schema::table('shapes', function(Blueprint $table){
            $table->dropColumn('mold');
        });
    }
}
?>