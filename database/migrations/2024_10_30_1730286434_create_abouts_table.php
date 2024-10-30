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
        Schema::create('abouts', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 255);
			$table->string('gmail', 255);
			$table->string('github', 255);
			$table->string('skype', 255);
			$table->string('language', 255);
			$table->string('years_experience', 255);
			$table->string('handled_project', 255);
			$table->string('open_source', 255);
			$table->string('awards', 255);
			$table->string('description', 255);
			
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
        Schema::dropIfExists('abouts');
    }
};
