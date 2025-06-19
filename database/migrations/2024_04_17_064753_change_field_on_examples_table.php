<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('examples', function (Blueprint $table) {
            $table->dropColumn('age');
            $table->dropColumn('birth_of_date');
            $table->dropColumn('birth_of_date_string');
            $table->string('nik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('examples', function (Blueprint $table) {
            $table->integer('age')->after('name');
            $table->date('birth_of_date')->after('age');
            $table->string('birth_of_date_string')->after('birth_of_date');
            $table->dropColumn('nik');
        });
    }
};
