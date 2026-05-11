<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login', function (Blueprint $table) {
            $table->id('id_login');
            $table->foreignId('id_petani')->nullable()->constrained('petani', 'id_petani');
            $table->foreignId('id_pengelola')->nullable()->constrained('pengelola', 'id_pengelola');
            $table->foreignId('id_admin')->nullable()->constrained('admin', 'id_admin');
            $table->string('username', 50)->unique();
            $table->string('password', 255); // akan di-hash Argon2 di seeder
            $table->enum('role', ['petani', 'pengelola', 'admin']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login');
    }
};