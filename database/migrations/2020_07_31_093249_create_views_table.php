<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('views', static function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->unsignedBigInteger('bucket_id');
            $table->string('type');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedTinyInteger('quality')->nullable();
            $table->string('color')->nullable();
            $table->string('position')->nullable();
            $table->boolean('upsize')->default(1);
            $table->boolean('strict')->default(0);
            $table->boolean('optimize')->default(0);
            $table->boolean('webp')->default(0);
            $table->timestamps();

            $table->unique(['bucket_id', 'name']);

            $table->foreign('bucket_id')
                ->references('id')->on('buckets')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('views');
    }
}
