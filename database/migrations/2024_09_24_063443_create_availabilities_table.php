<?php

use App\Models\Restaurant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Restaurant::class)->default(0);
            $table->string('day')->nullable();
            $table->string('meal_type')->nullable();
            $table->string('time_slot')->nullable(); 
            $table->boolean('open')->default(true); 
            $table->integer('available_seats')->default(0);
            $table->time('opening_time');
            $table->time('closing_time'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('availabilities');
    }
};
