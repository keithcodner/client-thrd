<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoreSiteConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_site_config', function (Blueprint $table) {
            $table->integer('cfg_id')->autoIncrement();
            $table->string('cfg_option_name', 255)->nullable()->default(null);
            $table->text('cfg_status')->nullable()->default(null);
            $table->text('cfg_value_1')->nullable()->default(null);
            $table->text('cfg_value_2')->nullable()->default(null);
            $table->text('cfg_value_3')->nullable()->default(null);
            $table->text('cfg_value_4')->nullable()->default(null);
            $table->text('cfg_value_5')->nullable()->default(null);
            $table->text('cfg_value_6')->nullable()->default(null);
            $table->text('cfg_value_7')->nullable()->default(null);

            // Indexes
            //$table->primary(['cfg_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('core_site_config');
    }
}
