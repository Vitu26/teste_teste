<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('archives', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 300);
            $table->text('description');
            $table->string('type');
            $table->string('path', 300);
            $table->string('name', 300);
            $table->string('extension');
            $table->softDeletes();
            $table->timestamps();

            $table->unsignedBigInteger('pet_id'); 
            $table->foreign('pet_id')->references('id')->on('pets')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};
