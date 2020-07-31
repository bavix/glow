<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();

            // route with bucket
            $table->string('route')->index();

            // filetype: image, file
            $table->string('type')->index();

            // public, private
            $table->boolean('visibility')->index()->default(1);

            // states
            $table->boolean('extracted')->index()->default(0); // metadata
            $table->dateTime('extracted_at')->nullable();
            $table->boolean('processed')->index()->default(0); // generate views
            $table->dateTime('processed_at')->nullable();
            $table->boolean('optimized')->index()->default(0); // optimize all images (include thumbs)
            $table->dateTime('optimized_at')->nullable();

            // {width, height, size, mime, ...}
            $table->json('extra')->nullable();

            // ['xs', 'm', 'xl']
            $table->json('thumbs')->nullable();

            // references
            $table->unsignedBigInteger('bucket_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->unique(['bucket_id', 'route']);

            $table->foreign('bucket_id')
                ->references('id')->on('buckets')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
