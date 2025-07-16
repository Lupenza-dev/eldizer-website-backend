<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->string('subtitle')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('badge')->nullable();
            $table->string('features')->nullable();
            $table->integer('order')->nullable();
            $table->string('content')->change()->nullable();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropColumn('subtitle');
            $table->dropColumn('button_text');
            $table->dropColumn('button_url');
            $table->dropColumn('badge');
            $table->dropColumn('features');
            $table->dropColumn('order');
        });
    }
};
