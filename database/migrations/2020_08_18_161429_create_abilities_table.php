<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abilities', static function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->unique();
            $table->timestamps();
        });

        $abilities = [
            'bucket:drop' => 'Bucket delete rights',
            'bucket:index' => 'Rights to show all buckets',
            'bucket:show' => 'Rights to show one bucket at a time',
            'bucket:store' => 'Rights to create a new bucket',

            'view:drop' => 'View delete rights',
            'view:index' => 'Rights to show all views',
            'view:show' => 'Rights to show one view at a time',
            'view:store' => 'Rights to create a new view',

            'file:drop' => 'File deletion rights',
            'file:edit' => 'File editing rights',
            'file:index' => 'File List View Rights',
            'file:invite' => 'File access rights for a limited number of days',
            'file:show' => 'Rights to display data by file',
            'file:store' => 'File upload rights',
        ];

        $inserts = [];
        foreach ($abilities as $ability => $description) {
            $inserts[] = [
                'name' => $ability,
                'description' => $description,
                'created_at' => now(),
            ];
        }

        \App\Models\Ability::insert($inserts);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('abilities');
    }
}
