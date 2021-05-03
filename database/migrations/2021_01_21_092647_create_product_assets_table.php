<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id')->index();
            $table->string('category')->default('');
            $table->string('type')->default('');
            $table->string('file')->default('');
            $table->string('name')->default('');
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
        Schema::dropIfExists('product_assets');
    }
}
