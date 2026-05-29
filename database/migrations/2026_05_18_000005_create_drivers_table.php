<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 45);
            $table->date('birth_date')->nullable();
            $table->string('email', 50)->unique();
            $table->string('phone', 40)->nullable();
            $table->string('avatar')->nullable();
            $table->foreignId('vehicle_id')->nullable()->unique()->constrained('vehicles')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
