<?php

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
        Schema::create('parsonal__infos', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
			$table->string('designation', 255);
			$table->string('residence', 255);
			$table->string('city', 255);
			$table->string('age', 255);
			$table->string('image', 255)->nullable();

            $table->enum('status',['Active','Inactive','Deleted'])->default('Active');
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parsonal__infos');
    }
};
