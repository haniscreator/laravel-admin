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
        Schema::create('albums', function (Blueprint $table) {
            $table->id(); // auto-increment primary key
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('keyword')->nullable();
            $table->string('image')->nullable();
            $table->string('location')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamp('created_date')->useCurrent();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps(); // adds created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
