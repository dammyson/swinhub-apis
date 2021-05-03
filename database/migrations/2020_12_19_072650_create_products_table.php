<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id')->index();
            $table->uuid('user_id')->index();
            $table->string('name')->default('');
            $table->string('logo')->default('');
            $table->string('description')->default('');
            $table->string('tech_description')->default('');
            $table->string('url')->default('');
            $table->string('category')->default('');
            $table->string('sub_category')->default('');
            $table->string('is_trial')->default('');
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
        Schema::dropIfExists('products');
    }
}
