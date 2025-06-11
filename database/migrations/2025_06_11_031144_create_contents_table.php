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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();

            $table->string('category_name');
            $table->foreign('category_name')->references('name')->on('categories')->onUpdate('cascade')->onDelete('cascade');

            $table->string('name', length: 20)->unique();
            $table->text('description');
            $table->string('image')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('number_of_episodes')->nullable();
            $table->string('genre')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
