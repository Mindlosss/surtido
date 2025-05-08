<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->string('area');
            $table->string('asunto');
            $table->text('descripcion');
            $table->enum('prioridad', ['Alta','Media','Baja'])->default('Media');
            $table->enum('estado', ['Abierto','En Progreso','Cerrado'])->default('Abierto');
            $table->string('asignado_a')->nullable(); 
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
