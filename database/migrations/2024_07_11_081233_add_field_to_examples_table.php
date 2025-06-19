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
            $table->string('dob');
            $table->string('citizen');
            $table->text('hobbies');
            $table->boolean('married_status')->default(false);
            $table->longText('profile_picture');
            $table->longText('supporting_document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('examples', function (Blueprint $table) {
            $table->dropColumn('dob');
            $table->dropColumn('citizen');
            $table->dropColumn('hobbies');
            $table->dropColumn('married_status');
            $table->dropColumn('profile_picture');
            $table->dropColumn('supporting_document');
        });
    }
};
